<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Insert default payment methods
        $methods = [
            ['name' => 'Tunai', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transfer Bank', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'QRIS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lainnya', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('payment_methods')->insert($methods);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
