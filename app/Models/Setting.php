<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Setting — Konfigurasi aplikasi (Single-row table).
 *
 * @property int $id
 * @property string $kost_name
 * @property string|null $kost_address
 * @property int $default_due_date_day
 * @property int|null $default_late_fee_id
 * @property int|null $default_bank_account_id
 */
class Setting extends Model
{
    protected $fillable = [
        'kost_name',
        'kost_logo',
        'kost_address',
        'default_due_date_day',
        'default_late_fee_id',
        'default_bank_account_id',
    ];

    /**
     * Dapatkan instance tunggal dari Setting.
     * Jika tidak ada, akan mengembalikan instance baru dengan nilai default.
     */
    public static function getInstance(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'kost_name' => 'Nama Kost Anda',
            'default_due_date_day' => 10,
        ]);
    }

    public function defaultLateFee(): BelongsTo
    {
        return $this->belongsTo(AdditionalFeeType::class, 'default_late_fee_id');
    }

    public function defaultBankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'default_bank_account_id');
    }
}
