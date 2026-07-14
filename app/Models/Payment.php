<?php

namespace App\Models;

use App\Enums\StatusPembayaran;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Payment — pencatatan pembayaran tagihan.
 *
 * @property int               $id
 * @property int               $invoice_id
 * @property int               $tenant_id
 * @property float             $amount
 * @property string            $payment_date
 * @property int               $payment_method_id
 * @property StatusPembayaran  $status
 * @property string|null       $proof_path
 * @property string|null       $notes
 * @property int|null          $verified_by
 */
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'tenant_id',
        'amount',
        'payment_date',
        'payment_method_id',
        'status',
        'proof_path',
        'notes',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'status'       => StatusPembayaran::class,
            'payment_date' => 'date',
            'amount'       => 'decimal:2',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Tagihan yang dibayar. */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** Penghuni yang membayar. */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** Metode pembayaran. */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /** User yang memverifikasi pembayaran ini. */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
