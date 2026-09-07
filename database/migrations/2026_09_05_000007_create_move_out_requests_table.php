<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel move_out_requests — proses keluar kost dan penyelesaian kontrak sewa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_out_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->foreignId('contract_id')
                ->constrained('contracts')
                ->cascadeOnDelete();
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->cascadeOnDelete();
            $table->date('requested_move_out_date')->comment('Tanggal rencana keluar kost');
            $table->text('reason')->comment('Alasan keluar kost');
            $table->text('notes')->nullable()->comment('Catatan tambahan dari penghuni');
            $table->string('status', 30)->default('pending')->comment('pending, approved, inspection, settlement, completed, rejected, cancelled');

            // Staf Review
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_note')->nullable();

            // Inspeksi Kamar
            $table->foreignId('inspected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('inspected_at')->nullable();
            $table->string('room_condition', 50)->nullable()->comment('Kondisi kamar: baik, perbaikan_ringan, perbaikan_berat');
            $table->text('damage_notes')->nullable()->comment('Rincian kerusakan');
            $table->decimal('damage_cost', 12, 2)->default(0)->comment('Biaya estimasi kerusakan/perbaikan');
            $table->boolean('requires_room_maintenance')->default(false)->comment('Apakah kamar perlu status maintenance setelah kontrak selesai');
            $table->text('inspection_notes')->nullable();

            // Settlement Finansial
            $table->foreignId('settled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('settled_at')->nullable();
            $table->decimal('deposit_amount', 12, 2)->default(0)->comment('Nilai deposit awal pada kontrak');
            $table->decimal('outstanding_invoices_amount', 12, 2)->default(0)->comment('Sisa tagihan yang belum dibayar');
            $table->decimal('damage_deduction_amount', 12, 2)->default(0)->comment('Potongan biaya kerusakan dari deposit');
            $table->decimal('deposit_returned_amount', 12, 2)->default(0)->comment('Jumlah deposit yang dikembalikan');
            $table->decimal('remaining_tenant_liability', 12, 2)->default(0)->comment('Sisa kewajiban tenant jika tagihan + kerusakan melebihi deposit');
            $table->text('settlement_notes')->nullable();

            // Finalisasi Selesai
            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['contract_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_out_requests');
    }
};
