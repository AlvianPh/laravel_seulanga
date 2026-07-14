<?php

namespace App\Console\Commands;

use App\Services\GenerateInvoiceService;
use Illuminate\Console\Command;

class CheckOverdueInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek tagihan pending yang melewati batas waktu bayar (due_date) dan menandainya menjadi overdue.';

    /**
     * Execute the console command.
     */
    public function handle(GenerateInvoiceService $service)
    {
        $this->info("Mengecek tagihan yang jatuh tempo...");

        $count = $service->markOverdueInvoices();

        $this->info("Selesai! {$count} tagihan ditandai sebagai overdue.");
    }
}
