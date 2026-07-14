<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel facilities — master data fasilitas kamar kost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Nama fasilitas, misal: AC, WiFi, Kasur');
            $table->string('icon', 100)->nullable()->comment('Nama ikon untuk ditampilkan di UI');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
