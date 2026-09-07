<?php

namespace Database\Factories;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Tenant;
use App\Models\TenantDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TenantDocument>
 */
class TenantDocumentFactory extends Factory
{
    protected $model = TenantDocument::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'uploaded_by' => User::factory(),
            'type' => DocumentType::Other,
            'status' => DocumentStatus::Pending,
            'title' => fake()->sentence(3),
            'file_path' => 'documents/fake/'.Str::uuid().'.pdf',
            'file_name' => fake()->word().'.pdf',
            'file_size' => fake()->numberBetween(100000, 2000000),
            'mime_type' => 'application/pdf',
            'description' => fake()->sentence(),
            'rejection_reason' => null,
            'verified_by' => null,
            'verified_at' => null,
        ];
    }
}
