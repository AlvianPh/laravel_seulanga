<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration menambahkan status partially_paid pada tabel invoices.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status', 30)
                ->default('pending')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }
};
