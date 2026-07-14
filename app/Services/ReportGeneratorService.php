<?php

namespace App\Services;

use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportGeneratorService
{
    /**
     * Parse date range inputs
     */
    protected function parseDateRange(string $filterType, ?string $startDate, ?string $endDate): array
    {
        $now = Carbon::now();

        switch ($filterType) {
            case 'daily':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'weekly':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'yearly':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'custom':
            default:
                $start = $startDate ? Carbon::parse($startDate)->startOfDay() : $now->copy()->startOfMonth();
                $end = $endDate ? Carbon::parse($endDate)->endOfDay() : $now->copy()->endOfMonth();
                break;
        }

        return [$start, $end];
    }

    public function generateIncomeReport(string $filterType, ?string $startDate, ?string $endDate): Collection
    {
        [$start, $end] = $this->parseDateRange($filterType, $startDate, $endDate);

        return Payment::with(['invoice.room', 'tenant', 'verifier'])
            ->where('status', StatusPembayaran::Verified->value)
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('payment_date', 'asc')
            ->get();
    }

    public function generateExpenseReport(string $filterType, ?string $startDate, ?string $endDate): Collection
    {
        [$start, $end] = $this->parseDateRange($filterType, $startDate, $endDate);

        return Expense::with(['creator', 'expenseCategory'])
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('expense_date', 'asc')
            ->get();
    }

    public function generateCashFlowReport(string $filterType, ?string $startDate, ?string $endDate): Collection
    {
        // Gabungkan tabel pendapatan dan pengeluaran secara virtual
        // Mengembalikan array objek
        $incomes = $this->generateIncomeReport($filterType, $startDate, $endDate)->map(function($item) {
            return (object) [
                'date' => $item->payment_date->format('Y-m-d'),
                'type' => 'income',
                'description' => 'Pembayaran Tagihan INV-' . $item->invoice_id,
                'amount' => $item->amount,
            ];
        });

        $expenses = $this->generateExpenseReport($filterType, $startDate, $endDate)->map(function($item) {
            return (object) [
                'date' => $item->expense_date->format('Y-m-d'),
                'type' => 'expense',
                'description' => $item->description,
                'amount' => $item->amount,
            ];
        });

        return $incomes->concat($expenses)->sortBy('date')->values();
    }

    public function generateOccupancyReport(string $filterType, ?string $startDate, ?string $endDate): array
    {
        // Occupancy agak unik karena statik saat ini, kita ambil status realtime saja jika tidak dikembangkan lebih kompleks
        $totalRooms = Room::count();
        $occupied = Room::where('status', StatusKamar::Occupied->value)->count();
        $available = Room::where('status', StatusKamar::Available->value)->count();
        $maintenance = $totalRooms - ($occupied + $available);

        return [
            'total' => $totalRooms,
            'occupied' => $occupied,
            'available' => $available,
            'maintenance' => $maintenance,
            'rate' => $totalRooms > 0 ? round(($occupied / $totalRooms) * 100, 1) : 0,
        ];
    }

    public function generateReceivablesReport(): Collection
    {
        // Laporan piutang tidak terikat date range, melainkan status pending/overdue saat ini
        return Invoice::with(['tenant', 'room'])
            ->whereIn('status', [StatusTagihan::Pending->value, StatusTagihan::Overdue->value])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function generateProfitLossReport(string $filterType, ?string $startDate, ?string $endDate): array
    {
        $incomes = $this->generateIncomeReport($filterType, $startDate, $endDate);
        $expenses = $this->generateExpenseReport($filterType, $startDate, $endDate);

        $totalIncome = $incomes->sum('amount');
        $totalExpense = $expenses->sum('amount');

        // Breakdown expense by category
        $expenseBreakdown = $expenses->groupBy('expense_category_id')->map(function ($group) {
            return [
                'label' => $group->first()->expenseCategory->name ?? '-',
                'total' => $group->sum('amount')
            ];
        });

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_profit' => $totalIncome - $totalExpense,
            'expense_breakdown' => $expenseBreakdown
        ];
    }

    /**
     * Dapatkan rentang tanggal format string untuk label laporan
     */
    public function getDateRangeLabel(string $filterType, ?string $startDate, ?string $endDate): string
    {
        [$start, $end] = $this->parseDateRange($filterType, $startDate, $endDate);
        return $start->format('d M Y') . ' - ' . $end->format('d M Y');
    }
}
