<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $packages = [
            [
                'name'      => 'Startup',
                'min_order' => 0,
                'max_order' => 500,
                'duration'  => 'monthly',
                'price'     => 900,
            ],
            [
                'name'      => 'Business',
                'min_order' => 501,
                'max_order' => 1000,
                'duration'  => 'monthly',
                'price'     => 2000,
            ],
            [
                'name'      => 'Business Plus',
                'min_order' => 1001,
                'max_order' => 1500,
                'duration'  => 'monthly',
                'price'     => 3000,
            ],
            [
                'name'      => 'Entrepreneur',
                'min_order' => 1501,
                'max_order' => 2000,
                'duration'  => 'monthly',
                'price'     => 4000,
            ],
            [
                'name'      => 'Enterprise',
                'min_order' => 2000,
                'max_order' => 2001,
                'duration'  => 'monthly',
                'price'     => 5000,
            ]
        ];

        foreach ($packages as $package) {
            Package::query()->create($package);
        }
    }
}
