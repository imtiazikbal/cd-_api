<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->merchant()->create()->each(function ($user) {

            $sms_config = json_encode([
                'cancelled' => '1',
                'confirmed' => '1',
                'shipped'   => '1',
                'return'    => '1',
                'delivered' => '1',
                'pending'   => '1',
                'hold_on'   => '1'
            ]);
            $user->shop()->create([
                'user_id'     => $user->id,
                'name'        => 'Loge achi dot com',
                'domain'      => Str::lower(Str::replace(' ', '-', 'Loge achi dot com')),
                'address'     => 'nijer khai nijer pori onner address e bebsa kori',
                'shop_id'     => random_int(111111, 999999),
                'order_sms'   => $sms_config,
                'sms_balance' => 100,
            ]);

            $user->merchantinfo()->create();
        });

        User::factory(200)->clients()->create()->each(function ($user) {

            $sms_config = json_encode([
                'cancelled' => '1',
                'confirmed' => '1',
                'shipped'   => '1',
                'return'    => '1',
                'delivered' => '1',
                'pending'   => '1',
                'hold_on'   => '1'
            ]);
            $user->shop()->create([
                'user_id'     => $user->id,
                'name'        => 'Loge achi dot com',
                'domain'      => Str::lower(Str::replace(' ', '-', 'Loge achi dot com')),
                'address'     => 'nijer khai nijer pori onner address e bebsa kori',
                'shop_id'     => random_int(111111, 999999),
                'order_sms'   => $sms_config,
                'sms_balance' => 100,
            ]);

            $user->merchantinfo()->create();
        });
    }
}
