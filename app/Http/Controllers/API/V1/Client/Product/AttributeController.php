<?php

namespace App\Http\Controllers\API\V1\Client\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeStoreRequest;
use App\Http\Requests\AttributeValueStoreRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\JsonResponse;

class AttributeController extends Controller
{
    public function index(): JsonResponse
    {
        $attributes = Attribute::query()
            ->whereNull('shop_id')
            ->get();

        $shop_attributes = Attribute::query()
            ->where('shop_id', request()->header('shop_id'))
            ->get();

        $attributes = $attributes->merge($shop_attributes);

        return $this->sendApiResponse($attributes);
    }

    public function store(AttributeStoreRequest $request): JsonResponse
    {
        $attribute = Attribute::query()->firstOrCreate([
            'key' => $request->input('key'),
        ], [
            'shop_id' => $request->header('shop_id'),
        ]);

        return $this->sendApiResponse($attribute);
    }

    public function attributeValues(int $id): JsonResponse
    {
        $values = AttributeValue::query()
            ->where('attribute_id', $id)
            ->get();

        return $this->sendApiResponse($values);
    }

    public function attributeValueStore(AttributeValueStoreRequest $request): JsonResponse
    {
        $value = AttributeValue::query()->firstOrCreate([
            'value'        => $request->input('value'),
            'attribute_id' => $request->input('attribute_id'),
        ]);

        return $this->sendApiResponse($value);
    }
}
