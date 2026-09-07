<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel tenant_permissions — permohonan izin penghuni kost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->foreignId('contract_id')
                ->constrained('contracts')
                ->cascadeOnDelete();
            $table->string('type', 50)->comment('Jenis izin: guest_stay, late_return, electronic_device, other');
            $table->string('title')->comment('Judul / ringkasan permohonan izin');
            $table->text('description')->comment('Rincian detail permohonan izin');
            $table->dateTime('start_at')->nullable()->comment('Tanggal/waktu mulai (opsional sesuai jenis izin)');
            $table->dateTime('end_at')->nullable()->comment('Tanggal/waktu selesai (opsional sesuai jenis izin)');
            $table->string('status', 20)->default('pending')->comment('Status: pending, approved, rejected, cancelled');
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable()->comment('Waktu peninjauan oleh staf');
            $table->text('review_note')->nullable()->comment('Catatan keputusan staf / alasan penolakan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_permissions');
    }
};
