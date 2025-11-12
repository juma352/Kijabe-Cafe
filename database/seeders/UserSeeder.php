<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@kijabe.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Kitchen Manager User
        User::create([
            'name' => 'Kitchen Manager',
            'email' => 'kitchen@kijabe.com',
            'password' => Hash::make('password123'),
            'role' => 'kitchen_manager',
            'email_verified_at' => now(),
        ]);

        // Create Cashier User
        User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@kijabe.com',
            'password' => Hash::make('password123'),
            'role' => 'cashier',
            'email_verified_at' => now(),
        ]);

        echo "Test users created:\n";
        echo "Admin: admin@kijabe.com / password123\n";
        echo "Kitchen Manager: kitchen@kijabe.com / password123\n";
        echo "Cashier: cashier@kijabe.com / password123\n";
    }
}
