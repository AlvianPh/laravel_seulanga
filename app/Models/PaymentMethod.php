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
class PaymentMethod extends Model
{
    protected $fillable = ['name'];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
