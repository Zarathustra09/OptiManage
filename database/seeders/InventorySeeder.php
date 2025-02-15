<?php

// database/seeders/InventorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Category;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $cablesCategory = Category::where('name', 'Cables')->first();
        $connectorsCategory = Category::where('name', 'Connectors')->first();
        $antennasCategory = Category::where('name', 'Antennas')->first();
        $modemsCategory = Category::where('name', 'Modems')->first();
        $routersCategory = Category::where('name', 'Routers')->first();

        $inventories = [
            ['category_id' => $cablesCategory->id, 'sku' => 'FOC-001', 'name' => 'Fiber Optic Cable', 'quantity' => 100, 'description' => 'High-speed data transmission cable'],
            ['category_id' => $cablesCategory->id, 'sku' => 'COC-002', 'name' => 'Coaxial Cable', 'quantity' => 200, 'description' => 'Cable for transmitting radio frequency signals'],
            ['category_id' => $connectorsCategory->id, 'sku' => 'RJ45-003', 'name' => 'RJ45 Connector', 'quantity' => 500, 'description' => 'Connector for Ethernet cables'],
            ['category_id' => $connectorsCategory->id, 'sku' => 'BNC-004', 'name' => 'BNC Connector', 'quantity' => 300, 'description' => 'Connector for coaxial cables'],
            ['category_id' => $antennasCategory->id, 'sku' => 'YAGI-005', 'name' => 'Yagi Antenna', 'quantity' => 50, 'description' => 'Directional antenna for long-range communication'],
            ['category_id' => $antennasCategory->id, 'sku' => 'OMNI-006', 'name' => 'Omni Antenna', 'quantity' => 75, 'description' => 'Antenna with 360-degree coverage'],
            ['category_id' => $modemsCategory->id, 'sku' => 'DSL-007', 'name' => 'DSL Modem', 'quantity' => 150, 'description' => 'Modem for DSL internet connections'],
            ['category_id' => $modemsCategory->id, 'sku' => 'CABLE-008', 'name' => 'Cable Modem', 'quantity' => 120, 'description' => 'Modem for cable internet connections'],
            ['category_id' => $routersCategory->id, 'sku' => 'WIFI-009', 'name' => 'Wireless Router', 'quantity' => 80, 'description' => 'Router for wireless networking'],
            ['category_id' => $routersCategory->id, 'sku' => 'WIRED-010', 'name' => 'Wired Router', 'quantity' => 60, 'description' => 'Router for wired networking'],
        ];

        foreach ($inventories as $inventory) {
            Inventory::create($inventory);
        }
    }
}
