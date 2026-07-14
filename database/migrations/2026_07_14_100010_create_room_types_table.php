<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel room_types — master data tipe kamar kost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Nama tipe kamar, misal: Standard, Deluxe, Suite');
            $table->text('description')->nullable()->comment('Deskripsi singkat tipe kamar');
            $table->decimal('default_price', 12, 2)->nullable()->comment('Harga rekomendasi / referensi untuk tipe ini');
            $table->timestamps();
        });

        // Seed tiga tipe default yang sesuai dengan ENUM lama
        DB::table('room_types')->insert([
            ['name' => 'Standard', 'description' => 'Kamar standar dengan fasilitas dasar',        'default_price' => 800000,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deluxe',   'description' => 'Kamar deluxe dengan fasilitas lebih lengkap', 'default_price' => 1500000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Suite',    'description' => 'Kamar suite premium dengan fasilitas terlengkap', 'default_price' => 2000000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
