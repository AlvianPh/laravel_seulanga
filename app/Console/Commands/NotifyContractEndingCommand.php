<?php

namespace App\Console\Commands;

use App\Enums\StatusKontrak;
use App\Models\Contract;
use App\Models\User;
use App\Notifications\ContractEndingNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotifyContractEndingCommand extends Command
{
    protected $signature = 'notify:contract-ending';
    protected $description = 'Kirim notifikasi untuk kontrak yang akan habis H-14';

    public function handle()
    {
        $targetDate = Carbon::today()->addDays(14)->format('Y-m-d');
        
        $contracts = Contract::where('status', StatusKontrak::Active)
            ->where('end_date', $targetDate)
            ->get();

        $admins = User::whereIn('role', ['admin', 'owner'])->get();

        foreach ($contracts as $contract) {
            Notification::send($admins, new ContractEndingNotification($contract));
            $this->info("Notifikasi kontrak H-14 terkirim untuk Kontrak #{$contract->id}");
        }

        if ($contracts->isEmpty()) {
            $this->info("Tidak ada kontrak yang akan habis pada {$targetDate}.");
        }
    }
}
