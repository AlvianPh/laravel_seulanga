<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class ResetTransactionalDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-transactional-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all transactional data (truncate specific tables) and run Room and Tenant seeders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('This will TRUNCATE transactional tables and reset Rooms and Tenants.');
        if (!$this->confirm('Are you sure you want to proceed?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $tables = [
            'payments',
            'invoices',
            'contracts',
            'room_photos',
            'rooms',
            'tenants',
            'expenses',
            'notifications',
        ];

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                DB::table($table)->truncate();
                $this->info("Truncated {$count} rows from table '{$table}'.");
            } else {
                $this->warn("Table '{$table}' does not exist, skipping.");
            }
        }

        Schema::enableForeignKeyConstraints();
        $this->info('Successfully truncated tables.');

        $this->info('Running RoomSeeder...');
        Artisan::call('db:seed', ['--class' => 'RoomSeeder']);
        
        $this->info('Running TenantSeeder...');
        Artisan::call('db:seed', ['--class' => 'TenantSeeder']);

        $this->info('Transactional data reset completed successfully.');
    }
}
