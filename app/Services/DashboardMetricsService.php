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
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    public function getSummaryStats(): array
    {
        $now = Carbon::now();

        // 1. Kamar & Occupancy
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', StatusKamar::Available->value)->count();
        $occupiedRooms = Room::where('status', StatusKamar::Occupied->value)->count();

        // 2. Penghuni Aktif
        $activeTenantsCount = Contract::where('status', StatusKontrak::Active->value)->distinct('tenant_id')->count('tenant_id');

        // 3. Tagihan Jatuh Tempo (Pending/Overdue <= 7 hari ke depan)
        $sevenDaysFromNow = $now->copy()->addDays(7);
        $dueInvoicesCount = Invoice::whereIn('status', [StatusTagihan::Pending->value, StatusTagihan::Overdue->value])
            ->whereDate('due_date', '<=', $sevenDaysFromNow->toDateString())
            ->count();

        // 4. Pembayaran Hari Ini
        $paymentsToday = Payment::whereDate('payment_date', $now->toDateString())
            ->where('status', StatusPembayaran::Verified->value)
            ->sum('amount');

        return [
            'totalRooms' => $totalRooms,
            'availableRooms' => $availableRooms,
            'occupiedRooms' => $occupiedRooms,
            'activeTenants' => $activeTenantsCount,
            'dueInvoices' => $dueInvoicesCount,
            'paymentsToday' => $paymentsToday,
            'occupancyRate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0,
        ];
    }

    public function getMonthlyFinancials(): array
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        // Pendapatan bulan ini (dari payment verified)
        $incomeThisMonth = Payment::whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->where('status', StatusPembayaran::Verified->value)
            ->sum('amount');

        // Pengeluaran bulan ini
        $expensesThisMonth = Expense::whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        // Laba
        $profitThisMonth = $incomeThisMonth - $expensesThisMonth;

        return [
            'income' => $incomeThisMonth,
            'expense' => $expensesThisMonth,
            'profit' => $profitThisMonth,
        ];
    }

    public function getChartData(): array
    {
        $months = [];
        $incomes = [];
        $expenses = [];
        $profits = [];

        // Ambil 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $months[] = $monthLabel;

            $inc = Payment::whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->where('status', StatusPembayaran::Verified->value)
                ->sum('amount');
                
            $exp = Expense::whereMonth('expense_date', $date->month)
                ->whereYear('expense_date', $date->year)
                ->sum('amount');

            $incomes[] = (float) $inc;
            $expenses[] = (float) $exp;
            $profits[] = (float) ($inc - $exp);
        }

        return [
            'labels' => $months,
            'incomes' => $incomes,
            'expenses' => $expenses,
            'profits' => $profits,
        ];
    }
}
