<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Konversi kolom type (ENUM) di tabel rooms menjadi room_type_id (FK ke room_types).
 * Data yang ada dipetakan: 'standard' → 'Standard', 'deluxe' → 'Deluxe', 'suite' → 'Suite'.
 * Tidak ada data kamar yang kehilangan tipenya.
 * Kompatibel dengan MySQL dan SQLite (untuk test).
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Langkah 1: Tambah kolom room_type_id (nullable dulu)
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('room_type_id')->nullable()->after('floor');
        });

        // Langkah 2: Mapping ENUM lama → row di room_types
        $mapping = [
            'standard' => 'Standard',
            'deluxe'   => 'Deluxe',
            'suite'    => 'Suite',
        ];

        $migrated = 0;

        foreach ($mapping as $enumValue => $typeName) {
            $roomType = DB::table('room_types')->where('name', $typeName)->first();

            if ($roomType) {
                $updated = DB::table('rooms')
                    ->whereRaw('type = ?', [$enumValue])
                    ->update(['room_type_id' => $roomType->id]);

                $migrated += $updated;
            }
        }

        echo "\n[room_type_id migration] Berhasil migrasi {$migrated} baris kamar.\n";

        // Langkah 3: Ubah kolom menjadi NOT NULL (cara cross-database via Blueprint change)
        // Pada SQLite, ->change() diimplementasikan berbeda (rebuild table)
        // Pada MySQL, gunakan raw ALTER TABLE untuk menghindari konflik nullOnDelete FK
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE rooms MODIFY COLUMN room_type_id BIGINT UNSIGNED NOT NULL');
        } else {
            // SQLite: gunakan Blueprint change() — kompatibel dengan SQLite via doctrine/dbal
            Schema::table('rooms', function (Blueprint $table) {
                $table->unsignedBigInteger('room_type_id')->nullable(false)->change();
            });
        }

        // Langkah 4: Tambah FK constraint (SQLite tidak mendukung FK enforcement by default, tapi Blueprint tetap bekerja)
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreign('room_type_id')->references('id')->on('room_types')->restrictOnDelete();
        });

        // Langkah 5: Drop index 'type' (jika ada), lalu drop kolom type (ENUM lama)
        // Dibungkus try-catch karena SQLite membutuhkan index dihapus sebelum drop column,
        // sementara index mungkin tidak ada di semua environment.
        try {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropIndex('rooms_type_index');
            });
        } catch (\Exception $e) {
            // Index mungkin tidak ada — abaikan
        }

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        // Hapus FK terlebih dahulu
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['room_type_id']);
        });

        // Kembalikan kolom type (ENUM lama)
        Schema::table('rooms', function (Blueprint $table) {
            $table->enum('type', ['standard', 'deluxe', 'suite'])->default('standard')->after('floor');
        });

        // Kembalikan data dari room_type_id ke type
        $mapping = [
            'Standard' => 'standard',
            'Deluxe'   => 'deluxe',
            'Suite'    => 'suite',
        ];

        foreach ($mapping as $typeName => $enumValue) {
            $roomType = DB::table('room_types')->where('name', $typeName)->first();
            if ($roomType) {
                DB::table('rooms')
                    ->where('room_type_id', $roomType->id)
                    ->update(['type' => $enumValue]);
            }
        }

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('room_type_id');
        });
    }
};
