<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Senior Quality Analyst',
                'slug' => 'senior-quality-analyst',
            ],
            [
                'name' => 'Senior Data Analyst',
                'slug' => 'senior-data-analyst',
            ],
            [
                'name' => 'Senior Web Developer',
                'slug' => 'senior-web-developer',
            ],
            [
                'name' => 'Inside Sales Head',
                'slug' => 'inside-sales-head',
            ],
            [
                'name' => 'Hub Manager',
                'slug' => 'hub-manager',
            ],
        ];

        foreach ($roles as $role) {
            Role::query()->create([
                'name' => $role['name'],
                'slug' => $role['slug'],
            ]);
        }
    }
}
