<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $shop = Shop::query()->pluck('shop_id');

        return [
            'shop_id' => $this->faker->randomElement($shop),
            'name'    => $this->faker->unique()->name,
            'slug'    => $this->faker->name,
            'status'  => true,
        ];
    }
}
