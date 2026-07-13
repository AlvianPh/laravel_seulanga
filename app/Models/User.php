<?php

namespace App\Models;

use App\Enums\RoleUser;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User — akun login Owner dan Admin sistem kost.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $password
 * @property RoleUser    $role
 * @property string|null $email_verified_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => RoleUser::class,
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Kontrak yang dibuat oleh user ini. */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'created_by');
    }

    /** Pengeluaran yang diinput oleh user ini. */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    /** Pembayaran yang diverifikasi oleh user ini. */
    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Cek apakah user adalah Owner. */
    public function isOwner(): bool
    {
        return $this->role === RoleUser::Owner;
    }

    /** Cek apakah user adalah Admin. */
    public function isAdmin(): bool
    {
        return $this->role === RoleUser::Admin;
    }
}
