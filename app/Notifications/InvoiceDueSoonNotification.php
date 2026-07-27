<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoiceDueSoonNotification extends Notification
{
    use Queueable;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Tagihan Segera Jatuh Tempo',
            'message' => "Tagihan #INV-{$this->invoice->id} untuk {$this->invoice->tenant->name} akan jatuh tempo pada {$this->invoice->due_date->format('d M Y')}.",
            'url' => route('invoices.show', $this->invoice->id),
            'invoice_id' => $this->invoice->id,
        ];
    }
}
