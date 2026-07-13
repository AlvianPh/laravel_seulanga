<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel invoices — tagihan bulanan penghuni.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')
                ->constrained('contracts')
                ->restrictOnDelete();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->restrictOnDelete()
                ->comment('Denormalized untuk query cepat');
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->restrictOnDelete()
                ->comment('Denormalized untuk query cepat');
            $table->smallInteger('year')->unsigned()->comment('Tahun tagihan, contoh: 2026');
            $table->tinyInteger('month')->unsigned()->comment('Bulan tagihan (1-12)');
            $table->decimal('rent_amount', 12, 2)->comment('Komponen biaya sewa');
            $table->decimal('electricity_fee', 10, 2)->nullable()->comment('Komponen biaya listrik');
            $table->decimal('water_fee', 10, 2)->nullable()->comment('Komponen biaya air');
            $table->decimal('internet_fee', 10, 2)->nullable()->comment('Komponen biaya internet');
            $table->decimal('penalty_fee', 10, 2)->nullable()->comment('Komponen denda keterlambatan');
            $table->decimal('other_fee', 10, 2)->nullable()->comment('Komponen biaya lain-lain');
            $table->decimal('total_amount', 12, 2)->comment('Total semua komponen');
            $table->date('due_date')->index()->comment('Tanggal jatuh tempo');
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])
                ->default('pending')
                ->index();
            $table->timestamps();

            // Tidak boleh ada tagihan dobel untuk bulan yang sama dalam satu kontrak
            $table->unique(['contract_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
