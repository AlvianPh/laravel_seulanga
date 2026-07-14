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

        // 1. Tambah kolom expense_category_id (nullable dulu)
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_category_id')->nullable()->after('id');
        });

        // 2. Mapping data enum lama -> id di expense_categories
        $mapping = [
            'electricity' => 'Listrik',
            'water'       => 'Air',
            'internet'    => 'Internet',
            'repair'      => 'Perbaikan',
            'cleaning'    => 'Kebersihan',
            'salary'      => 'Gaji',
            'other'       => 'Lainnya',
        ];

        $migrated = 0;

        foreach ($mapping as $enumValue => $categoryName) {
            $category = DB::table('expense_categories')->where('name', $categoryName)->first();
            if ($category) {
                $updated = DB::table('expenses')
                    ->whereRaw('category = ?', [$enumValue])
                    ->update(['expense_category_id' => $category->id]);
                $migrated += $updated;
            }
        }
        
        echo "\n[expense_category_id migration] Berhasil migrasi {$migrated} baris pengeluaran.\n";

        // 3. Ubah kolom expense_category_id menjadi NOT NULL
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE expenses MODIFY COLUMN expense_category_id BIGINT UNSIGNED NOT NULL');
        } else {
            Schema::table('expenses', function (Blueprint $table) {
                $table->unsignedBigInteger('expense_category_id')->nullable(false)->change();
            });
        }

        // 4. Tambah FK Constraint
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->restrictOnDelete();
        });

        // 5. Drop enum category (dengan fallback dropIndex untuk sqlite jika ada)
        try {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropIndex('expenses_category_index');
            });
        } catch (\Exception $e) {
            // Abaikan jika index tidak ada
        }

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        // Drop FK
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_category_id']);
        });

        // Restore enum
        Schema::table('expenses', function (Blueprint $table) {
            $table->enum('category', ['electricity', 'water', 'internet', 'repair', 'cleaning', 'salary', 'other'])->default('other')->after('id');
        });

        // Restore data
        $mapping = [
            'Listrik'    => 'electricity',
            'Air'        => 'water',
            'Internet'   => 'internet',
            'Perbaikan'  => 'repair',
            'Kebersihan' => 'cleaning',
            'Gaji'       => 'salary',
            'Lainnya'    => 'other',
        ];

        foreach ($mapping as $categoryName => $enumValue) {
            $category = DB::table('expense_categories')->where('name', $categoryName)->first();
            if ($category) {
                DB::table('expenses')
                    ->where('expense_category_id', $category->id)
                    ->update(['category' => $enumValue]);
            }
        }

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('expense_category_id');
        });
    }
};
