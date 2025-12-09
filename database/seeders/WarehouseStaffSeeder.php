<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class WarehouseStaffSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Warehouse Staff',
            'email' => 'warehouse@inventory.com',
            'password' => Hash::make('password'),
            'role' => 'warehouse_staff',
            'is_active' => true,
        ]);
    }
}