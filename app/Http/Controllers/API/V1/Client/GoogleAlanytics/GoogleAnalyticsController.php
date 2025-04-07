<?php

namespace App\Http\Controllers\API\V1\Client\GoogleAlanytics;

use App\Http\Controllers\Controller;
use App\Http\Resources\OtherScriptResource;
use App\Models\OtherScript;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoogleAnalyticsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $otherScript = OtherScript::query()->where('shop_id', $request->header('shop-id'))->first();

        return $this->sendApiResponse(new OtherScriptResource($otherScript), 'Other script list');
    }

    public function store(Request $request): JsonResponse
    {
        $otherScript = OtherScript::query()->where('shop_id', $request->header('shop-id'))->first();

        if (!$otherScript) {
            $otherScript = new OtherScript();
            $otherScript->shop_id = $request->header('shop-id');
        }
        $otherScript->fill($request->only(['gtm_head', 'gtm_body', 'google_analytics']));
        $otherScript->save();

        $responseMessage = '';

        if ($request->filled('gtm_head')) {
            $responseMessage = 'Google Tag Manager update successfully';
        } elseif ($request->filled('google_analytics')) {
            $responseMessage = 'Google Analytics update successfully';
        }

        return $this->sendApiResponse(new OtherScriptResource($otherScript), $responseMessage);
    }
}
