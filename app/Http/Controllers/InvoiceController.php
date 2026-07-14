<?php

namespace App\Http\Controllers;

use App\Enums\StatusTagihan;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Services\GenerateInvoiceService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private GenerateInvoiceService $invoiceService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $query = Invoice::with(['tenant', 'room', 'contract']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $invoices = $query->latest('year')->latest('month')->paginate(10)->withQueryString();
        $statuses = StatusTagihan::cases();

        return view('invoices.index', compact('invoices', 'statuses'));
    }

    /**
     * Store a newly created resource in storage (Not used for invoice, we generate them).
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);
        $invoice->load(['tenant', 'room', 'contract', 'payments']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice): View
    {
        $this->authorize('update', $invoice);
        $invoice->load(['tenant', 'room']);
        $statuses = StatusTagihan::cases();

        return view('invoices.edit', compact('invoice', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);

        $validated = $request->validated();

        $invoice->fill($validated);
        
        // Recalculate total amount from fees
        $invoice->total_amount = $invoice->calculateTotal();
        $invoice->save();

        return redirect()->route('invoices.index')
            ->with('success', "Tagihan #{$invoice->id} berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }

    /**
     * Generate tagihan manual via web interface.
     */
    public function generateManual(Request $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $count = $this->invoiceService->generateMonthlyInvoices((int)$month, (int)$year);

        if ($count > 0) {
            return back()->with('success', "Berhasil men-generate {$count} tagihan baru untuk {$month}/{$year}.");
        }

        return back()->with('info', "Tidak ada tagihan baru yang perlu dibuat (sudah di-generate atau tidak ada kontrak aktif).");
    }
}
