<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel maintenance_requests — laporan perbaikan & kerusakan fasilitas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique()->index()->comment('Nomor tiket unik MNT-YYYYMMDD-XXXX');
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->cascadeOnDelete();
            $table->string('category', 50)->comment('Kategori: plumbing, electrical, furniture, dll');
            $table->string('priority', 20)->default('medium')->comment('Urgensi: low, medium, high, urgent');
            $table->string('status', 20)->default('pending')->comment('Status: pending, in_progress, resolved, rejected');
            $table->string('location')->comment('Lokasi spesifik di dalam kamar/area kost');
            $table->text('description')->comment('Deskripsi detail masalah / kerusakan');
            $table->string('photo_path')->nullable()->comment('File path foto bukti kerusakan');
            $table->timestamp('reported_at')->useCurrent()->comment('Waktu pelaporan');
            $table->timestamp('resolved_at')->nullable()->comment('Waktu perbaikan selesai');
            $table->text('notes')->nullable()->comment('Catatan penanganan teknisi');
            $table->text('rejection_reason')->nullable()->comment('Alasan jika laporan ditolak');
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
