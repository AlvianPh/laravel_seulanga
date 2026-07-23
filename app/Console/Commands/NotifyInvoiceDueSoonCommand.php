<?php

namespace App\Console\Commands;

use App\Enums\StatusTagihan;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\InvoiceDueSoonNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotifyInvoiceDueSoonCommand extends Command
{
    protected $signature = 'notify:invoice-due-soon';
    protected $description = 'Kirim notifikasi untuk tagihan yang jatuh tempo H-3';

    public function handle()
    {
        $targetDate = Carbon::today()->addDays(3)->format('Y-m-d');
        
        $invoices = Invoice::where('status', StatusTagihan::Pending)
            ->where('due_date', $targetDate)
            ->get();

        $admins = User::whereIn('role', ['admin', 'owner'])->get();

        foreach ($invoices as $invoice) {
            Notification::send($admins, new InvoiceDueSoonNotification($invoice));
            $this->info("Notifikasi H-3 terkirim untuk Invoice #{$invoice->id}");
        }

        if ($invoices->isEmpty()) {
            $this->info("Tidak ada tagihan yang jatuh tempo pada {$targetDate}.");
        }
    }
}
