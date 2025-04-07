<?php

namespace Database\Seeders;

use App\Models\AdminCourier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class NewAdminCourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminCourier::query()->create([
            'name' => 'SteadFastCourier',
            'courier' => 'steadfastcourier',
            'email' => 'example@gmail.com',
            'password' => 'passwordLageNa',
            'config' => null
        ]);

        $adminCourier = AdminCourier::query()->get();
        Cache::put('admin_couriers', $adminCourier);
    }
}
