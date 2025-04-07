<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Seeder;
use Faker;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $shops = Shop::query()->pluck('shop_id');

        foreach ($shops as $shop) {
            $products = Product::query()->where('shop_id', $shop)->get();

            foreach ($products as $product) {

                $order = Order::query()->create([
                    'order_no'      => random_int(100, 9999),
                    'shop_id'       => $shop,
                    'address'       => $faker->address(),
                    'phone'         => $faker->phoneNumber,
                    'order_type'    => 'social',
                    'customer_name' => $faker->name(),
                    'created_at'    => Carbon::parse('2023-08-25')->toDateTimeString()
                ]);

                $order->order_details()->create([
                    'product_id'    => $product->id,
                    'product_qty'   => 4,
                    'unit_price'    => $product->price,
                    'shipping_cost' => 120,
                ]);

                $order->pricing()->create([
                    'shipping_cost' => 120,
                    'grand_total'   => $product->price * 4,
                    'due'           => ($product->price * 4) + 120,
                ]);

                $order->config()->create();
                $order->courier()->create();
            }
        }
    }
}
