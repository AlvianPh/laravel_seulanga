<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\AcceptAgreementRequest;
use App\Models\BankAccount;
use App\Models\Contract;
use App\Models\Setting;
use App\Services\ContractService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private ContractService $contractService) {}

    /**
     * Menampilkan halaman Kontrak Saya & Progress Onboarding Penghuni.
     */
    public function index(Request $request): View
    {
        $tenant = $request->user()->tenant;
        $contract = null;
        $bankAccounts = [];
        $setting = Setting::getInstance();

        if ($tenant) {
            $contract = $tenant->currentContract();

            if ($contract) {
                $contract->load(['room.roomType', 'room.facilities', 'invoices.payments.paymentMethod', 'agreementAcceptedBy']);
            }

            $bankAccounts = BankAccount::where('is_active', true)->get();
        }

        return view('portal.contract.index', compact('tenant', 'contract', 'bankAccounts', 'setting'));
    }

    /**
     * Memproses persetujuan digital Tata Tertib & Perjanjian Sewa oleh Tenant.
     */
    public function acceptAgreement(AcceptAgreementRequest $request, Contract $contract): RedirectResponse
    {
        $this->authorize('acceptAgreement', $contract);

        try {
            $this->contractService->acceptAgreement($contract, $request->user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('portal.contract.index')
            ->with('status', 'Tata tertib dan ketentuan sewa kost berhasil disetujui. Silakan lanjutkan pembayaran tagihan awal.');
    }
}
