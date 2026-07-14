<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Hapus kolom JSON facilities dari tabel rooms.
 * Dijalankan setelah migrasi data pivot terkonfirmasi aman.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('facilities');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->json('facilities')->nullable()->comment('Daftar fasilitas: AC, WiFi, dll')->after('status');
        });
    }
};
