<?php

namespace Database\Factories;

use App\Enums\StatusApplication;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TenantApplication>
 */
class TenantApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'room_id' => Room::factory(),
            'status' => StatusApplication::Pending,
            'application_notes' => fake()->optional()->sentence(),
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }

    public function approved(?User $reviewer = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusApplication::Approved,
            'reviewed_by' => $reviewer?->id ?? User::factory()->admin(),
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(string $reason = 'Kamar sudah terisi.', ?User $reviewer = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusApplication::Rejected,
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewer?->id ?? User::factory()->admin(),
            'reviewed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusApplication::Cancelled,
        ]);
    }
}
