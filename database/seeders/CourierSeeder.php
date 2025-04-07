<?php

namespace Database\Seeders;

use App\Models\MerchantCourier;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchant = User::query()->pluck('id');

        $shop = Shop::query()->pluck('shop_id');
        MerchantCourier::query()->create([
            'shop_id'     => $shop[0],
            'merchant_id' => $merchant[0],
            'provider'    => MerchantCourier::PATHAO,
            'status'      => MerchantCourier::STATUS_ACTIVE,
            'config'      => json_encode([
                "client_id"     => "VolejoBajN",
                "client_secret" => "qCOik5dZQbYeJxeRy4SBOAR3CuBX4us6JBU6Mo9v",
                "username"      => "mh.neshad39@gmail.com",
                "password"      => "5njb7p1uanfi",
                "grant_type"    => "password",
                "store_id"      => "89295"
            ])
        ]);
    }
}
