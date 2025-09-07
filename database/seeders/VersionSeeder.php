<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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
        // ✅ Always pull latest version from env (APP_VERSION in .env)
        $currentVersion = env('APP_VERSION', null);

        // All known versions up to now
        $newVersionArray = [
            '1.0', '1.1', '1.1.1',
            '1.2', '1.3', '1.3.1', '1.3.2', '1.3.3', '1.3.4',
            '1.4', '1.4.1', '1.4.2', '1.4.3', '1.4.4', '1.4.5',
            '1.4.6', '1.4.7', '1.4.8', '1.4.9',
            '1.5',
            '2.0', '2.1', '2.2',
        ];

        // Add current version dynamically if not already in the list
        if ($currentVersion && !in_array($currentVersion, $newVersionArray)) {
            $newVersionArray[] = $currentVersion;
        }

        // Already inserted versions
        $existingVersions = DB::table('versions')->pluck('version')->toArray();

        foreach ($newVersionArray as $version) {
            if (!in_array($version, $existingVersions)) {
                DB::table('versions')->insert([
                    'version' => $version,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Run version-specific updates
                $this->updateDatabaseTransaction($version);
            }
        }
    }

    /**
     * Call update seeders per version.
     */
    public function updateDatabaseTransaction($version)
    {
        $map = [
            '1.2'   => Version12Seeder::class,
            '1.3.1' => Version131Seeder::class,
            '1.3.2' => Version132Seeder::class,
            '1.3.3' => Version133Seeder::class,
            '1.3.4' => Version134Seeder::class,
            '1.4.1' => Version141Seeder::class,
            '1.4.2' => Version142Seeder::class,
            '1.4.3' => Version143Seeder::class,
            '1.4.4' => Version144Seeder::class,
            '1.4.5' => Version145Seeder::class,
            '1.4.6' => Version146Seeder::class,
            '1.4.7' => Version147Seeder::class,
            '1.4.8' => Version148Seeder::class,
            '1.4.9' => Version149Seeder::class,
            '2.1'   => Version21Seeder::class,
            '2.2'   => Version22Seeder::class,
            '2.3'   => Version23Seeder::class,
        ];

        if (isset($map[$version])) {
            app($map[$version])->run();
        }
    }
}
