<?php

namespace App\Http\Controllers\Portal;

use App\Enums\StatusPembayaran;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan riwayat pembayaran milik penghuni yang sedang login.
     */
    public function index(Request $request): View
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return view('portal.payments.index', [
                'tenant' => null,
                'payments' => collect(),
                'statuses' => StatusPembayaran::cases(),
            ]);
        }

        $query = $tenant->payments()
            ->with(['invoice.room', 'paymentMethod', 'verifier'])
            ->latest('payment_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(10)->withQueryString();
        $statuses = StatusPembayaran::cases();

        return view('portal.payments.index', compact('tenant', 'payments', 'statuses'));
    }

    /**
     * Menampilkan rincian transaksi pembayaran.
     */
    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load(['invoice.room', 'paymentMethod', 'verifier', 'tenant']);

        return view('portal.payments.show', compact('payment'));
    }

    /**
     * Menyimpan pembayaran baru dengan upload bukti transfer oleh penghuni.
     */
    public function store(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('pay', $invoice);

        $validated = $request->validated();
        $tenant = $request->user()->tenant;

        // Validasi otoritatif server: nominal tidak boleh melebihi sisa kewajiban
        $remainingBalance = $invoice->remainingBalance();
        if ($validated['amount'] <= 0 || $validated['amount'] > $remainingBalance) {
            return back()->withInput()->withErrors([
                'amount' => 'Nominal pembayaran tidak boleh melebihi sisa tagihan (Rp '.number_format($remainingBalance, 0, ',', '.').').',
            ]);
        }

        DB::transaction(function () use ($request, $validated, $invoice, $tenant) {
            $proofPath = $request->file('proof_photo')->store('payments', 'public');

            Payment::create([
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenant->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_method_id' => $validated['payment_method_id'],
                'status' => StatusPembayaran::Pending,
                'proof_path' => $proofPath,
                'notes' => $validated['notes'] ?? null,
                'verified_by' => null,
            ]);
        });

        return redirect()->route('portal.invoices.show', $invoice)
            ->with('status', 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi pengelola kost.');
    }

    /**
     * Menampilkan kuitansi resmi pembayaran (Printable View).
     */
    public function receipt(Payment $payment): View
    {
        $this->authorize('receipt', $payment);

        $payment->load([
            'invoice.room.roomType',
            'tenant',
            'paymentMethod',
            'verifier',
        ]);

        $setting = Setting::getInstance();

        return view('portal.payments.receipt', compact('payment', 'setting'));
    }
}
