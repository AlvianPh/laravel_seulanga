<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\User;
use App\Notifications\InvoiceCreatedNotification;
use App\Services\GenerateInvoiceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotifyInvoiceCreatedCommand extends Command
{
    protected $signature = 'notify:invoice-created {--force-test : Buat notifikasi dummy untuk testing jika tagihan bulan ini sudah di-generate}';
    protected $description = 'Generate tagihan bulanan dan kirim notifikasi pembuatan tagihan.';

    public function handle(GenerateInvoiceService $service)
    {
        $this->info("Menjalankan proses generate tagihan...");

        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $count = $service->generateMonthlyInvoices($month, $year);
        $this->info("Selesai! {$count} tagihan berhasil dibuat (dan notifikasi otomatis terkirim jika ada tagihan baru).");

        if ($count == 0 && $this->option('force-test')) {
            $this->info("Flag --force-test aktif. Mencari tagihan terakhir untuk mengirim notifikasi dummy...");
            $latestInvoice = Invoice::latest()->first();
            if ($latestInvoice) {
                $admins = User::whereIn('role', ['admin', 'owner'])->get();
                Notification::send($admins, new InvoiceCreatedNotification($latestInvoice));
                $this->info("Notifikasi dummy terkirim untuk Invoice #{$latestInvoice->id}.");
            } else {
                $this->warn("Tidak ada tagihan di database untuk dikirim sebagai tes.");
            }
        }
    }
}
