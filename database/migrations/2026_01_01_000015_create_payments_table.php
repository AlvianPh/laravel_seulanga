<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel payments — pencatatan pembayaran tagihan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->restrictOnDelete();
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->restrictOnDelete()
                ->comment('Denormalized untuk query riwayat pembayaran');
            $table->decimal('amount', 12, 2)->comment('Nominal yang dibayar');
            $table->date('payment_date')->index()->comment('Tanggal pembayaran');
            $table->enum('method', ['cash', 'transfer', 'qris', 'other']);
            $table->enum('status', ['pending', 'verified', 'rejected'])
                ->default('pending')
                ->index();
            $table->string('proof_path', 500)->nullable()->comment('Path bukti transfer di storage');
            $table->text('notes')->nullable();
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User yang memverifikasi pembayaran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
