<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name'              => 'Super Admin',
            'email'             => 'admin@gmail.com',
            'phone'             => '+8801800000000',
            'role'              => 'admin',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'password'          => '123456789', // password
            'remember_token'    => Str::random(10),
            'created_at'        => Carbon::parse('2023-08-09')->toDateTimeString(),

        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return Factory
     */
    public function merchant(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name'          => $this->faker->name,
                'email'         => 'mdh0674@gmail.com',
                'phone'         => '+8801314177216',
                'role'          => 'merchant',
                'next_due_date' => Carbon::parse('2023-08-09')->addDays(30)->toDateTimeString()
            ];
        });
    }

    public function clients(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name'          => $this->faker->name,
                'email'         => $this->faker->email,
                'phone'         => $this->faker->phoneNumber,
                'role'          => 'merchant',
                'next_due_date' => Carbon::parse('2023-08-09')->addDays(30)->toDateTimeString()
            ];
        });
    }

    public function customer(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name'  => $this->faker->name,
                'email' => 'customer' . random_int(1, 99999) . '@gmail.com',
                'phone' => $this->faker->unique()->phoneNumber(),
                'role'  => 'customer',
            ];
        });
    }

    /**
     * @return Factory
     */
    public function staff(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name'  => $this->faker->name,
                'email' => 'staff' . random_int(1, 99999) . '@gmail.com',
                'phone' => $this->faker->unique()->phoneNumber(),
                'role'  => 'staff',
            ];
        });
    }
}
