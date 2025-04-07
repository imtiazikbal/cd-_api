<?php

namespace App\Http\Controllers\API\V1\Theme\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThemeRequest;
use App\Models\ActiveTheme;
use App\Models\Theme;
use App\Models\User;

class LandingPageTemplateController extends Controller
{
    public function index()
    {
        try {
            $themes = Theme::with('landing_theme_image')->where('type', 'landing')->get();

            return response()->json([
                'success' => true,
                'data'    => $themes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }

    public function active(ThemeRequest $request)
    {
        try {

            $merchant = User::where('role', 'merchant')->find($request->header('id'));

            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Merchant not Found',
                ], 404);
            }

            $theme = Theme::where('id', $request->landing_theme_id)->where('type', 'landing')->first();

            if(!$theme) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Theme not Found',
                ], 404);
            }

            $activeTheme = ActiveTheme::where('shop_id', $merchant->shop->id)->first();

            if (!$activeTheme) {

                $storeActiveTheme = new ActiveTheme();
                $storeActiveTheme->shop_id = $merchant->shop->id;
                $storeActiveTheme->user_id = $merchant->id;
                $storeActiveTheme->landing_theme_id = $request->landing_theme_id;
                $storeActiveTheme->multiple_theme_id = null;
                $storeActiveTheme->save();

            }

            $activeTheme->landing_theme_id = $request->landing_theme_id;
            $activeTheme->multiple_theme_id = null;
            $activeTheme->save();

            return response()->json([
                'success' => true,
                'msg'     => 'Landing page template Active Successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg'     => $e->getMessage(),
            ], 400);
        }
    }
}
