<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Core Administrators and Staff
        User::firstOrCreate(
            ['email' => 'admin@stock.com'],
            [
                'name' => 'Admin StockMaster',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@stock.com'],
            [
                'name' => 'Staff Warehouse',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        // 2. Create Specialized Roles for Enterprise Module
        User::firstOrCreate(
            ['email' => 'finance@stockmaster.com'],
            [
                'name' => 'Finance Manager',
                'password' => Hash::make('password'),
                'role' => 'finance'
            ]
        );

        User::firstOrCreate(
            ['email' => 'warehouse@stockmaster.com'],
            [
                'name' => 'Warehouse Lead',
                'password' => Hash::make('password'),
                'role' => 'warehouse'
            ]
        );

        // 3. Create Customer Classifications
        $retail = CustomerCategory::firstOrCreate(['name' => 'Retail'], ['discount_percent' => 0]);
        $wholesale = CustomerCategory::firstOrCreate(['name' => 'Wholesale'], ['discount_percent' => 15]);
        $vip = CustomerCategory::firstOrCreate(['name' => 'VIP'], ['discount_percent' => 25]);

        // 4. Create Core B2B Customers
        Customer::firstOrCreate(
            ['email' => 'contact@indomart.com'],
            [
                'customer_category_id' => $retail->id,
                'name' => 'IndoMart Retail',
                'phone' => '021-555-1234'
            ]
        );

        Customer::firstOrCreate(
            ['email' => 'sales@globaltrading.com'],
            [
                'customer_category_id' => $wholesale->id,
                'name' => 'Global Trading Corp',
                'phone' => '021-999-8888'
            ]
        );

        // 5. Call Zenith Realistic Seeder for Catalog, Batches, and Transactions
        $this->call(ZenithRealisticSeeder::class);
    }
}
