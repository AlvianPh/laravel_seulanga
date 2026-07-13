<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel expenses — pengeluaran operasional kost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->enum('category', [
                'electricity', 'water', 'internet',
                'repair', 'cleaning', 'salary', 'other',
            ])->index();
            $table->string('description', 500)->comment('Keterangan detail pengeluaran');
            $table->decimal('amount', 12, 2)->comment('Nominal pengeluaran');
            $table->date('expense_date')->index()->comment('Tanggal pengeluaran');
            $table->string('receipt_path', 500)->nullable()->comment('Path foto struk/bukti');
            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
