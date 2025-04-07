<?php

namespace Database\Seeders;

use App\Models\AdminCourier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class AdminCourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminCourier::query()->create([
            'name' => 'Pathao',
            'courier' => 'pathao',
            'email' => 'example@gmail.com',
            'password' => 'passwordLageNa',
            'config' => null
        ]);

        AdminCourier::query()->create([
            'name' => 'Redx',
            'courier' => 'redx',
            'email' => '01789012345',
            'password' => 'passwordLageNa',
            'config' => null
        ]);

        AdminCourier::query()->create([
            'name' => 'SteadFast',
            'courier' => 'steadfast',
            'email' => 'example@gmail.com',
            'password' => 'passwordLageNa',
            'config' => null
        ]);

        $adminCourier = AdminCourier::query()->get();
        Cache::put('admin_couriers', $adminCourier);
    }
}
