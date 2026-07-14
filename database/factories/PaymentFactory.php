<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
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
        $method = PaymentMethod::inRandomOrder()->first() ?? PaymentMethod::factory()->create();

        return [
            'invoice_id'   => Invoice::factory(),
            'tenant_id'    => Tenant::factory(),
            'amount'       => 0, // diisi oleh seeder sesuai total_amount invoice
            'payment_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'payment_method_id' => $method->id,
            'status'       => StatusPembayaran::Verified,
            'proof_path'   => $method->name !== 'Tunai'
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
