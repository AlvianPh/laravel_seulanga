<?php

namespace App\Http\Controllers;

use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Http\Requests\ContractRequest;
use App\Models\Contract;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantApplication;
use App\Services\ContractService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContractController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private ContractService $contractService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Contract::class);

        $query = Contract::with(['tenant', 'room']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('room', function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%");
            });
        }

        $contracts = $query->latest('start_date')->paginate(10)->withQueryString();
        $statuses = StatusKontrak::cases();

        return view('contracts.index', compact('contracts', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Contract::class);

        // Hanya kamar yang available yang bisa dipilih untuk sewa baru
        $rooms = Room::where('status', StatusKamar::Available)->orderBy('room_number')->get();
        $tenants = Tenant::orderBy('name')->get();

        $selectedTenantId = $request->query('tenant_id');
        $selectedRoomId = $request->query('room_id');
        $applicationId = $request->query('application_id');

        if ($applicationId) {
            $application = TenantApplication::find($applicationId);
            if ($application) {
                $selectedTenantId = $application->tenant_id;
                $selectedRoomId = $application->room_id;
            }
        }

        return view('contracts.create', compact('rooms', 'tenants', 'selectedTenantId', 'selectedRoomId', 'applicationId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContractRequest $request): RedirectResponse
    {
        $this->authorize('create', Contract::class);

        $asDraft = $request->boolean('is_draft');

        try {
            $this->contractService->createContract($request->validated(), $request->user()->id, $asDraft);
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }

        $msg = $asDraft
            ? 'Draft kontrak sewa berhasil dibuat. Menunggu persetujuan tata tertib dan pembayaran awal dari penghuni.'
            : 'Kontrak baru berhasil dibuat dan kamar ditandai sebagai terisi (Occupied).';

        return redirect()->route('contracts.index')->with('success', $msg);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract): View
    {
        $this->authorize('view', $contract);
        $contract->load(['tenant', 'room', 'creator', 'application', 'agreementAcceptedBy', 'invoices.payments']);

        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract): View
    {
        $this->authorize('update', $contract);
        $rooms = Room::orderBy('room_number')->get();
        $tenants = Tenant::orderBy('name')->get();

        return view('contracts.edit', compact('contract', 'rooms', 'tenants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContractRequest $request, Contract $contract): RedirectResponse
    {
        $this->authorize('update', $contract);

        $contract->update($request->validated());

        return redirect()->route('contracts.index')
            ->with('success', 'Data kontrak berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract): RedirectResponse
    {
        $this->authorize('delete', $contract);

        // Jika kontrak yang dihapus statusnya active, bebaskan kamar dulu
        if ($contract->status === StatusKontrak::Active) {
            $room = $contract->room;
            if ($room && $room->status === StatusKamar::Occupied) {
                $room->update(['status' => StatusKamar::Available]);
            }
        }

        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'Data kontrak berhasil dihapus.');
    }

    /**
     * Mengaktifkan contract draft setelah onboarding terpenuhi.
     */
    public function activate(Contract $contract): RedirectResponse
    {
        $this->authorize('activate', $contract);

        try {
            $this->contractService->activateContract($contract, Auth::id());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Kontrak sewa berhasil diaktifkan. Kamar kini berstatus Terisi (Occupied) dan penghuni aktif.');
    }

    /**
     * Renew (Perpanjang) kontrak.
     */
    public function renew(Request $request, Contract $contract): RedirectResponse
    {
        $this->authorize('update', $contract);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_price' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->contractService->renewContract($contract, $request->all(), $request->user()->id);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('contracts.index')
            ->with('success', 'Kontrak berhasil diperpanjang.');
    }

    /**
     * Terminate (Akhiri) kontrak.
     */
    public function terminate(Contract $contract): RedirectResponse
    {
        $this->authorize('update', $contract);

        try {
            $this->contractService->terminateContract($contract);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Kontrak diakhiri paksa dan kamar telah dikosongkan.');
    }
}
