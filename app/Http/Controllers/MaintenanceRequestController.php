<?php

namespace App\Http\Controllers;

use App\Enums\KategoriMaintenance;
use App\Enums\PrioritasMaintenance;
use App\Enums\StatusMaintenance;
use App\Http\Requests\UpdateMaintenanceStatusRequest;
use App\Models\ExpenseCategory;
use App\Models\MaintenanceRequest;
use App\Services\MaintenanceRequestService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRequestController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected MaintenanceRequestService $service
    ) {}

    /**
     * Menampilkan daftar semua permintaan maintenance untuk staf (Admin/Owner).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MaintenanceRequest::class);

        $query = MaintenanceRequest::with(['tenant', 'room', 'expenses']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('tenant', function ($t) use ($search) {
                        $t->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('room', function ($r) use ($search) {
                        $r->where('room_number', 'like', "%{$search}%");
                    });
            });
        }

        $requests = $query->latest('reported_at')->paginate(10)->withQueryString();

        $counts = [
            'total' => MaintenanceRequest::count(),
            'pending' => MaintenanceRequest::where('status', StatusMaintenance::Pending)->count(),
            'in_progress' => MaintenanceRequest::where('status', StatusMaintenance::InProgress)->count(),
            'resolved' => MaintenanceRequest::where('status', StatusMaintenance::Resolved)->count(),
            'rejected' => MaintenanceRequest::where('status', StatusMaintenance::Rejected)->count(),
        ];

        $statuses = StatusMaintenance::cases();
        $priorities = PrioritasMaintenance::cases();
        $categories = KategoriMaintenance::cases();

        return view('maintenance.index', compact('requests', 'counts', 'statuses', 'priorities', 'categories'));
    }

    /**
     * Menampilkan detail permintaan maintenance dan riwayat biayanya.
     */
    public function show(MaintenanceRequest $maintenance): View
    {
        $this->authorize('view', $maintenance);

        $maintenance->load(['tenant.user', 'room.roomType', 'expenses.expenseCategory', 'creator', 'updater']);
        $expenseCategories = ExpenseCategory::all();
        $statuses = StatusMaintenance::cases();

        return view('maintenance.show', compact('maintenance', 'expenseCategories', 'statuses'));
    }

    /**
     * Memperbarui status penanganan perbaikan (in_progress, resolved, rejected).
     */
    public function updateStatus(UpdateMaintenanceStatusRequest $request, MaintenanceRequest $maintenance): RedirectResponse
    {
        $this->authorize('updateStatus', $maintenance);

        try {
            $newStatus = StatusMaintenance::from($request->status);
            $this->service->updateStatus(
                request: $maintenance,
                newStatus: $newStatus,
                additionalData: $request->validated(),
                actor: $request->user()
            );

            return redirect()->route('maintenance.show', $maintenance)
                ->with('success', 'Status permintaan perbaikan berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
