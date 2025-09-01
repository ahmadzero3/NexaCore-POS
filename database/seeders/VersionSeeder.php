<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Updates\Version12Seeder;
use Database\Seeders\Updates\Version131Seeder;
use Database\Seeders\Updates\Version132Seeder;
use Database\Seeders\Updates\Version133Seeder;
use Database\Seeders\Updates\Version134Seeder;
use Database\Seeders\Updates\Version141Seeder;
use Database\Seeders\Updates\Version142Seeder;
use Database\Seeders\Updates\Version143Seeder;
use Database\Seeders\Updates\Version144Seeder;
use Database\Seeders\Updates\Version145Seeder;
use Database\Seeders\Updates\Version146Seeder;
use Database\Seeders\Updates\Version147Seeder;
use Database\Seeders\Updates\Version148Seeder;
use Database\Seeders\Updates\Version149Seeder;
use Database\Seeders\Updates\Version21Seeder;
use Database\Seeders\Updates\Version22Seeder;
use Database\Seeders\Updates\Version23Seeder;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $newVersionArray = [
            '1.0',  //1.0, Date: 24-10-2024
        ];

        $existingVersions = DB::table('versions')->pluck('version')->toArray();

        foreach ($newVersionArray as $version) {
            //validate is the version exist in it?
            if (!in_array($version, $existingVersions)) {
                DB::table('versions')->insert([
                    'version' => $version,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                /**
                 * Version wise any seeder updates
                 * */
                $this->updateDatabaseTransaction($version);
            }
        }
    }

    public function updateDatabaseTransaction($version)
    {
        if ($version == '1.2') {
            $adminSeeder = new Version12Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.3.1') {
            $adminSeeder = new Version131Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.3.2') {
            $adminSeeder = new Version132Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.3.3') {
            $adminSeeder = new Version133Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.3.4') {
            $adminSeeder = new Version134Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.4.1') {
            $adminSeeder = new Version141Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.4.2') {
            $adminSeeder = new Version142Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.4.3') {
            $adminSeeder = new Version143Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.4.4') {
            $adminSeeder = new Version144Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.4.5') {
            $adminSeeder = new Version145Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.4.6') {
            $adminSeeder = new Version146Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.4.7') {
            $adminSeeder = new Version147Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.4.8') {
            $adminSeeder = new Version148Seeder();
            $adminSeeder->run();
        }
        if ($version == '1.4.9') {
            $adminSeeder = new Version149Seeder();
            $adminSeeder->run();
        }
        if ($version == '2.1') {
            $adminSeeder = new Version21Seeder();
            $adminSeeder->run();
        }
        if ($version == '2.2') {
            $adminSeeder = new Version22Seeder();
            $adminSeeder->run();
        }
        if ($version == '2.3') {
            $adminSeeder = new Version23Seeder();
            $adminSeeder->run();
        }
    }
}
