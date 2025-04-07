<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @return void
     */
    public function run()
    {
        $this->call(MerchantSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(ProductTableSeeder::class);
        $this->call(CourierSeeder::class);
        $this->call(OrderTableSeeder::class);
        $this->call(PackageTableSeeder::class);
        $this->call(AttributeTableSeeder::class);
    }
}
