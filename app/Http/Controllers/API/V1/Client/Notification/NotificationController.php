<?php

namespace App\Http\Controllers\API\V1\Client\Notification;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationController extends Controller
{
    use sendApiResponse;

    protected $dataNotFoundMsg;

    public function __construct()
    {
        $this->dataNotFoundMsg = 'Notification not found !';
    }

    public function NotificationShow(Request $request, $id = null): JsonResponse
    {
        if ($request->input('type') == 'order') {

            $shopInfo = Shop::find($request->input('notify_id'));

            if ($shopInfo) {
                $notifies = $shopInfo->notifications;
                $shopId = $shopInfo->shop_id;

                if ($id) {
                    $notifyLimitData = array_slice($notifies->toArray(), 0, $id);

                    if (count($notifyLimitData) > 0) {
                        $orderNotification = [];

                        foreach ($notifyLimitData as $notify) {
                            $notifyOrderShopId = $notify['data']['shop_id'];
                            $notifyOrderType = $notify['data']['type'];

                            if ($notifyOrderShopId == $shopId && $notifyOrderType == $request->input('type')) {
                                $orderNotification[] = [
                                    'data' => $notify['data'],
                                    'read' => $notify['read_at']
                                ];
                            }
                        }
                        echo 'run';

                        return $this->sendApiResponse($this->paginateData($orderNotification), 'Order notification show');
                    } else {
                        return $this->sendApiResponse('', $this->dataNotFoundMsg);
                    }
                } else {
                    $notifyArr = $notifies;

                    if (count($notifyArr) > 0) {
                        if ($notifyArr) {

                            $orderNotification = [];

                            foreach ($notifyArr as $notification) {
                                if ($notification->data['shop_id'] == $shopId && $notification->data['type'] == $request->input('type')) {
                                    $orderNotification[] = [
                                        'data' => $notification->data,
                                        'read' => $notification->read_at
                                    ];
                                }
                            }

                            return $this->sendApiResponse($this->paginateData($orderNotification), 'Order notification show');
                        }
                    } else {
                        return $this->sendApiResponse('', $this->dataNotFoundMsg);
                    }
                }
            } else {
                return $this->sendApiResponse('', 'Data not found !');
            }
        } else {
            return $this->sendApiResponse('', $this->dataNotFoundMsg);
        }

        if ($request->input('type') == 'product') {
            $productInfo = Product::find($request->input('notify_id'));

            if ($productInfo) {

                $notifies = $productInfo->notifications;
                $productId = $productInfo->id;


                if ($id) {
                    $notifyLimitData = array_slice($notifies->toArray(), 0, $id);

                    if (count($notifyLimitData) > 0) {
                        $productNotification = [];

                        foreach ($notifyLimitData as $notify) {
                            $notifyProductId = $notify['data']['product_id'];
                            $notifyType = $notify['data']['type'];

                            if ($notifyProductId == $productId && $notifyType == $request->input('type')) {
                                $productNotification[] = [
                                    'data' => $notify['data'],
                                    'read' => $notify['read_at']
                                ];
                            }
                        }

                        return $this->sendApiResponse($this->paginateData($productNotification), 'Product notification show');
                    } else {
                        return $this->sendApiResponse('', $this->dataNotFoundMsg);
                    }
                } else {
                    $notifyArr = $notifies;

                    if (count($notifyArr) > 0) {
                        if ($notifyArr) {
                            $productNotification = [];

                            foreach ($notifyArr as $notification) {
                                $notifyProductId = $notification->data['product_id'];
                                $notifyProductType = $notification->data['type'];

                                if ($notifyProductId == $productId && $notifyProductType == $request->input('type')) {
                                    $productNotification[] = [
                                        'data' => $notification->data,
                                        'read' => $notification->read_at
                                    ];
                                }
                            }

                            return $this->sendApiResponse($this->paginateData($productNotification), 'Product notification show');
                        }
                    } else {
                        return $this->sendApiResponse('', $this->dataNotFoundMsg);
                    }
                }
            }

            return $this->sendApiResponse('', $this->dataNotFoundMsg);
        } else {
            return $this->sendApiResponse('', $this->dataNotFoundMsg);
        }
    }

    private function paginateData(array $data): LengthAwarePaginator
    {
        $collection = collect($data);
        $page = request()->page ?? 1;
        $perPage = request()->query('perPage') ?? 10;

        return new LengthAwarePaginator(collect($collection)->forPage($page, $perPage)
            ->values(), $collection->count(), $perPage, $page, []);
    }

    public function NotificationRead(Request $request): JsonResponse
    {
        $notifyId = $request->input('notify_id');
        $type = $request->input('type');

        if ($type == 'product') {
            $product = Product::find($notifyId);

            if ($product) {
                $notifyArr = $product->notifications;

                if (count($notifyArr) > 0) {
                    $notifyArr->markAsRead();

                    return $this->sendApiResponse('', 'Product notification read');
                } else {
                    return $this->sendApiResponse('', 'Product notification not found !');
                }
            } else {
                return $this->sendApiResponse('', 'Product notification not found !');
            }
        }

        if ($type == 'order') {
            $order = Shop::find($notifyId);

            if ($order) {
                $notifyArr = $order->notifications;

                if (count($notifyArr) > 0) {
                    $notifyArr->markAsRead();

                    return $this->sendApiResponse('', 'Order notification read');
                } else {
                    return $this->sendApiResponse('', 'Order notification not found !');
                }
            } else {
                return $this->sendApiResponse('', 'Order notification not found !');
            }
        }
    }
}
