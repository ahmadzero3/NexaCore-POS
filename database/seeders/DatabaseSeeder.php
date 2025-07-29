<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try{
            $hasDataInAppSettings = DB::table('app_settings')->count();

            if(!$hasDataInAppSettings){
                // Create instances of the seeder classes & call its method run()
                $adminSeeder = new AdminSeeder();
                $adminSeeder->run();

                $rolesAndPermissionsSeeder = new RolesAndPermissionsSeeder();
                $rolesAndPermissionsSeeder->run();

                $roleSeeder = new RoleSeeder();
                $roleSeeder->run();

                $languageSeeder = new LanguageSeeder();
                $languageSeeder->run();

                $settingsSeeder = new SettingsSeeder();
                $settingsSeeder->run();

                $companySeeder = new CompanySeeder();
                $companySeeder->run();

                $prefixSeeder = new PrefixSeeder();
                $prefixSeeder->run();

                $smsTemplatesSeeder = new SmsTemplatesSeeder();
                $smsTemplatesSeeder->run();

                $emailTemplatesSeeder = new EmailTemplatesSeeder();
                $emailTemplatesSeeder->run();

                $accountGroupSeeder = new AccountGroupSeeder();
                $accountGroupSeeder->run();

                $paymentTypesSeeder = new PaymentTypesSeeder();
                $paymentTypesSeeder->run();

                $taxSeeder = new TaxSeeder();
                $taxSeeder->run();

                $itemCategorySeeder = new ItemCategorySeeder();
                $itemCategorySeeder->run();

                $unitSeeder = new UnitSeeder();
                $unitSeeder->run();

                $warehouseSeeder = new WarehouseSeeder();
                $warehouseSeeder->run();

                $statesSeeder = new StatesSeeder();
                $statesSeeder->run();
            }

            //Call UpdateSeeder
            $adminSeeder = new UpdateSeeder();
            $adminSeeder->run();

            DB::commit();

            echo "Seeding Completed!!";

             // Run the permission cache clear command
            Artisan::call('config:clear');

        } catch (\Exception $e) {
                echo "Error: ".$e->getMessage();
                throw $e;
                DB::rollback();
        }


    }
}
