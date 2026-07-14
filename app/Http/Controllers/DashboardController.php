<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * DashboardController — halaman utama setelah login.
 * Dapat diakses oleh Owner maupun Admin.
 */
class DashboardController extends Controller
{
    protected DashboardMetricsService $dashboardService;

    public function __construct(DashboardMetricsService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /** Tampilkan halaman dashboard. */
    public function index(): View
    {
        $stats = $this->dashboardService->getSummaryStats();
        $financials = $this->dashboardService->getMonthlyFinancials();
        $chartData = $this->dashboardService->getChartData();

        return view('dashboard', compact('stats', 'financials', 'chartData'));
    }
}
