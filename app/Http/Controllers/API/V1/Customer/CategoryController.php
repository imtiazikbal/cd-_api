<?php

namespace App\Http\Controllers\API\V1\Customer;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductResourceController;

class CategoryController extends Controller
{
    use sendApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->getCategoryTreeForParentId($request->header('shop-id'));

        return $this->sendApiResponse($categories);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $slug
     * @return JsonResponse
     */
    public function show(Request $request, $slug): JsonResponse
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        if (!$category) {
            $this->sendApiResponse('', 'Category Not found', 'NotFound');
        }

        $categories = $this->getCategoryTreeForParentId($request->header('shop-id'), $category->id);

        return $this->sendApiResponse($categories);
    }

    protected function getCategoryTreeForParentId($shop_id, $parent_id = 0): array
    {
        // return Cache::remember('cachedCategories-' . $shop_id, 3600, function () use ($shop_id, $parent_id) {
            $categories = [];

            $result = Category::with('category_image')
                ->where('parent_id', $parent_id)
                ->where('shop_id', $shop_id)
                ->get();

            foreach ($result as $mainCategory) {
                $category = [];
                $category['id'] = $mainCategory->id;
                $category['name'] = $mainCategory->name;
                $category['slug'] = $mainCategory->slug;
                $category['image'] = $mainCategory->category_image;
                $category['description'] = $mainCategory->description;
                $category['shop_id'] = $mainCategory->shop_id;
                $category['parent_id'] = $mainCategory->parent_id;
                $category['status'] = $mainCategory->status;
                $category['sub_categories'] = $this->getCategoryTreeForParentId($category['id'], $category['shop_id']);
                $categories[] = $category;
            }

            return $categories;
        // });
    }

    public function productListCategoryWise(Request $request, $id): JsonResponse
    {
        // return Cache::remember('cachedProductsByCategory-' . $id, 3600, function () use ($request, $id) {

            $products = Product::query()
                ->with('main_image', 'variations')
                ->where('category_id', $id)
                ->where('shop_id', $request->header('shop-id'))
                ->paginate(20);

            if (!$products) {
                return $this->sendApiResponse('', 'Data not found!');
            }

            return $this->sendApiResponse(ProductResourceController::collection($products));
        // });
    }
}
