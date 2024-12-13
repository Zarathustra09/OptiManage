<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Cables', 'description' => 'Various types of cables used in telecommunications'],
            ['name' => 'Connectors', 'description' => 'Connectors for telecommunications equipment'],
            ['name' => 'Antennas', 'description' => 'Antennas for signal transmission and reception'],
            ['name' => 'Modems', 'description' => 'Devices for modulating and demodulating signals'],
            ['name' => 'Routers', 'description' => 'Devices for routing data packets between networks'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
