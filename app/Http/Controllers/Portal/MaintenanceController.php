<?php

namespace App\Http\Controllers\Portal;

use App\Enums\KategoriMaintenance;
use App\Enums\PrioritasMaintenance;
use App\Enums\StatusMaintenance;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreMaintenanceRequest;
use App\Models\MaintenanceRequest;
use App\Services\MaintenanceRequestService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected MaintenanceRequestService $service
    ) {}

    /**
     * Menampilkan daftar riwayat perbaikan kamar milik penghuni.
     */
    public function index(Request $request): View
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return view('portal.maintenance.index', [
                'tenant' => null,
                'requests' => collect(),
                'activeContract' => null,
                'statuses' => StatusMaintenance::cases(),
            ]);
        }

        $activeContract = $tenant->activeContract();

        $query = $tenant->maintenanceRequests()
            ->with(['room', 'expenses'])
            ->latest('reported_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(10)->withQueryString();
        $statuses = StatusMaintenance::cases();

        return view('portal.maintenance.index', compact('tenant', 'requests', 'activeContract', 'statuses'));
    }

    /**
     * Form pengajuan perbaikan kamar baru.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant || ! $tenant->activeContract()) {
            return redirect()->route('portal.maintenance.index')
                ->with('error', 'Hanya penghuni dengan kamar aktif yang dapat mengajukan keluhan perbaikan.');
        }

        $activeContract = $tenant->activeContract();
        $categories = KategoriMaintenance::cases();
        $priorities = PrioritasMaintenance::cases();

        return view('portal.maintenance.create', compact('tenant', 'activeContract', 'categories', 'priorities'));
    }

    /**
     * Simpan pengajuan perbaikan kamar.
     */
    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $tenant = $request->user()->tenant;

        try {
            $maintenanceRequest = $this->service->createRequest(
                tenant: $tenant,
                data: $request->validated(),
                photo: $request->file('photo'),
                actor: $request->user()
            );

            return redirect()->route('portal.maintenance.show', $maintenanceRequest)
                ->with('success', "Permintaan perbaikan berhasil dibuat dengan nomor tiket {$maintenanceRequest->ticket_number}. Tim pengelola akan segera memproses.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan detail pengajuan perbaikan kamar.
     */
    public function show(MaintenanceRequest $maintenance): View
    {
        $this->authorize('view', $maintenance);

        $maintenance->load(['room', 'expenses', 'creator', 'updater']);

        return view('portal.maintenance.show', compact('maintenance'));
    }
}
