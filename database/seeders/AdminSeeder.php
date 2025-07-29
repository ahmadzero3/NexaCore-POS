<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
                                    'id' => 1,
                                    'username' => 'admin',
                                    'first_name' => 'Super',
                                    'last_name' => 'Human',
                                    'email' => 'admin@example.com',
                                    'password' => Hash::make('12345678'),
                                    'status' => 1,
                                ]);
    }
}
