<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContractEndingNotification extends Notification
{
    use Queueable;

    public $contract;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Kontrak Akan Habis',
            'message' => "Kontrak untuk kamar {$this->contract->room->room_number} (Penghuni: {$this->contract->tenant->name}) akan habis pada {$this->contract->end_date->format('d M Y')}.",
            'url' => route('contracts.show', $this->contract->id),
            'contract_id' => $this->contract->id,
        ];
    }
}
