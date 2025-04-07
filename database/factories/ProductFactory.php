<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $shop = Shop::query()->pluck('shop_id');
        $category = Category::query()->pluck('id');
        $deliver_charge = $this->faker->randomElement(['paid', 'free']);
        $inside_dhaka = $deliver_charge === 'free' ? 0 : 60;
        $outside_dhaka = $deliver_charge === 'paid' ? 150 : 0;

        return [
            'shop_id'         => $this->faker->randomElement($shop),
            'category_id'     => $this->faker->randomElement($category),
            'product_name'    => $this->faker->unique()->name,
            'slug'            => Str::slug($this->faker->unique()->name),
            'product_code'    => mt_rand(11111, 99999),
            'product_qty'     => $this->faker->randomDigitNotZero(),
            'price'           => $this->faker->numberBetween(1000, 10000),
            'delivery_charge' => $deliver_charge,
            'inside_dhaka'    => $inside_dhaka,
            'outside_dhaka'   => $outside_dhaka,
        ];
    }
}
