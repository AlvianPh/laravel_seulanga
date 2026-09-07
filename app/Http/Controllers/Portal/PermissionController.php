<?php

namespace App\Http\Controllers\Portal;

use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreTenantPermissionRequest;
use App\Models\TenantPermission;
use App\Services\TenantPermissionService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TenantPermissionService $service
    ) {}

    /**
     * Menampilkan daftar permohonan izin milik penghuni.
     */
    public function index(Request $request): View
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return view('portal.permissions.index', [
                'tenant' => null,
                'permissions' => collect(),
                'activeContract' => null,
                'statuses' => PermissionStatus::cases(),
            ]);
        }

        $activeContract = $tenant->activeContract();

        $query = $tenant->permissions()
            ->with(['contract.room', 'reviewer'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $permissions = $query->paginate(10)->withQueryString();
        $statuses = PermissionStatus::cases();
        $types = PermissionType::cases();

        return view('portal.permissions.index', compact('tenant', 'permissions', 'activeContract', 'statuses', 'types'));
    }

    /**
     * Form pengajuan permohonan izin baru.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant || ! $tenant->activeContract()) {
            return redirect()->route('portal.permissions.index')
                ->with('error', 'Hanya penghuni dengan kontrak kamar aktif yang dapat mengajukan permohonan izin.');
        }

        $activeContract = $tenant->activeContract();
        $types = PermissionType::cases();

        return view('portal.permissions.create', compact('tenant', 'activeContract', 'types'));
    }

    /**
     * Menyimpan permohonan izin baru dari penghuni.
     */
    public function store(StoreTenantPermissionRequest $request): RedirectResponse
    {
        $tenant = $request->user()->tenant;

        try {
            $permission = $this->service->create(
                tenant: $tenant,
                data: $request->validated(),
                actor: $request->user()
            );

            return redirect()->route('portal.permissions.show', $permission)
                ->with('success', 'Permohonan izin berhasil diajukan dan sedang menunggu review pengelola.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan detail permohonan izin penghuni.
     */
    public function show(TenantPermission $permission): View
    {
        $this->authorize('view', $permission);

        $permission->load(['contract.room', 'reviewer', 'tenant']);

        return view('portal.permissions.show', compact('permission'));
    }

    /**
     * Membatalkan permohonan izin oleh penghuni sendiri.
     */
    public function cancel(TenantPermission $permission): RedirectResponse
    {
        $this->authorize('cancel', $permission);

        try {
            $this->service->cancel($permission, request()->user());

            return redirect()->route('portal.permissions.show', $permission)
                ->with('success', 'Permohonan izin berhasil dibatalkan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
