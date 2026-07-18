<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_barang' => $this->faker->words(3, true),
            'harga_barang' => $this->faker->numberBetween(1000, 1000000),
            'stok_barang' => $this->faker->numberBetween(1, 100),
            'min_stock' => 5,
            'kode_barang' => strtoupper($this->faker->unique()->bothify('SKU-####')),
            'category_id' => Category::factory(),
            'unit_id' => Unit::factory(),
            'supplier_id' => Supplier::factory(),
        ];
    }
}
