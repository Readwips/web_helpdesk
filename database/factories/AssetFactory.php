<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        $cat = AssetCategory::firstOrCreate(['code' => 'LPT'], ['name' => 'Laptop']);

        return ['asset_code' => 'AST-LPT-'.fake()->unique()->numerify('####'), 'asset_category_id' => $cat->id, 'name' => 'Laptop '.fake()->word(), 'brand' => 'Lenovo', 'model' => 'ThinkPad', 'serial_number' => fake()->unique()->bothify('SN-####??'), 'location' => 'Ruang IT', 'condition' => 'baik', 'status' => 'tersedia'];
    }
}
