<?php

namespace App\Http\Controllers\Portal;

use App\Enums\StatusKontrak;
use App\Enums\StatusTagihan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * DashboardController (Tenant Portal)
 *
 * Menampilkan dashboard mandiri untuk penghuni kost.
 * Action-oriented: menampilkan status akun, kamar yang ditempati,
 * dan tagihan mendesak jika sudah terhubung dengan data Tenant.
 */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Eager load data tenant beserta kontrak aktif dan tagihan
        $tenant = $user->tenant;

        $activeContract = null;
        $currentRoom = null;
        $pendingInvoice = null;

        if ($tenant) {
            $activeContract = $tenant->contracts()
                ->where('status', StatusKontrak::Active)
                ->with(['room.roomType', 'room.facilities'])
                ->latest()
                ->first();

            $currentRoom = $activeContract?->room;

            $pendingInvoice = $tenant->invoices()
                ->whereIn('status', [StatusTagihan::Pending, StatusTagihan::Overdue])
                ->orderBy('due_date', 'asc')
                ->first();
        }

        return view('portal.dashboard', compact(
            'user',
            'tenant',
            'activeContract',
            'currentRoom',
            'pendingInvoice'
        ));
    }
}
