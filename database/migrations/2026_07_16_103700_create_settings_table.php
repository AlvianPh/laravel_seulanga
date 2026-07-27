<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('kost_name')->default('Kost App');
            $table->text('kost_address')->nullable();
            $table->integer('default_due_date_day')->default(10);
            
            $table->foreignId('default_late_fee_id')->nullable()
                  ->constrained('additional_fee_types')->nullOnDelete();
                  
            $table->foreignId('default_bank_account_id')->nullable()
                  ->constrained('bank_accounts')->nullOnDelete();

            $table->timestamps();
        });

        // Seed the single default row
        DB::table('settings')->insert([
            'id' => 1,
            'kost_name' => 'Nama Kost Anda',
            'kost_address' => 'Alamat Kost Anda',
            'default_due_date_day' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
