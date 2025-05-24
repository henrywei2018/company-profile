<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@usahaprimaestari.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+62 21 1234567',
            'company' => 'CV Usaha Prima Lestari',
            'address' => 'Jakarta, Indonesia',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'is_active' => true,
        ]);
        $superAdmin->assignRole('super-admin');

        // Admin based on the Excel data
        $admin1 = User::create([
            'name' => 'Arif Sudarwan',
            'email' => 'sudarwanarif@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+62 812 3456789',
            'company' => 'CV Usaha Prima Lestari',
            'address' => 'Jakarta, Indonesia',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'is_active' => true,
        ]);
        $admin1->assignRole('admin');

        $admin2 = User::create([
            'name' => 'Robinson Totong',
            'email' => 'robinsonjuventino@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+62 813 4567890',
            'company' => 'CV Usaha Prima Lestari',
            'address' => 'Jakarta, Indonesia',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'is_active' => true,
        ]);
        $admin2->assignRole('admin');

        // Editor
        $editor = User::create([
            'name' => 'Junaidi',
            'email' => 'junaidi01091983@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+62 814 5678901',
            'company' => 'CV Usaha Prima Lestari',
            'address' => 'Jakarta, Indonesia',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'is_active' => true,
        ]);
        $editor->assignRole('editor');

        // Sample clients
        $client1 = User::create([
            'name' => 'PT Maju Bersama',
            'email' => 'info@majubersama.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+62 21 9876543',
            'company' => 'PT Maju Bersama',
            'address' => 'Jl. Sudirman No. 123, Jakarta',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'is_active' => true,
        ]);
        $client1->assignRole('client');

        $client2 = User::create([
            'name' => 'CV Berkah Sejahtera',
            'email' => 'contact@berkahsejahtera.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+62 22 1234567',
            'company' => 'CV Berkah Sejahtera',
            'address' => 'Jl. Asia Afrika No. 456, Bandung',
            'city' => 'Bandung',
            'country' => 'Indonesia',
            'is_active' => true,
        ]);
        $client2->assignRole('client');
    }
}