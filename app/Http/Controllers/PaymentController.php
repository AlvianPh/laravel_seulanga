<?php

namespace App\Http\Controllers;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPembayaran;
use App\Http\Requests\PaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
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

        $query = Payment::with(['invoice', 'tenant', 'verifier']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('invoice', function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%");
            });
        }

        $payments = $query->latest()->paginate(10)->withQueryString();
        $statuses = StatusPembayaran::cases();
        $methods = MetodePembayaran::cases();

        return view('payments.index', compact('payments', 'statuses', 'methods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Payment::class);

        // Hanya tagihan yang belum lunas (pending/overdue)
        $invoices = Invoice::with(['tenant', 'room'])
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->get();
            
        $methods = MetodePembayaran::cases();

        return view('payments.create', compact('invoices', 'methods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request): RedirectResponse
    {
        $this->authorize('create', Payment::class);

        $validated = $request->validated();
        
        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $validated['tenant_id'] = $invoice->tenant_id;
        $validated['status'] = StatusPembayaran::Pending;

        if ($request->hasFile('proof_photo')) {
            $validated['proof_path'] = $request->file('proof_photo')->store('payments/proofs', 'public');
        }

        Payment::create($validated);

        return redirect()->route('payments.index')
            ->with('success', 'Pembayaran berhasil diinput. Menunggu verifikasi Owner.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);
        $payment->load(['invoice.room', 'tenant', 'verifier']);

        return view('payments.show', compact('payment'));
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
