<?php

namespace App\Http\Controllers\Portal;

use App\Enums\StatusMoveOut;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreMoveOutRequest;
use App\Models\MoveOutRequest;
use App\Services\MoveOutService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MoveOutController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected MoveOutService $service
    ) {}

    /**
     * Menampilkan daftar permohonan move-out milik penghuni.
     */
    public function index(Request $request): View
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return view('portal.move-outs.index', [
                'tenant' => null,
                'requests' => collect(),
                'activeContract' => null,
                'statuses' => StatusMoveOut::cases(),
            ]);
        }

        $activeContract = $tenant->activeContract();

        $query = $tenant->moveOutRequests()
            ->with(['contract.room', 'reviewer', 'inspector'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(10)->withQueryString();
        $statuses = StatusMoveOut::cases();

        return view('portal.move-outs.index', compact('tenant', 'requests', 'activeContract', 'statuses'));
    }

    /**
     * Form pengajuan move-out baru.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $tenant = $request->user()->tenant;

        if (! $tenant || ! $tenant->activeContract()) {
            return redirect()->route('portal.move-outs.index')
                ->with('error', 'Hanya penghuni dengan kontrak sewa aktif yang dapat mengajukan move-out.');
        }

        $activeContract = $tenant->activeContract();

        if ($activeContract->activeMoveOutRequest()) {
            return redirect()->route('portal.move-outs.show', $activeContract->activeMoveOutRequest())
                ->with('error', 'Anda sudah memiliki permohonan move-out yang sedang berjalan.');
        }

        return view('portal.move-outs.create', compact('tenant', 'activeContract'));
    }

    /**
     * Simpan pengajuan move-out baru.
     */
    public function store(StoreMoveOutRequest $request): RedirectResponse
    {
        $tenant = $request->user()->tenant;

        try {
            $moveOut = $this->service->createRequest(
                tenant: $tenant,
                data: $request->validated(),
                actor: $request->user()
            );

            return redirect()->route('portal.move-outs.show', $moveOut)
                ->with('success', 'Permohonan move-out berhasil diajukan dan sedang menunggu review pengelola.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan detail permohonan move-out untuk penghuni.
     */
    public function show(MoveOutRequest $moveOut): View
    {
        $this->authorize('view', $moveOut);

        $moveOut->load(['contract.room', 'reviewer', 'inspector', 'settler', 'completer']);
        $settlement = $this->service->calculateSettlement($moveOut);

        return view('portal.move-outs.show', compact('moveOut', 'settlement'));
    }

    /**
     * Membatalkan permohonan move-out oleh penghuni sendiri.
     */
    public function cancel(MoveOutRequest $moveOut): RedirectResponse
    {
        $this->authorize('cancel', $moveOut);

        try {
            $this->service->cancel($moveOut, request()->user());

            return redirect()->route('portal.move-outs.show', $moveOut)
                ->with('success', 'Permohonan move-out berhasil dibatalkan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
