<?php

namespace App\Http\Controllers\API\V1\Client\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    public function sku_combinations(Request $request): JsonResponse
    {
        $options = [];

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = [];

                foreach ($request[$name] as $attribute => $item) {
                    $data[] = $item;
                }
                $options[] = $data;
            }
        }
        $combinations = $this->combinations($options);

        $variations = [];

        foreach ($combinations as $c_key => $combination) {
            $str = '';

            foreach ($combination as $i_key => $item) {
                if ($i_key > 0) {
                    $str .= '-' . str_replace(' ', '', $item);
                } else {
                    $str .= str_replace(' ', '', $item);
                }
            }
            $variations[] = $str;
        }
        $list = [];

        foreach ($variations as $key => $variant) {
            $data = [
                'id'           => $key + 1,
                'variant'      => $variant,
                'price'        => $request->input('price') ?? 0,
                'product_code' => $request->input('code') ?? '',
                'quantity'     => 1,
                'description'  => null,
                'media'        => null,
            ];

            $list[] = $data;
        }

        return $this->sendApiResponse($list);
    }

    private function combinations(array $arrays): array
    {
        $result = [[]];

        foreach ($arrays as $property => $property_values) {
            $tmp = [];

            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }

        return $result;
    }
}
