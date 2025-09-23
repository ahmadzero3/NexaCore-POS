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
use Database\Seeders\Updates\Version235Seeder;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Always pull APP_VERSION (ignore if null/empty)
        $currentVersion = env('APP_VERSION', null);

        // All known versions with release notes
        $newVersionArray = [
            '1.0',    // 1.0, Date: 24-10-2024
            '1.1',    // 1.1, Date: 28-11-2024
            '1.1.1',  // 1.1.1, Date: 30-11-2024
            '1.2',    // 1.2, Date: 07-12-2024
            '1.3',    // 1.3, Date: 17-12-2024
            '1.3.1',  // 1.3.1, Date: 22-12-2024
            '1.3.2',  // 1.3.2, Date: 24-12-2024
            '1.3.3',  // 1.3.3, Date: 28-12-2024
            '1.3.4',  // 1.3.4, Date: 31-12-2024
            '1.4',    // 1.4, Date: 01-01-2025
            '1.4.1',  // 1.4.1, Date: 09-01-2025
            '1.4.2',  // 1.4.2, Date: 13-01-2025
            '1.4.3',  // 1.4.3, Date: 14-01-2025
            '1.4.4',  // 1.4.4, Date: 18-01-2025
            '1.4.5',  // 1.4.5, Date: 18-01-2025
            '1.4.6',  // 1.4.6, Date: 31-01-2025
            '1.4.7',  // 1.4.7, Date: 31-01-2025
            '1.4.8',  // 1.4.8, Date: 03-02-2025
            '1.4.9',  // 1.4.9, Date: 12-02-2025
            '1.5',    // 1.5, Date: 14-02-2025
            '2.0',    // 2.0, Date: 21-02-2025
            '2.1',    // 2.1, Date: 25-02-2025
            '2.2',    // 2.2, Date: 06-03-2025
            '2.3',    // 2.3, Date: 08-03-2025
            '2.3.5',  // 2.3.5, Date: 19-09-2025
        ];

        // Add current version dynamically (only if defined + not duplicate)
        if (!empty($currentVersion) && !in_array($currentVersion, $newVersionArray)) {
            $newVersionArray[] = $currentVersion;
        }

        // Already inserted versions
        $existingVersions = DB::table('versions')->pluck('version')->toArray();

        foreach ($newVersionArray as $version) {
            if (!in_array($version, $existingVersions)) {
                DB::table('versions')->insert([
                    'version'    => $version,
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
            '2.3.5' => Version235Seeder::class,
        ];

        if (isset($map[$version])) {
            app($map[$version])->run();
        }
    }
}
