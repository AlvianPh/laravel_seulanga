<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── USER ────────────────────────────────────────────────────────────
        User::factory()->owner()->create([
            'name'  => 'Budi Santoso',
            'email' => 'owner@kost.test',
        ]);

        User::factory()->admin()->create([
            'name'  => 'Rina Wijaya',
            'email' => 'admin@kost.test',
        ]);
    }
}
