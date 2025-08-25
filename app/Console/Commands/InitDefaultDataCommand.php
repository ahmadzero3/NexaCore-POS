<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\DatabaseSeeder;
use Exception;

class InitDefaultDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:default-data {--force : Force initialization even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the database with default data (same as installation process)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Initializing NexaCore POS with default data...');
        $this->info('================================================');

        try {
            // Check if data already exists
            if (!$this->option('force')) {
                $hasData = DB::table('app_settings')->count();
                if ($hasData > 0) {
                    if (!$this->confirm('Default data already exists. Do you want to continue? This may duplicate data.')) {
                        $this->warn('Initialization cancelled.');
                        return 1;
                    }
                }
            }

            // Run migrations first
            $this->info('🔄 Running database migrations...');
            Artisan::call('migrate', ['--force' => true]);
            $this->info('✅ Migrations completed successfully');

            // Run the main database seeder
            $this->info('🌱 Seeding database with default data...');
            $seeder = new DatabaseSeeder();
            $seeder->run();
            $this->info('✅ Database seeding completed successfully');

            // Clear application cache
            $this->info('🧹 Clearing application cache...');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            $this->info('✅ Cache cleared successfully');

            // Installation status is already set in .env file
            $this->info('📝 Installation status is configured in .env file');

            // Clear any Laravel console sessions
            $this->info('🧹 Clearing Laravel console sessions...');
            Artisan::call('tinker', ['--execute' => 'exit;']);
            $this->info('✅ Console sessions cleared');

            $this->newLine();
            $this->info('🎉 Default data initialization completed successfully!');
            $this->info('================================================');
            $this->info('📋 The following default data has been created:');
            $this->line('   • Admin user (admin@example.com / 12345678)');
            $this->line('   • Default roles and permissions');
            $this->line('   • Application settings');
            $this->line('   • Company information');
            $this->line('   • Default language (English)');
            $this->line('   • SMS and Email templates');
            $this->line('   • Account groups and payment types');
            $this->line('   • Default tax settings');
            $this->line('   • Item categories and units');
            $this->line('   • Default warehouse (Main)');
            $this->line('   • State/region data');
            $this->line('   • Version information');
            $this->newLine();
            $this->info('🔑 Login with: admin@example.com / 12345678');

            return 0;

        } catch (Exception $e) {
            $this->error('❌ Initialization failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
