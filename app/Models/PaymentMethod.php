<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model PaymentMethod — master data metode pembayaran.
 *
 * @property int    $id
 * @property string $name
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
