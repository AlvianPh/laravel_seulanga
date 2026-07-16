<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model BankAccount — master data bank/rekening.
 *
 * @property int $id
 * @property string $nama_bank
 * @property string $nomor_rekening
 * @property string $nama_pemilik_rekening
 * @property bool $is_active
 */
class BankAccount extends Model
{
    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
