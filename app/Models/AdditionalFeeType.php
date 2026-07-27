<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model AdditionalFeeType — master data jenis denda/biaya tambahan.
 *
 * @property int $id
 * @property string $nama
 * @property string $jenis
 * @property float $nilai_default
 * @property bool $is_active
 */
class AdditionalFeeType extends Model
{
    protected $fillable = [
        'nama',
        'jenis',
        'nilai_default',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'nilai_default' => 'decimal:2',
    ];
}
