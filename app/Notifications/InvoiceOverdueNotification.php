<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoiceOverdueNotification extends Notification
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
            'title' => 'Tagihan Overdue',
            'message' => "Tagihan #INV-{$this->invoice->id} untuk {$this->invoice->tenant->name} telah melewati batas waktu pembayaran.",
            'url' => route('invoices.show', $this->invoice->id),
            'invoice_id' => $this->invoice->id,
        ];
    }
}
