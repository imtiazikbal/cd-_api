<?php

namespace App\Http\Controllers\API\V1\Customer;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductVariation;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductResourceController;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // return Cache::remember('cachedProducts-' . $request->header('shop-id'), 3600, function () use ($request) {
            $products = Product::query()->with('main_image', 'variations')
                ->where('shop_id', $request->header('shop-id'))
                ->orderByDesc('id')
                ->paginate(20);

            return $this->sendApiResponse(ProductResourceController::collection($products));
        // });
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id)
    {
        // return Cache::remember('cachedSingleProduct-' . $id, 3600, function () use ($request, $id) {

            $shopId = $request->header('shop-id');
            $product = Product::query()
                ->with('main_image', 'other_images', 'variations', 'variations.media')
                ->where('shop_id', $shopId)
                ->where('id', $id)
                ->first();

            $relatedProducts = Product::where('shop_id', $shopId)
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $id)
                ->get();

            if (!$product) {
                return $this->sendApiResponse('', 'Product Not Found', 'NotFound');
            }

            return (new ProductDetailsResource($product))->additional([
                'relatedProducts' => $relatedProducts
            ]);
        // });
    }

    public function productSearch(Request $request): JsonResponse
    {
        // return Cache::remember('cachedProductSearch-' . $request->header('shop-id'), 3600, function () use ($request) {
            $terms = '%' . $request->search . '%';
            $products = Product::query()
                ->with('main_image', 'variations')
                ->where('shop_id', $request->header('shop-id'))
                ->where(function ($query) use ($terms) {
                    $query->where('product_name', 'LIKE', $terms)
                        ->orWhere('product_code', 'LIKE', $terms);
                })
                ->paginate(20);

            if (empty($products)) {
                return $this->sendApiResponse('', 'Product not found !');
            }

            return $this->sendApiResponse(ProductResourceController::collection($products), 'Product search result');
        // });
    }

    public function productCombinedPrice(Request $request)
    {
        $variant = ProductVariation::query()
            ->with('product', 'media')
            ->where('product_id', $request->input('product_id'))
            ->where('variant', $request->input('variant'))
            ->first();

        if (!$variant) {
            return $this->sendApiResponse('', 'Data not found !', false);
        }

        return $this->sendApiResponse($variant, 'Product variation combined price');
    }
}