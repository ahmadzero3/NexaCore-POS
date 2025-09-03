<?php

namespace Database\Seeders;

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
    public function run(): void
    {
        // Ensure the versions table has at least one entry
        DB::table('versions')->insert([
            'version' => '1.0.0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $newVersionArray = [
            '1.0','1.1','1.1.1','1.2','1.3','1.3.1','1.3.2','1.3.3','1.3.4',
            '1.4','1.4.1','1.4.2','1.4.3','1.4.4','1.4.5','1.4.6','1.4.7',
            '1.4.8','1.4.9','1.5','2.0','2.1','2.2',
            env('APP_VERSION','2.3'),
        ];

        $existingVersions = DB::table('versions')->pluck('version')->toArray();

        foreach ($newVersionArray as $version) {
            if (!in_array($version, $existingVersions)) {
                DB::table('versions')->insert([
                    'version' => $version,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->updateDatabaseTransaction($version);
            }
        }
    }

    public function updateDatabaseTransaction($version)
    {
        if ($version == '1.2') { (new Version12Seeder())->run(); }
        if ($version == '1.3.1') { (new Version131Seeder())->run(); }
        if ($version == '1.3.2') { (new Version132Seeder())->run(); }
        if ($version == '1.3.3') { (new Version133Seeder())->run(); }
        if ($version == '1.3.4') { (new Version134Seeder())->run(); }
        if ($version == '1.4.1') { (new Version141Seeder())->run(); }
        if ($version == '1.4.2') { (new Version142Seeder())->run(); }
        if ($version == '1.4.3') { (new Version143Seeder())->run(); }
        if ($version == '1.4.4') { (new Version144Seeder())->run(); }
        if ($version == '1.4.5') { (new Version145Seeder())->run(); }
        if ($version == '1.4.6') { (new Version146Seeder())->run(); }
        if ($version == '1.4.7') { (new Version147Seeder())->run(); }
        if ($version == '1.4.8') { (new Version148Seeder())->run(); }
        if ($version == '1.4.9') { (new Version149Seeder())->run(); }
        if ($version == '2.1') { (new Version21Seeder())->run(); }
        if ($version == '2.2') { (new Version22Seeder())->run(); }
        if ($version == '2.3') { (new Version23Seeder())->run(); }
    }
}
