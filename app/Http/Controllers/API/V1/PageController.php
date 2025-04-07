<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\MerchantBaseController;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;

class PageController extends MerchantBaseController
{
    use sendApiResponse;

    /**
     * @param int $shop_id
     * @param $page
     * @return JsonResponse
     */
    public function show(int $shop_id, $page): JsonResponse
    {
        $page = Page::query()
                ->with('themes', 'product', 'product.variations', 'product.variations.media', 'Footer')
                ->where('shop_id', $shop_id)
                ->where('slug', $page)
                ->first();

        if(!$page) {
            return $this->sendApiResponse('', 'No page found', 'NotFound');
        }

        return $this->sendApiResponse(new PageResource($page));
    }
}
