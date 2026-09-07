<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('status', 30)->default('active')->change();
            $table->foreignId('application_id')
                ->nullable()
                ->after('created_by')
                ->constrained('tenant_applications')
                ->nullOnDelete();
            $table->timestamp('agreement_accepted_at')->nullable()->after('application_id');
            $table->foreignId('agreement_accepted_by')
                ->nullable()
                ->after('agreement_accepted_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('agreement_version', 20)->nullable()->default('v1.0')->after('agreement_accepted_by');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropForeign(['agreement_accepted_by']);
            $table->dropColumn([
                'application_id',
                'agreement_accepted_at',
                'agreement_accepted_by',
                'agreement_version',
            ]);
        });
    }
};
