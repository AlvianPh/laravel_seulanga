<?php

namespace App\Models;

use App\Enums\KategoriPengeluaran;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Expense — pengeluaran operasional kost.
 *
 * @property int                  $id
 * @property KategoriPengeluaran  $category
 * @property string               $description
 * @property float                $amount
 * @property string               $expense_date
 * @property string|null          $receipt_path
 * @property int                  $created_by
 */
class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'category',
        'description',
        'amount',
        'expense_date',
        'receipt_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'category'     => KategoriPengeluaran::class,
            'expense_date' => 'date',
            'amount'       => 'decimal:2',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** User yang menginput pengeluaran ini. */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
