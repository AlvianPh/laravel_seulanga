<?php

namespace App\Http\Controllers\Portal;

use App\Enums\StatusTagihan;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Menampilkan daftar tagihan milik penghuni yang sedang login.
     */
    public function index(Request $request): View
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return view('portal.invoices.index', [
                'tenant' => null,
                'invoices' => collect(),
                'statuses' => StatusTagihan::cases(),
                'totalInvoices' => 0,
                'unpaidCount' => 0,
                'totalUnpaidAmount' => 0,
            ]);
        }

        $query = $tenant->invoices()
            ->with(['room.roomType', 'contract', 'payments'])
            ->latest('due_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(10)->withQueryString();

        // Hitung ringkasan status
        $allInvoices = $tenant->invoices()->get();
        $totalInvoices = $allInvoices->count();
        $unpaidInvoices = $allInvoices->filter(fn (Invoice $inv) => ! $inv->isPaid() && $inv->status !== StatusTagihan::Cancelled);
        $unpaidCount = $unpaidInvoices->count();
        $totalUnpaidAmount = $unpaidInvoices->sum(fn (Invoice $inv) => $inv->remainingBalance());

        return view('portal.invoices.index', [
            'tenant' => $tenant,
            'invoices' => $invoices,
            'statuses' => StatusTagihan::cases(),
            'totalInvoices' => $totalInvoices,
            'unpaidCount' => $unpaidCount,
            'totalUnpaidAmount' => $totalUnpaidAmount,
        ]);
    }

    /**
     * Menampilkan rincian tagihan beserta histori pembayaran dan form submit pembayaran.
     */
    public function show(Request $request, Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load([
            'room.roomType',
            'contract',
            'payments.paymentMethod',
            'payments.verifier',
        ]);

        $paymentMethods = PaymentMethod::orderBy('name')->get();
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('portal.invoices.show', compact('invoice', 'paymentMethods', 'bankAccounts'));
    }
}
