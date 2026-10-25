<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Demo Admin, Staff & Customer
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Toko',
                'email' => 'admin@inventory.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Staff Gudang & Kasir',
                'email' => 'staff@inventory.test',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
            ]
        );

        User::updateOrCreate(
            ['username' => 'customer'],
            [
                'name' => 'Pelanggan Toko (Customer)',
                'email' => 'customer@inventory.test',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
            ]
        );

        // 2. Master Data
        $this->call([
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
