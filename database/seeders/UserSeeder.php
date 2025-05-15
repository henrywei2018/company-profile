<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    \App\Models\User::create([
        'name' => 'Super Admin',
        'email' => 'usahaprimalestari@gmail.com',
        'password' => bcrypt('Sup@dmin2025!@#'), // Never store plain text passwords
    ]);
}

}
