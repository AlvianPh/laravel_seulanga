<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel contracts — perjanjian sewa antara penghuni dan kamar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->restrictOnDelete();
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->restrictOnDelete();
            $table->date('start_date')->index()->comment('Tanggal masuk penghuni');
            $table->date('end_date')->index()->comment('Tanggal keluar rencana');
            $table->decimal('rent_price', 12, 2)->comment('Snapshot harga sewa saat kontrak dibuat');
            $table->decimal('deposit_amount', 12, 2)->comment('Nominal deposit yang dibayar');
            $table->enum('status', ['active', 'ended', 'terminated'])
                ->default('active')
                ->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamps();

            // Unique: satu penghuni tidak bisa punya 2 kontrak di kamar yang sama dengan tanggal masuk sama
            $table->unique(['tenant_id', 'room_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
