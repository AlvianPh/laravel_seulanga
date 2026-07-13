<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel rooms — data semua kamar kost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number', 20)->unique()->comment('Nomor kamar, contoh: 101, A2');
            $table->tinyInteger('floor')->unsigned()->index()->comment('Lantai ke-berapa');
            $table->enum('type', ['standard', 'deluxe', 'suite'])->index();
            $table->decimal('size_m2', 5, 2)->nullable()->comment('Luas dalam meter persegi');
            $table->decimal('monthly_price', 12, 2)->comment('Harga sewa per bulan');
            $table->decimal('deposit_price', 12, 2)->comment('Besaran deposit standar');
            $table->enum('status', ['available', 'occupied', 'maintenance'])
                ->default('available')
                ->index();
            $table->json('facilities')->nullable()->comment('Daftar fasilitas: AC, WiFi, dll');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
