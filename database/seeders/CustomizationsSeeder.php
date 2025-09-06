<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomizationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('customizations')->insert([
            [
                'id'    => 1,
                'key'   => 'toggle_switch',
                'value' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'    => 2,
                'key'   => 'card_header_color',
                'value' => '#f5f5f5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'    => 3,
                'key'   => 'card_border_color',
                'value' => '#ffffff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'    => 4,
                'key'   => 'heading_color',
                'value' => '#000000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
