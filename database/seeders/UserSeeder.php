<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $clientRole = Role::create(['name' => 'client']);
        $staffRole = Role::create(['name' => 'staff']);
        
        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@usahaprimalestari.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'phone' => '+62 21 5678 9012',
            'is_active' => true,
        ]);
        
        $admin->assignRole($adminRole);
        
        // Create staff users
        $staff1 = User::create([
            'name' => 'Staff Member 1',
            'email' => 'staff1@usahaprimalestari.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'phone' => '+62 21 5678 9013',
            'is_active' => true,
        ]);
        
        $staff1->assignRole($staffRole);
        
        $staff2 = User::create([
            'name' => 'Staff Member 2',
            'email' => 'staff2@usahaprimalestari.com',
            'password' => bcrypt('P@ssword123'),
            'email_verified_at' => now(),
            'phone' => '+62 21 5678 9014',
            'is_active' => true,
        ]);
        
        $staff2->assignRole($staffRole);
        
        // Create client users
        $client1 = User::create([
            'name' => 'PT Maju Bersama',
            'email' => 'client1@example.com',
            'password' => bcrypt('P@ssword123'),
            'email_verified_at' => now(),
            'phone' => '+62 21 3456 7890',
            'company' => 'PT Maju Bersama',
            'address' => 'Jl. Sudirman No. 123, Jakarta',
            'city' => 'Jakarta',
            'state' => 'DKI Jakarta',
            'postal_code' => '12190',
            'country' => 'Indonesia',
            'is_active' => true,
        ]);
        
        $client1->assignRole($clientRole);
        
        $client2 = User::create([
            'name' => 'PT Harmoni Sentosa',
            'email' => 'client2@example.com',
            'password' => bcrypt('P@ssword123'),
            'email_verified_at' => now(),
            'phone' => '+62 21 2345 6789',
            'company' => 'PT Harmoni Sentosa',
            'address' => 'Jl. Gatot Subroto No. 456, Jakarta',
            'city' => 'Jakarta',
            'state' => 'DKI Jakarta',
            'postal_code' => '12930',
            'country' => 'Indonesia',
            'is_active' => true,
        ]);
        
        $client2->assignRole($clientRole);
        
        $client3 = User::create([
            'name' => 'CV Karya Makmur',
            'email' => 'client3@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'phone' => '+62 21 4567 8901',
            'company' => 'CV Karya Makmur',
            'address' => 'Jl. Thamrin No. 789, Jakarta',
            'city' => 'Jakarta',
            'state' => 'DKI Jakarta',
            'postal_code' => '10230',
            'country' => 'Indonesia',
            'is_active' => true,
        ]);
        
        $client3->assignRole($clientRole);
        
        // Create some unverified clients
        $client4 = User::create([
            'name' => 'PT Sejahtera Abadi',
            'email' => 'client4@example.com',
            'password' => bcrypt('P@ssword123'),
            'phone' => '+62 21 7890 1234',
            'company' => 'PT Sejahtera Abadi',
            'address' => 'Jl. Asia Afrika No. 101, Bandung',
            'city' => 'Bandung',
            'state' => 'Jawa Barat',
            'postal_code' => '40112',
            'country' => 'Indonesia',
            'is_active' => false,
        ]);
        
        $client4->assignRole($clientRole);
        
        // Create additional regular users for testing
        User::factory(5)->create()->each(function ($user) use ($clientRole) {
            $user->assignRole($clientRole);
        });
    }
}