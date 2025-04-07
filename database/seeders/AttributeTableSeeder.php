<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attributes = [
            'Size' => [
                'M',
                'L',
                'XL',
                'XXL',
            ],
            'Weight' => [
                '100gm',
                '200gm',
                '500gm',
                '1kg',
            ],
            'Color' => [
                'Red',
                'Green',
                'Blue',
                'Yellow',
                'Black'
            ],
            'Material' => [
                'Gold',
                'Silver',
                'Platinum',
                'Plastic',
                'Carbon-fiber'
            ]
        ];

        foreach ($attributes as $key => $attribute) {
            $item = Attribute::query()->create([
                'key' => $key,
            ]);

            foreach ($attribute as $value) {
                $attribute_values = AttributeValue::query()->create([
                    'attribute_id' => $item->id,
                    'value'        => $value
                ]);
            }
        }


    }
}
