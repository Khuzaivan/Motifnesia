<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // 1. Existing Legacy Admin
        User::updateOrCreate(
            ['email' => 'admin@motifnesia.com'],
            [
                'name' => 'admin',
                'full_name' => 'Admin Motifnesia',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'admin_role' => 'owner',
            ]
        );

        // 2. Demo Owner
        User::updateOrCreate(
            ['email' => 'owner@motifnesia.com'],
            [
                'name' => 'owner',
                'full_name' => 'Owner Motifnesia',
                'password' => Hash::make('owner123'),
                'role' => 'admin',
                'admin_role' => 'owner',
            ]
        );

        // 3. Demo Finance
        User::updateOrCreate(
            ['email' => 'finance@motifnesia.com'],
            [
                'name' => 'finance',
                'full_name' => 'Finance Motifnesia',
                'password' => Hash::make('finance123'),
                'role' => 'admin',
                'admin_role' => 'finance',
            ]
        );

        // 4. Demo Kasir
        User::updateOrCreate(
            ['email' => 'kasir@motifnesia.com'],
            [
                'name' => 'kasir',
                'full_name' => 'Kasir Motifnesia',
                'password' => Hash::make('kasir123'),
                'role' => 'admin',
                'admin_role' => 'kasir',
            ]
        );

        // 5. Demo Gudang
        User::updateOrCreate(
            ['email' => 'gudang@motifnesia.com'],
            [
                'name' => 'gudang',
                'full_name' => 'Gudang Motifnesia',
                'password' => Hash::make('gudang123'),
                'role' => 'admin',
                'admin_role' => 'gudang',
            ]
        );

        // 6. Customer abay
        User::updateOrCreate(
            ['email' => 'abay@gmail.com'],
            [
                'name' => 'abay',
                'full_name' => 'Muhammad Abyaz Zaydan',
                'password' => Hash::make('123456'),
                'role' => 'customer',
            ]
        );
    }
}
