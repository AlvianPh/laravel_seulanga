<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel tenants — data diri penghuni kost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index()->comment('Nama lengkap penghuni');
            $table->string('nik', 16)->unique()->comment('Nomor KTP 16 digit');
            $table->string('phone', 20)->index();
            $table->string('email')->nullable()->index();
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable()->comment('Alamat asal');
            $table->string('ktp_photo_path', 500)->nullable()->comment('Path foto KTP');
            $table->string('tenant_photo_path', 500)->nullable()->comment('Path foto penghuni');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
