<?php

namespace App\Http\Controllers;

use App\Enums\StatusPembayaran;
use App\Http\Requests\PaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\VerifyPaymentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private VerifyPaymentService $verifyService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['invoice.room', 'tenant', 'verifier', 'paymentMethod']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('invoice.room', function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%");
            });
        }

        $payments = $query->latest('payment_date')->paginate(10)->withQueryString();
        $statuses = StatusPembayaran::cases();
        $paymentMethods = \App\Models\PaymentMethod::orderBy('name')->get();

        return view('payments.index', compact('payments', 'statuses', 'paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Payment::class);

        $invoices = Invoice::with(['tenant', 'room'])
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->get();
            
        $paymentMethods = PaymentMethod::all();
        $selectedInvoiceId = $request->query('invoice_id');

        return view('payments.create', compact('invoices', 'paymentMethods', 'selectedInvoiceId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request): RedirectResponse
    {
        $this->authorize('create', Payment::class);

        $data = $request->validated();
        $invoice = Invoice::findOrFail($data['invoice_id']);
        
        $data['tenant_id'] = $invoice->tenant_id;
        $data['status']    = StatusPembayaran::Pending;

        if ($request->hasFile('proof_photo')) {
            $data['proof_path'] = $request->file('proof_photo')->store('payments', 'public');
        }

        Payment::create($data);

        return redirect()->route('payments.index')
                         ->with('success', 'Pembayaran berhasil dicatat dan menunggu verifikasi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);
        $payment->load(['invoice.room', 'tenant', 'verifier', 'paymentMethod']);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment): View
    {
        $this->authorize('update', $payment);

        $invoices = Invoice::with(['tenant', 'room'])
            ->whereIn('status', ['pending', 'overdue'])
            ->orWhere('id', $payment->invoice_id)
            ->orderBy('due_date')
            ->get();

        $paymentMethods = PaymentMethod::all();

        return view('payments.edit', compact('payment', 'invoices', 'paymentMethods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentRequest $request, Payment $payment): RedirectResponse
    {
        $this->authorize('update', $payment);

        $data = $request->validated();

        if ($request->hasFile('proof_photo')) {
            $data['proof_path'] = $request->file('proof_photo')->store('payments', 'public');
        }

        $payment->update($data);

        return redirect()->route('payments.index')->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $this->authorize('delete', $payment);

        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Pembayaran berhasil dihapus.');
    }

    /**
     * Menampilkan halaman khusus verifikasi (Hanya Owner).
     */
    public function verifyForm(Payment $payment): View
    {
        $this->authorize('verify', $payment);

        $payment->load(['invoice', 'tenant']);

        return view('payments.verify', compact('payment'));
    }

    /**
     * Memproses verifikasi (Terima / Tolak).
     */
    public function processVerification(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorize('verify', $payment);

        $request->validate([
            'action' => 'required|in:verify,reject',
            'notes'  => 'nullable|string|max:1000',
        ]);

        try {
            if ($request->action === 'verify') {
                $this->verifyService->verify($payment, $request->user()->id);
                $msg = 'Pembayaran berhasil diverifikasi. Status tagihan telah disesuaikan.';
            } else {
                $this->verifyService->reject($payment, $request->user()->id, $request->notes);
                $msg = 'Pembayaran ditolak.';
            }
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('payments.index')->with('success', $msg);
    }
}
