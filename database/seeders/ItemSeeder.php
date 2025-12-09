<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemLocation;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'item_code' => 'ITEM-001',
                'name' => 'Laptop Dell Inspiron',
                'description' => 'Laptop untuk keperluan kantor',
                'current_stock' => 15,
                'minimum_stock' => 5,
                'unit' => 'pcs',
                'category' => 'Electronics',
               
            ],
            [
                'item_code' => 'ITEM-002',
                'name' => 'Mouse Wireless',
                'description' => 'Mouse wireless untuk komputer',
                'current_stock' => 50,
                'minimum_stock' => 10,
                'unit' => 'pcs',
                'category' => 'Accessories',
               
            ],
            [
                'item_code' => 'ITEM-003',
                'name' => 'Keyboard Mechanical',
                'description' => 'Keyboard mechanical gaming',
                'current_stock' => 25,
                'minimum_stock' => 8,
                'unit' => 'pcs',
                'category' => 'Accessories',
                
            ],
            [
                'item_code' => 'ITEM-004',
                'name' => 'Monitor 24 inch',
                'description' => 'Monitor LED 24 inch Full HD',
                'current_stock' => 12,
                'minimum_stock' => 3,
                'unit' => 'pcs',
                'category' => 'Electronics',
                
            ],
            [
                'item_code' => 'ITEM-005',
                'name' => 'Kabel HDMI',
                'description' => 'Kabel HDMI 2 meter',
                'current_stock' => 100,
                'minimum_stock' => 20,
                'unit' => 'pcs',
                'category' => 'Cables',
               
            ],
        ];

        foreach ($items as $itemData) {
            $item = Item::create($itemData);
            
            // Create default location for each item
            ItemLocation::create([
                'item_id' => $item->id,
                'location_name' => 'Gudang Utama',
                'quantity' => $item->current_stock,
            ]);
        }
    }
}