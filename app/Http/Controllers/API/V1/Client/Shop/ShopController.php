<?php

namespace App\Http\Controllers\API\V1\Client\Shop;

use App\Http\Controllers\MerchantBaseController;
use App\Http\Resources\TagManagerResource;
use App\Models\ActiveTheme;
use App\Models\MerchantCourier;
use App\Models\Product;
use App\Models\Shop;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShopController extends MerchantBaseController
{
    use sendApiResponse;

    protected function loadBannersAndSlidersWithTimestamps($shop)
    {
        $banners = $shop->banner()->orderBy('id', 'DESC')->limit(3)->get();
        $banners = $banners->map(function ($banner) {
            $imagePath = $banner->image;
            $s3URL = substr($imagePath, 0, 30);

            if($s3URL == 'https://cdn-s3.funnelliner.com') {
                $image = $banner->image;
            } else {
                $image = env('AWS_URL') . $banner->image;
            }

            $timestamp = $banner->updated_at->getTimestamp();
            $banner->image = $image . '?v=' . $timestamp;

            return $banner;
        });

        $shop->setRelation('banner', $banners);

        $sliders = $shop->slider()->orderBy('id', 'DESC')->limit(3)->get();
        $sliders = $sliders->map(function ($slider) {
            $imagePath = $slider->image;
            $s3URL = substr($imagePath, 0, 30);

            if($s3URL == 'https://cdn-s3.funnelliner.com') {
                $image = $slider->image;
            } else {
                $image = env('AWS_URL') . $slider->image;
            }

            $timestamp = $slider->updated_at->getTimestamp();
            $slider->image = $image . '?v=' . $timestamp;

            return $slider;
        });

        $shop->setRelation('slider', $sliders);
    }

    public function index(Request $request)
    {
        if ($request->header('domain') && $request->header('domain') !== null) {
            $shop = Shop::query()->where('domain', $request->header('domain'))->first();

            if (!$shop) {
                throw ValidationException::withMessages([
                    'shop_id' => Shop::DOMAINNOTFOUND
                ]);
            }

            $activeTheme = ActiveTheme::query()
                ->join('themes', 'themes.id', '=', 'active_themes.theme_id')
                ->where('active_themes.shop_id', $shop->shop_id)
                ->where('active_themes.type', 'multiple')
                ->first();

            $shop['theme_id'] = null;

            if ($activeTheme) {
                $shop['theme_id'] = $activeTheme->name;
            }

            $this->loadBannersAndSlidersWithTimestamps($shop);
            $shop->load('shop_logo');
            $shop->load('shop_favicon');
            $shop->load('addons_info');
            $shop->load('otherScript');

            return $this->sendApiResponse($shop);
        }

        return $this->sendApiResponse('', Shop::DOMAINNOTFOUND, 'DomainNotFound', [], 401);
    }

    public function domain(Request $request): JsonResponse
    {
        if ($request->header('domain') && $request->header('domain') !== null) {
            $shop = Shop::query()->where('domain_request', $request->header('domain'))->first();

            if (!$shop) {
                throw ValidationException::withMessages([
                    'domain_request' => Shop::DOMAINNOTFOUND
                ]);
            }

            $activeTheme = ActiveTheme::query()->join('themes', 'themes.id', '=', 'active_themes.theme_id')
                ->where('active_themes.shop_id', $shop->shop_id)
                ->where('active_themes.type', 'multiple')
                ->first();

            $shop['theme_id'] = null;

            if ($activeTheme) {
                $shop['theme_id'] = $activeTheme->name;
            }
            $this->loadBannersAndSlidersWithTimestamps($shop);
            $shop->load('shop_logo', 'shop_favicon', 'addons_info', 'otherScript');

            return $this->sendApiResponse($shop);
        }

        return $this->sendApiResponse('', Shop::DOMAINNOTFOUND, 'DomainNotFound', [], 401);
    }

    public function googleTagManager(Request $request): JsonResponse
    {
        if ($request->header('domain') && $request->header('domain') !== null) {
            $shop = Shop::query()->where('domain_request', $request->header('domain'))->first();

            if (!$shop) {
                return $this->sendApiResponse('', 'Invalid domain !');
            }
            $shop->load('otherScript');

            return $this->sendApiResponse(new TagManagerResource($shop));
        }

        return $this->sendApiResponse('', Shop::DOMAINNOTFOUND, 'DomainNotFound', [], 401);
    }

    public function shopSteps()
    {
        $shop = Shop::query()->with('shop_logo')->where('shop_id', request()->header('shop-id'))->first();
        $custom = is_null($shop->domain_request) ? 0 : 1;
        $product = Product::where('shop_id', request()->header('shop-id'))->first();
        $design = ActiveTheme::where('shop_id', request()->header('shop-id'))
            ->where('type', 'multiple')
            ->first();
        $landingPageInfo = ActiveTheme::where('shop_id', request()->header('shop-id'))
            ->where('type', 'landing')
            ->first();
        $courierInfo = MerchantCourier::where('shop_id', request()->header('shop-id'))->first();
        $businessInfo = ($shop->name !== null && $shop->phone !== null && $shop->address !== null && $shop->shop_logo !== null) ? 1 : 0;
        $productInfo = ($product) ? 1 : 0;
        $designWebsite = ($design) ? 1 : 0;
        $landingPage = ($landingPageInfo) ? 1 : 0;
        $courier = ($courierInfo) ? 1 : 0;

        $shopSteps = [
            'business_info'  => $businessInfo,
            'product_info'   => $productInfo,
            'desing_website' => $designWebsite,
            'custom_domain'  => $custom,
            'courier'        => $courier,
            'landing_page'   => $landingPage,
            'total_task'     => $landingPage + $businessInfo + $productInfo + $designWebsite + $custom + $courier
        ];

        return $this->sendApiResponse($shopSteps, 'Shop steps data');
    }

    // Order attach image permission update
    public function orderAttachImgPermUpdate(Request $request): JsonResponse
    {
        $shopId = $request->header('shop-id');

        $orderAttchImg = Shop::query()
        ->select('id', 'order_attach_img_perm', 'shop_id')
        ->where('shop_id', $shopId)
        ->first();

        if($orderAttchImg) {
            $newStatus = match($orderAttchImg->order_attach_img_perm) {
                0 => 1,
                1 => 0,
            };

            $orderAttchImg->order_attach_img_perm = $newStatus;
            $orderAttchImg->update();

            return $this->sendApiResponse($orderAttchImg, 'Order attach image permission status updated');
        }

        return $this->sendApiResponse('', 'Shop not found !');
    }
}