<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Insert default categories matching previous ENUM
        $categories = [
            ['name' => 'Listrik', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Air', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Internet', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Perbaikan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kebersihan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gaji', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lainnya', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('expense_categories')->insert($categories);
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
