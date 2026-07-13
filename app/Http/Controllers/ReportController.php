<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Services\ReportGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    protected ReportGeneratorService $reportService;

    public function __construct(ReportGeneratorService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Tampilkan halaman pilih laporan.
     */
    public function index(Request $request): View
    {
        return view('reports.index');
    }

    /**
     * Hitung laporan, lalu putuskan apakah return View(Tabel Web), PDF, atau Excel/CSV
     */
    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense,cashflow,occupancy,receivables,profit_loss',
            'filter' => 'required|in:daily,weekly,monthly,yearly,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'action' => 'required|in:view,pdf,excel,csv'
        ]);

        $type = $request->input('type');
        $filter = $request->input('filter');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $action = $request->input('action');

        $data = [];
        $viewName = '';
        $reportTitle = '';

        $dateLabel = $this->reportService->getDateRangeLabel($filter, $startDate, $endDate);

        switch ($type) {
            case 'income':
                $data['records'] = $this->reportService->generateIncomeReport($filter, $startDate, $endDate);
                $viewName = 'reports.partials.income_table';
                $reportTitle = 'Laporan Pendapatan';
                break;
            case 'expense':
                $data['records'] = $this->reportService->generateExpenseReport($filter, $startDate, $endDate);
                $viewName = 'reports.partials.expense_table';
                $reportTitle = 'Laporan Pengeluaran';
                break;
            case 'cashflow':
                $data['records'] = $this->reportService->generateCashFlowReport($filter, $startDate, $endDate);
                $viewName = 'reports.partials.cashflow_table';
                $reportTitle = 'Laporan Arus Kas';
                break;
            case 'occupancy':
                $data['stats'] = $this->reportService->generateOccupancyReport($filter, $startDate, $endDate);
                $viewName = 'reports.partials.occupancy_table';
                $reportTitle = 'Laporan Keterisian Kamar (Occupancy)';
                // Occupancy statik, tapi kita lewatkan date label
                break;
            case 'receivables':
                $data['records'] = $this->reportService->generateReceivablesReport();
                $viewName = 'reports.partials.receivables_table';
                $reportTitle = 'Laporan Piutang Tagihan';
                $dateLabel = 'Sisa Piutang Berjalan';
                break;
            case 'profit_loss':
                $data['report'] = $this->reportService->generateProfitLossReport($filter, $startDate, $endDate);
                $viewName = 'reports.partials.profit_loss_table';
                $reportTitle = 'Laporan Laba Rugi';
                break;
        }

        $data['title'] = $reportTitle;
        $data['dateLabel'] = $dateLabel;
        $data['isExport'] = ($action !== 'view');

        // Action: View HTML (Results page)
        if ($action === 'view') {
            return view('reports.results', [
                'type' => $type,
                'filter' => $filter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'title' => $reportTitle,
                'dateLabel' => $dateLabel,
                'viewName' => $viewName,
                'data' => $data
            ]);
        }

        // Action: PDF
        if ($action === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf_template', ['viewName' => $viewName, 'data' => $data]);
            return $pdf->download(strtolower(str_replace(' ', '_', $reportTitle)) . '.pdf');
        }

        // Action: Excel / CSV
        $filename = strtolower(str_replace(' ', '_', $reportTitle));
        $exportFormat = $action === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;
        $ext = $action === 'csv' ? '.csv' : '.xlsx';

        return Excel::download(new ReportExport($viewName, $data), $filename . $ext, $exportFormat);
    }
}
