<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->index('created_at', 'rooms_created_at_idx');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->index('created_at', 'tenants_created_at_idx');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->index('created_at', 'contracts_created_at_idx');
            $table->index(['status', 'end_date'], 'contracts_status_end_date_idx');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('created_at', 'invoices_created_at_idx');
            $table->index(['status', 'due_date'], 'invoices_status_due_date_idx');
            $table->index(['year', 'month'], 'invoices_year_month_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('created_at', 'payments_created_at_idx');
            $table->index(['status', 'payment_date'], 'payments_status_payment_date_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('created_at', 'expenses_created_at_idx');
            $table->index('expense_category_id', 'expenses_category_id_idx');
            $table->index(['expense_category_id', 'expense_date'], 'expenses_category_date_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index('created_at', 'notifications_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('rooms_created_at_idx');
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex('tenants_created_at_idx');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex('contracts_created_at_idx');
            $table->dropIndex('contracts_status_end_date_idx');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_created_at_idx');
            $table->dropIndex('invoices_status_due_date_idx');
            $table->dropIndex('invoices_year_month_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_created_at_idx');
            $table->dropIndex('payments_status_payment_date_idx');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_created_at_idx');
            $table->dropIndex('expenses_category_id_idx');
            $table->dropIndex('expenses_category_date_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_created_at_idx');
        });
    }
};
