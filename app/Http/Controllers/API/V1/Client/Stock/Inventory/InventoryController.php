<?php

namespace App\Http\Controllers\API\V1\Client\Stock\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductInventoryResource;
use App\Models\Product;
use App\Models\Media;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class InventoryController extends Controller
{
    use sendApiResponse;

    protected $dataNotFoundMsg;

    public function __construct()
    {
        // Initialize the global variable if needed
        $this->dataNotFoundMsg = 'Product not found !';
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = 10;

        if (request()->query('perPage')) {
            $perPage = request()->query('perPage');
        }

        $products = Product::query()->with(['main_image', 'variations' => function ($query) {
            $query->select('id', 'product_id', 'variant', 'quantity', 'price');
        }])
            ->where('shop_id', $request->header('shop-id'))
            ->orderByDesc('id')
            ->paginate($perPage);

        if ($products->isEmpty()) {
            return $this->sendApiResponse('', 'No products available', 'NotAvailable');
        }

        return $this->sendApiResponse(ProductInventoryResource::collection($products), 'Product inventory list');
    }

    public function show(Request $request, $id): JsonResponse
    {
        $product = Product::query()->with('main_image', 'other_images')
            ->where('id', $id)
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        if (!$product) {
            return $this->sendApiResponse('', $this->dataNotFoundMsg, 'NotFound');
        }

        return $this->sendApiResponse($product, $this->dataNotFoundMsg, 'NotFound');
    }

    public function update(ProductRequest $request): JsonResponse
    {
        $product = Product::query()->with('main_image')
            ->where('id', $request->input('product_id'))
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        if (!$product) {
            return $this->sendApiResponse('', $this->dataNotFoundMsg, 'NotFound');
        }

        if ($request->input('product_name') !== null) {
            $product->product_name = $request->input('product_name');
        }

        if ($request->input('product_code') !== null) {
            $product->product_code = $request->input('product_code');
        }

        if ($request->input('selling_price') !== null) {
            $product->price = $request->input('selling_price');
        }

        $product->save();

        if ($request->has('main_image')) {

            $image_path = $product->main_image->name;
            File::delete(public_path($image_path));

            $imageName = time() . '_main_image.' . $request->main_image->extension();
            $request->main_image->move(public_path('images'), $imageName);
            Media::query()->where('type', 'product_main_image')->where('parent_id', $product->id)->update([
                'name' => '/images/' . $imageName
            ]);
        }

        return $this->sendApiResponse($product, 'Inventory updated successfully');
    }
}
