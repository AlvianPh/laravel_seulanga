<?php

namespace App\Console\Commands;

use App\Services\GenerateInvoiceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate {--month=} {--year=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan bulanan untuk semua kontrak aktif.';

    /**
     * Execute the console command.
     */
    public function handle(GenerateInvoiceService $service)
    {
        $month = $this->option('month') ? (int) $this->option('month') : Carbon::now()->month;
        $year = $this->option('year') ? (int) $this->option('year') : Carbon::now()->year;

        $this->info("Mulai membuat tagihan untuk bulan {$month} tahun {$year}...");

        $count = $service->generateMonthlyInvoices($month, $year);

        $this->info("Selesai! {$count} tagihan berhasil dibuat.");
    }
}
