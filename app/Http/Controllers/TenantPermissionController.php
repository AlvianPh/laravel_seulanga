<?php

namespace App\Http\Controllers;

use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use App\Http\Requests\ReviewTenantPermissionRequest;
use App\Models\TenantPermission;
use App\Services\TenantPermissionService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantPermissionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TenantPermissionService $service
    ) {}

    /**
     * Menampilkan daftar seluruh permohonan izin penghuni untuk staf.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', TenantPermission::class);

        $query = TenantPermission::with(['tenant', 'contract.room', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('tenant', function ($t) use ($search) {
                        $t->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.room', function ($r) use ($search) {
                        $r->where('room_number', 'like', "%{$search}%");
                    });
            });
        }

        $permissions = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'total' => TenantPermission::count(),
            'pending' => TenantPermission::where('status', PermissionStatus::Pending)->count(),
            'approved' => TenantPermission::where('status', PermissionStatus::Approved)->count(),
            'rejected' => TenantPermission::where('status', PermissionStatus::Rejected)->count(),
            'cancelled' => TenantPermission::where('status', PermissionStatus::Cancelled)->count(),
        ];

        $statuses = PermissionStatus::cases();
        $types = PermissionType::cases();

        return view('permissions.index', compact('permissions', 'counts', 'statuses', 'types'));
    }

    /**
     * Menampilkan detail permohonan izin untuk staf.
     */
    public function show(TenantPermission $permission): View
    {
        $this->authorize('view', $permission);

        $permission->load(['tenant.user', 'contract.room.roomType', 'reviewer']);

        return view('permissions.show', compact('permission'));
    }

    /**
     * Memberikan keputusan review (Approve / Reject) terhadap permohonan izin.
     */
    public function review(ReviewTenantPermissionRequest $request, TenantPermission $permission): RedirectResponse
    {
        $action = $request->input('action');
        $note = $request->input('review_note');

        $this->authorize($action === 'approve' ? 'approve' : 'reject', $permission);

        try {
            if ($action === 'approve') {
                $this->service->approve($permission, $note, $request->user());
                $message = 'Permohonan izin berhasil disetujui.';
            } else {
                $this->service->reject($permission, (string) $note, $request->user());
                $message = 'Permohonan izin berhasil ditolak.';
            }

            return redirect()->route('permissions.show', $permission)
                ->with('success', $message);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
