<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // 1. Tambah kolom payment_method_id (nullable dulu)
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('payment_date');
        });

        // 2. Mapping data enum lama -> id di payment_methods
        $mapping = [
            'cash'     => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris'     => 'QRIS',
            'other'    => 'Lainnya',
        ];

        $migrated = 0;

        foreach ($mapping as $enumValue => $methodName) {
            $method = DB::table('payment_methods')->where('name', $methodName)->first();
            if ($method) {
                $updated = DB::table('payments')
                    ->whereRaw('method = ?', [$enumValue])
                    ->update(['payment_method_id' => $method->id]);
                $migrated += $updated;
            }
        }
        
        echo "\n[payment_method_id migration] Berhasil migrasi {$migrated} baris pembayaran.\n";

        // 3. Ubah kolom payment_method_id menjadi NOT NULL
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE payments MODIFY COLUMN payment_method_id BIGINT UNSIGNED NOT NULL');
        } else {
            Schema::table('payments', function (Blueprint $table) {
                $table->unsignedBigInteger('payment_method_id')->nullable(false)->change();
            });
        }

        // 4. Tambah FK Constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->restrictOnDelete();
        });

        // 5. Drop enum method (dengan fallback dropIndex untuk sqlite jika ada)
        try {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex('payments_method_index');
            });
        } catch (\Exception $e) {
            // Abaikan jika index tidak ada
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('method');
        });
    }

    public function down(): void
    {
        // Drop FK
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
        });

        // Restore enum
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('method', ['cash', 'transfer', 'qris', 'other'])->default('cash')->after('payment_date');
        });

        // Restore data
        $mapping = [
            'Tunai'         => 'cash',
            'Transfer Bank' => 'transfer',
            'QRIS'          => 'qris',
            'Lainnya'       => 'other',
        ];

        foreach ($mapping as $methodName => $enumValue) {
            $method = DB::table('payment_methods')->where('name', $methodName)->first();
            if ($method) {
                DB::table('payments')
                    ->where('payment_method_id', $method->id)
                    ->update(['method' => $enumValue]);
            }
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_method_id');
        });
    }
};
