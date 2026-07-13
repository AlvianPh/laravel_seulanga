<?php

namespace Database\Factories;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPembayaran;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory Payment — membuat data dummy pembayaran tagihan.
 *
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $method = fake()->randomElement(MetodePembayaran::cases());

        return [
            'invoice_id'   => Invoice::factory(),
            'tenant_id'    => Tenant::factory(),
            'amount'       => 0, // diisi oleh seeder sesuai total_amount invoice
            'payment_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'method'       => $method,
            'status'       => StatusPembayaran::Verified,
            'proof_path'   => $method !== MetodePembayaran::Cash
                ? 'payments/bukti-' . fake()->uuid() . '.jpg'
                : null,
            'notes'        => null,
            'verified_by'  => null,
        ];
    }

    /** State: pembayaran sudah terverifikasi. */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusPembayaran::Verified,
        ]);
    }

    /** State: pembayaran menunggu verifikasi. */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => StatusPembayaran::Pending,
            'verified_by' => null,
        ]);
    }
}
