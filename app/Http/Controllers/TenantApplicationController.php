<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewTenantApplicationRequest;
use App\Models\TenantApplication;
use App\Services\TenantApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use InvalidArgumentException;

/**
 * TenantApplicationController (Staff/Admin/Owner Area)
 *
 * Mengelola peninjauan, persetujuan, dan penolakan pengajuan sewa kamar oleh staf.
 */
class TenantApplicationController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengajuan sewa kamar.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TenantApplication::class);

        $query = TenantApplication::with(['tenant', 'room.roomType', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('tenant', fn ($t) => $t->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                    ->orWhereHas('room', fn ($r) => $r->where('room_number', 'like', "%{$search}%"));
            });
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        return view('tenant_applications.index', compact('applications'));
    }

    /**
     * Tampilkan detail permohonan pengajuan sewa kamar.
     */
    public function show(TenantApplication $tenantApplication): View
    {
        Gate::authorize('view', $tenantApplication);

        $tenantApplication->load(['tenant', 'room.roomType', 'room.facilities', 'reviewer']);

        return view('tenant_applications.show', compact('tenantApplication'));
    }

    /**
     * Proses review pengajuan (Approve / Reject).
     */
    public function review(
        TenantApplication $tenantApplication,
        ReviewTenantApplicationRequest $request,
        TenantApplicationService $service
    ): RedirectResponse {
        $user = $request->user();
        $action = $request->validated('action');

        try {
            if ($action === 'approve') {
                Gate::authorize('approve', $tenantApplication);
                $service->approve($tenantApplication, $user->id);

                return redirect()->route('tenant-applications.show', $tenantApplication)
                    ->with('status', 'Pengajuan sewa kamar berhasil disetujui. Langkah selanjutnya: buat kontrak sewa untuk penghuni.');
            }

            if ($action === 'reject') {
                Gate::authorize('reject', $tenantApplication);
                $service->reject($tenantApplication, $user->id, $request->validated('rejection_reason'));

                return redirect()->route('tenant-applications.show', $tenantApplication)
                    ->with('status', 'Pengajuan sewa kamar telah ditolak.');
            }
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('tenant-applications.index');
    }
}
