<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel room_photos — foto-foto kamar kost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->cascadeOnDelete();
            $table->string('file_path', 500)->comment('Path file di storage');
            $table->boolean('is_primary')->default(false)->comment('Foto utama kamar');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_photos');
    }
};
