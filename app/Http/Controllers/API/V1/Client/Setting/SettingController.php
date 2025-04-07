<?php

namespace App\Http\Controllers\API\V1\Client\Setting;

use App\Models\Shop;
use App\Models\User;
use App\Models\Media;
use App\Models\MerchantInfo;
use Illuminate\Http\Request;
use App\Models\WebsiteSetting;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\MerchantSettingRequest;
use App\Http\Resources\AdvancePaymentResource;
use App\Http\Controllers\MerchantBaseController;
use Carbon\Carbon;

class SettingController extends MerchantBaseController
{
    use sendApiResponse;

    protected $dataNotFoundMsg;

    public function __construct()
    {
        $this->dataNotFoundMsg = 'Merchant not Found';
    }

    public function business_info(MerchantSettingRequest $request): JsonResponse
    {
        // return Cache::remember('cachedShopInfo-' . $request->header('shop-id'), 3600, function () use ($request) {
            $shop = Shop::query()
                ->with('merchant', 'shop_logo', 'shop_favicon')
                ->where('shop_id', $request->header('shop-id'))
                ->first();

            if (!$shop) {
                return response()->json(['success' => false, 'msg' => 'Shop not Found',], 200);
            }

            return response()->json(['success' => true, 'data' => $shop], 200);
        // });
    }

    public function business_info_update(MerchantSettingRequest $request): JsonResponse
    {
        Cache::forget('cachedShopInfo-' . $request->header('shop-id'));

        $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();

        // if ($request->filled('shop_name')) {
        //     $shop->name = $request->input('shop_name');
        // }
        if ($request->filled('shop_address')) {
            $shop->address = $request->input('shop_address');
        }

        if ($request->filled('email')) {
            $shop->email = $request->input('email');
        }

        if ($request->filled('phone')) {
            $shop->phone = $request->input('phone');
        }

        if ($request->filled('about_us')) {
            $shop->about_us = $request->input('about_us');
        }

        if ($request->filled('tos')) {
            $shop->tos = $request->input('tos');
        }

        if ($request->filled('privacy_policy')) {
            $shop->privacy_policy = $request->input('privacy_policy');
        }

        if ($request->filled('default_delivery_location')) {
            $shop->default_delivery_location = $request->input('default_delivery_location');
        }

        $shop->shop_id = $request->header('shop-id');

        if ($request->filled('shop_meta_title')) {
            $shop->shop_meta_title = $request->input('shop_meta_title');
        }

        if ($request->filled('shop_meta_description')) {
            $shop->shop_meta_description = $request->input('shop_meta_description');
        }

        if ($request->filled('fb')) {
            $shop->fb = $request->input('fb');
        }

        if ($request->filled('twitter')) {
            $shop->twitter = $request->input('twitter');
        }

        if ($request->filled('linkedin')) {
            $shop->linkedin = $request->input('linkedin');
        }

        if ($request->filled('instagram')) {
            $shop->instagram = $request->input('instagram');
        }

        if ($request->filled('youtube')) {
            $shop->youtube = $request->input('youtube');
        }
        $shop->save();

        if ($request->hasFile('shop_favicon')) {
            $filePath = 'media/shop-favicon/' . $request->header('id');

            if ($shop->shop_favicon !== null) {
                $shop->shop_favicon->replaceWith($request->file('shop_favicon'), $filePath);
            }
            Media::upload($shop, $request->file('shop_favicon'), $filePath, 'shop_favicon');
        }

        if ($request->hasFile('shop_logo')) {
            $filePath = 'media/shop-logo/' . $request->header('id');

            if ($shop->shop_logo !== null) {
                $shop->shop_logo->replaceWith($request->file('shop_logo'), $filePath);
            }
            Media::upload($shop, $request->file('shop_logo'), $filePath, 'shop_logo');
        }

        $shop->load('shop_logo', 'shop_favicon');

        return response()->json([
            'success' => true,
            'msg'     => 'Merchant setting business information update successfully',
            'data'    => $shop,
        ], 200);
    }

    public function owner_info_update(MerchantSettingRequest $request)
    {
        try {
            DB::beginTransaction();
            $merchant = User::where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => $this->dataNotFoundMsg,
                ], 404);
            }

            $merchant->name = $request->owner_name;
            $merchant->email = $request->owner_email;
            $merchant->phone = $request->owner_number;
            $merchant->save();

            $merchantInfo = MerchantInfo::where('user_id', $merchant->id)->first();
            $merchantInfo->address = $request->owner_address;
            $merchantInfo->other_info = $request->owner_other_info;
            $merchantInfo->save();

            $ownerInfo = [
                'owner_name'       => $merchant->name,
                'owner_email'      => $merchant->email,
                'owner_number'     => $merchant->phone,
                'owner_address'    => $merchantInfo->address,
                'owner_other_info' => $merchantInfo->other_info,
            ];


            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'merchant setting owner information update successfully',
                'data'    => $ownerInfo,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }

    public function owner_info(MerchantSettingRequest $request)
    {
        try {
            DB::beginTransaction();
            $merchant = User::where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => $this->dataNotFoundMsg,
                ], 404);
            }

            $merchantInfo = MerchantInfo::where('user_id', $merchant->id)->first();

            if (!$merchantInfo) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Merchant info not found',
                ], 404);
            }

            $ownerInfo = [
                'owner_name'       => $merchant->name,
                'owner_email'      => $merchant->email,
                'owner_number'     => $merchant->phone,
                'owner_address'    => $merchantInfo->address,
                'owner_other_info' => $merchantInfo->other_info,
            ];


            DB::commit();

            return response()->json([
                'success' => true,
                'data'    => $ownerInfo,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }

    public function password_security_update(MerchantSettingRequest $request)
    {
        try {
            DB::beginTransaction();
            $merchant = User::where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => $this->dataNotFoundMsg,
                ], 404);
            }

            #Match The Old Password
            if (!Hash::check($request->old_password, $merchant->password)) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Old Password Doesn\'t match!',
                ], 404);
            }

            #Update the new Password
            User::whereId($merchant->id)->update([
                'password' => Hash::make($request->new_password)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'merchant setting password update successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }

    public function website_update(MerchantSettingRequest $request)
    {
        try {
            $merchant = User::query()->where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => $this->dataNotFoundMsg,
                ], 404);
            }

            $websiteSetting = WebsiteSetting::query()->where('user_id', $merchant->id)->first();

            if (!$websiteSetting) {
                DB::beginTransaction();
                $web = new WebsiteSetting();

                if ($request->filled('cash_on_delivery')) {
                    $web->cash_on_delivery = $request->input('cash_on_delivery');
                }

                if ($request->filled('invoice_id')) {
                    $web->invoice_id = $request->input('invoice_id');
                }

                if ($request->filled('custom_domain')) {
                    $web->custom_domain = $request->input('custom_domain');
                }

                // if ($request->filled('shop_name')) {
                //     $web->shop_name = $request->input('shop_name');
                // }
                if ($request->filled('shop_address')) {
                    $web->shop_address = $request->input('shop_address');
                }

                if ($request->filled('website_shop_id')) {
                    $web->website_shop_id = $request->input('website_shop_id');
                }

                $web->shop_id = $request->header('shop-id');
                $web->user_id = $request->header('id');

                if ($request->meta_title) {
                    $web->meta_title = $request->meta_title;
                }

                if ($request->meta_description) {
                    $web->meta_description = $request->meta_description;
                }

                $web->save();

                if ($request->hasFile('website_shop_logo')) {
                    $filePath = 'media/website-shop-logo/' . $request->header('id');
                    $media = Media::upload($web, $request->file('website_shop_logo'), $filePath, 'website_shop_logo');

                    if ($media) {
                        $web['website_shop_logo'] = $media->name;
                    }
                }
                DB::commit();

                return response()->json([
                    'success' => true,
                    'msg'     => 'Merchant website setting update successfully',
                    'data'    => $web,
                ], 200);
            }

            $websiteSetting->website_shop_logo;
            DB::beginTransaction();

            if ($request->cash_on_delivery) {
                $websiteSetting->cash_on_delivery = $request->cash_on_delivery;
            }

            if ($request->invoice_id) {
                $websiteSetting->invoice_id = $request->invoice_id;
            }

            if ($request->custom_domain) {
                $websiteSetting->custom_domain = $request->custom_domain;
            }

            // if ($request->shop_name) {
            //     $websiteSetting->shop_name = $request->shop_name;
            // }
            if ($request->shop_address) {
                $websiteSetting->shop_address = $request->shop_address;
            }

            if ($request->website_shop_id) {
                $websiteSetting->website_shop_id = $request->website_shop_id;
            }


            $websiteSetting->shop_id = $request->header('shop-id');
            $websiteSetting->user_id = $request->header('id');

            if ($request->meta_title) {
                $websiteSetting->meta_title = $request->meta_title;
            }

            if ($request->meta_description) {
                $websiteSetting->meta_description = $request->meta_description;
            }
            $websiteSetting->save();

            if ($request->hasFile('website_shop_logo')) {
                $filePath = 'media/website-shop-logo/' . $request->header('id');
                $media = Media::upload($websiteSetting, $request->file('website_shop_logo'), $filePath, 'website_shop_logo');

                if ($media) {
                    $websiteSetting['website_shop_logo'] = $media->name;
                }
            }


            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'Merchant website setting update successfully',
                'data'    => $websiteSetting,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }

    public function pixel_update(MerchantSettingRequest $request)
    {

        try {
            DB::beginTransaction();
            $merchant = User::where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => $this->dataNotFoundMsg,
                ], 404);
            }

            $shop = Shop::where('user_id', $merchant->id)->first();

            $shop->fb_pixel = !empty($request->fb_pixel) ? $request->fb_pixel : null;

            if ($request->c_api) {
                $shop->c_api = $request->c_api;
            }

            if ($request->test_event) {
                $shop->test_event = $request->test_event;
            }
            $shop->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'FB Pixel setting update successfully',
                'data'    => $shop,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }

    public function domain_verify(MerchantSettingRequest $request)
    {

        try {
            DB::beginTransaction();
            $merchant = User::where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => $this->dataNotFoundMsg,
                ], 404);
            }

            $shop = Shop::query()->where('user_id', $merchant->id)->first();
            $shop->shop_id = $request->shop_id;
            $shop->domain_verify = $request->domain_verify;
            $shop->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'Domain verify meta update successfully',
                'data'    => $shop,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }

    public function domain_request(MerchantSettingRequest $request)
    {
        try {
            DB::beginTransaction();
            $merchant = User::where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => $this->dataNotFoundMsg,
                ], 404);
            }

            $shop = Shop::where('user_id', $merchant->id)->first();
            $shop->shop_id = $request->shop_id;
            $shop->domain_request = $request->domain_request;
            $shop->domain_status = $request->input('domain_status');
            $shop->domain_request_date = now();
            $shop->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'Domain request successfully added.',
                'data'    => $shop,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }

    public function website(): JsonResponse
    {
        $websiteSetting = WebsiteSetting::with('website_shop_logo')->where('user_id', request()->header('id'))->first();

        if (!$websiteSetting) {
            return response()->json([
                'success' => false,
                'msg'     => 'Website setting not Found',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'data'    => $websiteSetting,
        ], 200);
    }

    public function updateAdvancePaymentStatus(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required'
        ]);

        $shop = WebsiteSetting::query()->where('shop_id', $request->header('shop-id'))->first();

        if (!$shop) {
            $shop = WebsiteSetting::query()->create([
                'user_id' => $request->header('id'),
                'shop_id' => $request->header('shop-id'),
            ]);
        }
        $shop->advanced_payment = $request->input('status');
        $shop->save();

        return $this->sendApiResponse('', 'status updated successfully');
    }

    public function getAdvancePaymentStatus(Request $request): JsonResponse
    {
        $advancedPayStatus = WebsiteSetting::query()->where('shop_id', $request->header('shop-id'))->first();

        if (!$advancedPayStatus) {
            $settings = WebsiteSetting::query()->create([
                'user_id' => $request->header('id'),
                'shop_id' => $request->header('shop-id'),
            ]);

            return $this->sendApiResponse(collect(new AdvancePaymentResource($settings)));
        }

        return $this->sendApiResponse(collect(new AdvancePaymentResource($advancedPayStatus)));
    }

    public function updateHoldOnStatus(Request $request)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $shop = WebsiteSetting::query()->where('shop_id', $request->header('shop-id'))->first();

        if (!$shop) {
            $shop = WebsiteSetting::query()->create([
                'user_id' => $request->header('id'),
                'shop_id' => $request->header('shop-id'),
            ]);
        }
        $shop->hold_on = $request->input('status');
        $shop->save();

        return $this->sendApiResponse('', 'Status updated');
    }

    public function getHoldOnStatus(Request $request): JsonResponse
    {
        $holdOnStatus = WebsiteSetting::query()->where('shop_id', $request->header('shop-id'))->first();

        if (!$holdOnStatus) {
            $settings = WebsiteSetting::query()->create([
                'user_id' => $request->header('id'),
                'shop_id' => $request->header('shop-id'),
            ]);

            return $this->sendApiResponse($settings, 'Created successfully');
        }

        return $this->sendApiResponse($holdOnStatus, 'Get data successfully');
    }

    public function updateShippedDateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'required'
        ]);

        $shop = WebsiteSetting::firstOrNew([
            'shop_id' => $request->header('shop-id')
        ], [
            'user_id' => $request->header('id')
        ]);

        $shop->shipped_date_status = $request->input('status');
        $shop->save();

        return $this->sendApiResponse([], 'Status successfully updated.');
    }

    public function getShippedDateStatus(Request $request): JsonResponse
    {
        $shippedDateStatus = WebsiteSetting::firstOrCreate([
            'shop_id' => $request->header('shop-id')
        ], [
            'user_id' => $request->header('id')
        ]);

        return $this->sendApiResponse($shippedDateStatus, 'Shipped Date Status retrieved successfully');
    }

    public function cStatusUpdateByShopWise(Request $request)
    {
        $shop = Shop::query()
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        if (!$shop) {
            return $this->sendApiResponse('', 'Shop not found !', 'false');
        }

        if (!$request->filled('c_status')) {
            return $this->sendApiResponse('', 'C_Status field is required', 'false');
        }
        $shop->c_status = $request->c_status;
        $shop->save();

        return $this->sendApiResponse('', 'Conversion API updated');
    }

    public function refreshDomainRequest(Request $request): JsonResponse
    {
        $shop = Shop::query()
        ->where('shop_id', $request->header('shop-id'))
        ->select('id', 'domain_request', 'shop_id')
        ->first();

        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }

        $shop->domain_status = "pending";
        $shop->domain_request_date = Carbon::today()->toDateString();
        $shop->ssl_status = "pending";
        $shop->save();

        return response()->json(['msg' => 'Domain request refreshed successfully']);
    }

    public function getDnsRecords(Request $request)
    {
        $domain = Shop::query()
            ->where('shop_id', $request->header('shop-id'))
            ->select('id', 'domain_request')
            ->first();
        
        $domainName = $domain->domain_request;    

        if (!$domainName) {
            return response()->json(['error' => 'Domain is required'], 400);
        }

        try {
            $aRecords = dns_get_record($domainName, DNS_A);
            $cnameRecords = dns_get_record($domainName, DNS_CNAME);
            $nsRecords = dns_get_record($domainName, DNS_NS);
        } catch (\Exception $e) {
            return response()->json(['error' => 'A temporary server error occurred while fetching DNS records. Please try again later.'], 500);
        }

        if (empty($aRecords)) {
            $aRecords = [['target' => 'No A Record Found']];
        }
        if (empty($cnameRecords)) {
            $cnameRecords = [['target' => 'No CNAME Found']];
        }
        if (empty($nsRecords)) {
            $nsRecords = [['target' => 'No Nameservers Found']];
        }

        return response()->json([
            'aRecords' => $this->formatDnsRecords($aRecords, 'ip'),
            'cnameRecords' => $this->formatDnsRecords($cnameRecords, 'target'),
            'nsRecords' => $this->formatDnsRecords($nsRecords, 'target')
        ]);
    }

    private function formatDnsRecords($records, $key)
    {
        return array_map(function ($record) use ($key) {
            return $record[$key];
        }, $records);
    }

}