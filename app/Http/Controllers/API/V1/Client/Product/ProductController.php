<?php

namespace App\Http\Controllers\API\V1\Client\Product;

use DateTime;
use DateTimeZone;
use App\Models\Media;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Models\ProductVariation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\ProductStoreRequest;
use App\Notifications\ProductNotification;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductDashboardResource;

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
        $perPage = 10;

        if (request()->query('perPage')) {
            $perPage = request()->query('perPage');
        }

        $products = Product::query()
            ->with('main_image', 'other_images', 'variations', 'variations.media')
            ->where('shop_id', $request->header('shop-id'))
            ->orderByDesc('id')
            ->paginate($perPage);

        if ($products->isEmpty()) {
            return $this->sendApiResponse('', 'No Data available');
        }

        return $this->sendApiResponse(ProductDashboardResource::collection($products));
    }

    /**
     * Display a listing of products for search.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function productForSearch(Request $request): JsonResponse
    {
        $query = Product::with('variations:id,variant,product_id','main_image')->select('id', 'product_name','product_code','price','product_qty','created_at','inside_dhaka','outside_dhaka','default_delivery_location','delivery_charge','sub_area_charge')
            ->where('shop_id', $request->header('shop-id'));

        if ($request->query('search')) {
            $query->where('product_name', 'LIKE', '%' . $request->query('search') . '%');
        }

        $products = $query->orderBy('product_name')->get();

        if ($products->isEmpty()) {
            return $this->sendApiResponse('', 'No Data available');
        }

        return $this->sendApiResponse($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductStoreRequest $request
     * @return JsonResponse
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {

            $productCode = $request->product_code;
            $productCodeCheck = Product::query()->where('product_code', $productCode)
            ->where('shop_id', $request->header('shop-id'))
            ->exists();

            if ($productCodeCheck) {
                $errors = [
                    'product_code' => ['Product code already exists']
                ];
                return response()->json([
                    'message' => 'Product code is exits',
                    'errors' => $errors
                ], 400);
            }
            
            $product = new Product();

            if (!$request->input('category_id')) {
                $category = new Category();
                $category->name = $request->input('category_name');
                $category->slug = Str::slug($request->input('category_name'));
                $category->shop_id = $request->header('shop-id');
                $category->status = 1;
                $category->save();

                $categoryId = $category->id;
            } else {
                $categoryId = $request->input('category_id');
            }

            $product->product_code = $request->input('product_code');
            $product->category_id = $categoryId;
            $product->shop_id = $request->header('shop-id');
            $product->product_name = $request->input('product_name');
            $product->slug = Str::slug($request->input('product_name')) . '-' . Str::random('5');
            $product->price = $request->input('price');
            $product->product_qty = $request->input('product_qty');
            $product->discount = $request->input('discount');
            $product->discount_type = $request->input('discount_type');
            $product->short_description = $request->input('short_description');
            $product->long_description = $request->input('long_description');
            $product->delivery_charge = $request->input('delivery_charge');

            if ($request->input('delivery_charge') === 'paid') {
                $product->inside_dhaka = $request->input('inside_dhaka');
                $product->outside_dhaka = $request->input('outside_dhaka');
                $product->sub_area_charge = $request->input('sub_area_charge');
            } else {
                $product->inside_dhaka = 0;
                $product->outside_dhaka = 0;
            }
            $product->save();

            if ($request->hasFile('main_image')) {
                $filePath = 'media/main-image/' . $request->header('id');
                Media::upload($product, $request->file('main_image'), $filePath, 'product_main_image');
            }

            if ($request->hasFile('gallery_image')) {
                $galleryImages = $request->file('gallery_image');

                foreach ($galleryImages as $image) {
                    $filePath = 'media/product-gallery-image/' . $request->header('id');
                    Media::upload($product, $image, $filePath, 'product_gallery_image');
                }
            }

            $choice_options = [];

            if ($request->has('choice_no')) {
                foreach ($request->choice_no as $key => $no) {
                    $str = 'choice_options_' . $no;
                    $item['id'] = $no;
                    $item['key'] = $request->choice[$key];
                    $data = [];

                    foreach ($request[$str] as $i => $eachValue) {
                        $attribute_value = AttributeValue::query()
                            ->select('id', 'attribute_id', 'value')
                            ->where('attribute_id', $no)
                            ->where('value', $eachValue)
                            ->first()
                            ->toArray();

                        $data[] = (object)$attribute_value;
                    }
                    $item['values'] = $data;
                    $choice_options[] = $item;
                }
            }

            if (!empty($request->choice_no)) {
                $product->attributes = json_encode($choice_options);
            } else {
                $product->attributes = json_encode([]);
            }
            $product->save();

            if ($request->variants !== null) {
                $variations = collect(json_decode($request->variants))->toArray();

                foreach ($variations as $key => $item) {
                    $variant = collect($item)->toArray();
                    $variation = ProductVariation::query()->create([
                        'product_id'  => $product->id,
                        'variant'     => $variant['variant'],
                        'price'       => $variant['price'] === 0 ? $product->price : $variant['price'],
                        'quantity'    => $variant['quantity'],
                        'code'        => $variant['product_code'],
                        'description' => $variant['description'],
                    ]);

                    $image = 'media_' . $key;

                    if ($request->hasFile($image)) {
                        $filePath = 'media/product-variation-image/' . $request->header('id');
                        Media::upload($variation, $request->file($image), $filePath, 'product_variation_image');
                    }
                }
            }

            $product->load('main_image', 'other_images', 'variations', 'variations.media');

            $date = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            $time = $date->format("M d Y h:i A");
            $notifyInfo = [
                'text'       => "You added a new product " . $product->name . " ",
                'product_id' => $product->id,
                'order_time' => $time,
                'type'       => 'product'
            ];

            Notification::send($product, new ProductNotification($notifyInfo));

            return $this->sendApiResponse($product, 'Product created successfully');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $product = Product::query()
            ->with('main_image', 'other_images', 'variations', 'variations.media')
            ->where('shop_id', $request->header('shop-id'))
            ->where('id', $id)
            ->first();
        $product['other_images'] = $product->other_images;

        if (!$product) {
            return $this->sendApiResponse('', 'No Data available', 'NotFound');
        }

        return $this->sendApiResponse(new ProductDetailsResource($product));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ProductUpdateRequest $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $product = Product::with('main_image')->find($id);

            if (!$product) {
                return $this->sendApiResponse('', 'Product Not Found', 'NotFound');
            }

            $productCode = $request->product_code;
            if ($product->product_code == $productCode) {
                $product->product_code = $productCode;
            }elseif($product->product_code != $productCode){
                
                $productCodeCheck = Product::query()
                ->where('product_code', $productCode)
                ->where('shop_id', $request->header('shop-id'))
                ->exists();

                if($productCodeCheck){
                    $errors = [
                        'product_code' => ['Product code already exists']
                    ];
                    return response()->json([
                        'message' => 'Product code is exits',
                        'errors' => $errors
                    ], 400);
                }else {
                    $product->product_code = $productCode;
                }
            }
            
            $product->category_id   = $request->category_id;
            $product->product_name  = $request->product_name;
            $product->slug          = Str::slug($request->product_name);
            $product->price         = $request->price;
            $product->product_qty   = $request->product_qty;
            $product->discount      = $request->discount;
            $product->discount_type = $request->input('discount_type');
            $product->short_description = $request->short_description;
            $product->long_description = $request->input('long_description');
            $product->status        = $request->status ? $request->status : $product->status;
            $product->delivery_charge = $request->input('delivery_charge');

            if ($request->input('delivery_charge') === 'paid') {
                $product->inside_dhaka = $request->input('inside_dhaka');
                $product->outside_dhaka = $request->input('outside_dhaka');
                $product->sub_area_charge = $request->input('sub_area_charge');
            } else {
                $product->inside_dhaka = 0;
                $product->outside_dhaka = 0;
            }

            if ($request->hasFile('main_image')) {
                $filePath = 'media/main-image/' . $request->header('id');

                if ($product->main_image !== null) {
                    $product->main_image->replaceWith($request->file('main_image'), $filePath);
                }

                Media::query()->where('parent_id', $product->id)
                    ->where(function ($q) {
                        $q->where('type', 'product_main_image');
                    })
                    ->delete();

                Media::upload($product, $request->file('main_image'), $filePath, 'product_main_image');
            }

            if ($request->hasFile('gallery_image')) {

                foreach ($product->other_images as $image) {
                    if ($image !== null) {
                        s3ImageDelete(Product::PRODUCTGALLERYIMAGEPATH, $image->name, $request->header('id'));
                        Media::query()->where('parent_id', $product->id)
                            ->where(function ($q) {
                                $q->where('type', 'product_gallery_image');
                            })
                            ->delete();
                    }
                }

                $galleryImages = $request->file('gallery_image');

                foreach ($galleryImages as $image) {
                    $filePath = 'media/product-gallery-image/' . $request->header('id');
                    Media::upload($product, $image, $filePath, 'product_gallery_image');
                }
            }

            $choice_options = [];

            if ($request->has('choice_no')) {
                foreach ($request->choice_no as $key => $no) {
                    $str = 'choice_options_' . $no;
                    $item['id'] = $no;
                    $item['key'] = $request->choice[$key];
                    $data = [];

                    foreach ($request[$str] as $i => $eachValue) {

                        $attribute_value = AttributeValue::query()
                            ->select('id', 'attribute_id', 'value')
                            ->where('attribute_id', $no)
                            ->where('value', $eachValue)
                            ->first()
                            ->toArray();
                        $data[] = (object)$attribute_value;
                    }
                    $item['values'] = $data;
                    $choice_options[] = $item;
                }
            }

            if (!empty($request->choice_no)) {
                $product->attributes = json_encode($choice_options);
            } else {
                $product->attributes = json_encode([]);
            }

            if ($request->variants !== null) {
                $variations = collect(json_decode($request->variants))->toArray();

                $variantsName = collect($variations)->pluck('variant')->all();
                $deleteVariantIds = ProductVariation::query()
                    ->where('product_id', $id)
                    ->whereNotIn('variant', $variantsName)
                    ->pluck('id');

                if ($deleteVariantIds) {
                    $deleteMediaItems = Media::query()
                        ->where('type', 'product_variation_image')
                        ->whereIn('parent_id', $deleteVariantIds)
                        ->get();

                    foreach ($deleteMediaItems as $media) {
                        s3ImageDelete(Product::PRODUCTVARIATIONIMAGEPATH, $media->name, $request->header('id'));
                        Media::query()->where('parent_id', $media->parent_id)->delete();
                    }
                    ProductVariation::query()->whereIn('id', $deleteVariantIds)->delete();
                }

                foreach ($variations as $key => $item) {
                    $variant = collect($item)->toArray();
                    $variation = ProductVariation::query()->updateOrCreate([
                        'product_id' => $product->id,
                        'variant'    => $variant['variant'],
                    ], [
                        'price'       => $variant['price'] === 0 ? $product->price : $variant['price'],
                        'quantity'    => $variant['quantity'],
                        'code'        => $variant['product_code'],
                        'description' => $variant['description'],
                    ]);


                    $image = 'media_' . $key;

                    if ($request->hasFile($image)) {
                        $productVariation = $product->load('variations');

                        if ($productVariation->variations[$key]->media != null) {
                            s3ImageDelete(Product::PRODUCTVARIATIONIMAGEPATH, $productVariation->variations[$key]->media, $request->header('id'));
                            Media::query()
                                ->where('parent_id', $variation->id)
                                ->where('type', 'product_variation_image')
                                ->delete();
                        }
                        $filePath = 'media/product-variation-image/' . $request->header('id');
                        Media::upload($variation, $request->file($image), $filePath, 'product_variation_image');
                    }
                }
            }

            $product->save();
            $product->load('main_image', 'other_images', 'variations', 'variations.media');

            DB::commit();

            return $this->sendApiResponse($product, 'Product Updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendApiResponse('', $e->getMessage(), 'Exception');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $product = Product::with(['main_image'])->find($id);

        if (!$product) {
            return $this->sendApiResponse('', 'Product Not Found', 'NotFound');
        }

        if ($product->main_image) {
            s3ImageDelete(Product::PRODUCTIMAGEPATH, $product->main_image->name, $request->header('id'));
            Media::query()->where('parent_id', $product->id)
                ->where('type', 'product_main_image')
                ->delete();
        }

        $product->delete();

        return $this->sendApiResponse('', 'Product Removed Successfully');
    }
}