<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Invoice;
use App\Enums\StatusTagihan;
use App\Services\GenerateInvoiceService;
use App\Notifications\InvoiceOverdueNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class NotifyInvoiceOverdueCommand extends Command
{
    protected $signature = 'notify:invoice-overdue';
    protected $description = 'Cek tagihan overdue dan kirim notifikasi';

    public function handle(GenerateInvoiceService $service)
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $invoices = Invoice::where('status', StatusTagihan::Pending)
            ->where('due_date', '<', $today)
            ->get();

        $service->markOverdueInvoices();

        $admins = User::whereIn('role', ['admin', 'owner'])->get();

        foreach ($invoices as $invoice) {
            $invoice->refresh();
            Notification::send($admins, new InvoiceOverdueNotification($invoice));
            $this->info("Notifikasi Overdue terkirim untuk Invoice #{$invoice->id}");
        }

        if ($invoices->isEmpty()) {
            $this->info("Tidak ada tagihan overdue yang baru.");
        }
    }
}
