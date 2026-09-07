<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreTenantApplicationRequest;
use App\Models\TenantApplication;
use App\Services\TenantApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use InvalidArgumentException;

/**
 * TenantApplicationController (Tenant Portal)
 *
 * Mengelola pengajuan sewa kamar mandiri oleh tenant.
 */
class TenantApplicationController extends Controller
{
    /**
     * Tampilkan riwayat dan status pengajuan kamar milik tenant yang sedang login.
     */
    public function index(Request $request): View
    {
        $tenant = $request->user()->tenant;

        $applications = $tenant
            ? $tenant->applications()
                ->with(['room.roomType', 'reviewer'])
                ->latest()
                ->paginate(10)
            : collect();

        return view('portal.applications.index', compact('applications', 'tenant'));
    }

    /**
     * Kirim pengajuan sewa kamar baru.
     */
    public function store(StoreTenantApplicationRequest $request, TenantApplicationService $service): RedirectResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Profil penghuni Anda belum lengkap. Silakan hubungi admin.');
        }

        try {
            $service->apply(
                $tenant,
                (int) $request->validated('room_id'),
                $request->validated('application_notes')
            );

            return redirect()->route('portal.applications.index')
                ->with('status', 'Pengajuan sewa kamar berhasil dikirim! Silakan menunggu konfirmasi dari pengelola kost.');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Batalkan pengajuan sewa kamar yang masih pending.
     */
    public function cancel(Request $request, TenantApplication $application, TenantApplicationService $service): RedirectResponse
    {
        Gate::authorize('cancel', $application);

        try {
            $service->cancel($application, $request->user()->tenant->id);

            return redirect()->route('portal.applications.index')
                ->with('status', 'Pengajuan sewa kamar telah dibatalkan.');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
