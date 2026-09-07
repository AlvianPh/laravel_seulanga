<?php

namespace App\Http\Controllers;

use App\Enums\StatusMoveOut;
use App\Http\Requests\FinalizeMoveOutRequest;
use App\Http\Requests\InspectionMoveOutRequest;
use App\Http\Requests\ReviewMoveOutRequest;
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
     * Menampilkan daftar permohonan move-out untuk staf (Admin/Owner).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MoveOutRequest::class);

        $query = MoveOutRequest::with(['tenant', 'contract.room', 'reviewer', 'inspector']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhereHas('tenant', function ($t) use ($search) {
                        $t->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contract.room', function ($r) use ($search) {
                        $r->where('room_number', 'like', "%{$search}%");
                    });
            });
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'total' => MoveOutRequest::count(),
            'pending' => MoveOutRequest::where('status', StatusMoveOut::Pending)->count(),
            'approved' => MoveOutRequest::where('status', StatusMoveOut::Approved)->count(),
            'inspection' => MoveOutRequest::where('status', StatusMoveOut::Inspection)->count(),
            'completed' => MoveOutRequest::where('status', StatusMoveOut::Completed)->count(),
            'rejected' => MoveOutRequest::where('status', StatusMoveOut::Rejected)->count(),
            'cancelled' => MoveOutRequest::where('status', StatusMoveOut::Cancelled)->count(),
        ];

        $statuses = StatusMoveOut::cases();

        return view('move-outs.index', compact('requests', 'counts', 'statuses'));
    }

    /**
     * Menampilkan detail permohonan move-out, rincian inspeksi & settlement.
     */
    public function show(MoveOutRequest $moveOut): View
    {
        $this->authorize('view', $moveOut);

        $moveOut->load(['tenant.user', 'contract.room.roomType', 'contract.invoices.payments', 'reviewer', 'inspector', 'settler', 'completer']);
        $settlement = $this->service->calculateSettlement($moveOut);

        return view('move-outs.show', compact('moveOut', 'settlement'));
    }

    /**
     * Memberikan persetujuan awal (Approve / Reject) permohonan move-out.
     */
    public function review(ReviewMoveOutRequest $request, MoveOutRequest $moveOut): RedirectResponse
    {
        $action = $request->input('action');
        $note = $request->input('review_note');

        $this->authorize($action === 'approve' ? 'approve' : 'reject', $moveOut);

        try {
            if ($action === 'approve') {
                $this->service->approve($moveOut, $note, $request->user());
                $message = 'Permohonan move-out berhasil disetujui. Langkah selanjutnya adalah inspeksi kamar.';
            } else {
                $this->service->reject($moveOut, (string) $note, $request->user());
                $message = 'Permohonan move-out berhasil ditolak.';
            }

            return redirect()->route('move-outs.show', $moveOut)
                ->with('success', $message);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Mencatat hasil inspeksi kamar dan kerusakan.
     */
    public function inspect(InspectionMoveOutRequest $request, MoveOutRequest $moveOut): RedirectResponse
    {
        $this->authorize('inspect', $moveOut);

        try {
            $this->service->recordInspection($moveOut, $request->validated(), $request->user());

            return redirect()->route('move-outs.show', $moveOut)
                ->with('success', 'Hasil inspeksi kamar berhasil disimpan.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Menyelesaikan settlement dan mengakhiri kontrak sewa secara resmi.
     */
    public function finalize(FinalizeMoveOutRequest $request, MoveOutRequest $moveOut): RedirectResponse
    {
        $this->authorize('finalize', $moveOut);

        try {
            $this->service->finalize($moveOut, $request->input('settlement_notes'), $request->user());

            return redirect()->route('move-outs.show', $moveOut)
                ->with('success', 'Proses move-out berhasil diselesaikan. Kontrak telah diakhiri dan status kamar telah diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
