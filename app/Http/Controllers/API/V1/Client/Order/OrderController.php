<?php

namespace App\Http\Controllers\API\V1\Client\Order;

use App\Events\ProductStockEvent;
use App\Events\SendSmsEvent;
use App\Events\SmsBalanceEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClientOrderStoreRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\InvoiceOrderResource;
use App\Http\Resources\MerchantOrderResource;
use App\Models\CustomerInfo;
use App\Models\Media;
use App\Models\Order;
use App\Models\OrderCourier;
use App\Models\OrderDate;
use App\Models\OrderDetails;
use App\Models\OrderNote;
use App\Models\OrderPricing;
use App\Models\OrderStatus;
use App\Models\OrderTrackingTimeline;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Shop;
use App\Notifications\OrderNotification;
use App\Services\CacheService;
use App\Services\FraudCheckerService;
use App\Services\OrderService;
use App\Services\Sms;
use App\Traits\sendApiResponse;
use DateTime;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Helpers\OrderHelper;

/**
 * @property OrderService $orderService
 */
class OrderController extends Controller
{
    use sendApiResponse;

    /**
     * OrderController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request): JsonResponse
    {
        $data = $this->orderService->index($request);
        
        return $this->sendApiResponse(MerchantOrderResource::collection($data), 'Order list', '', [
            'order_count' => count((object) $data)
        ]);
    }

    public function order(int $id): JsonResponse
    {
        $orders = Order::with('order_details', 'customer')
            ->where('id', $id)
            ->firstOrFail();

        if (!$orders) {
            return $this->sendApiResponse('', 'Orders not found', 'NotFound');
        }

        return $this->sendApiResponse($orders);
    }

    public function store(ClientOrderStoreRequest $request, CacheService $cacheService): jsonResponse
    {
        $shop = Shop::where('shop_id', $request->header('shop-id'))->first();

        return DB::transaction(function () use ($request, $shop, $cacheService) {
            $fraudCheck = new FraudCheckerService;
            $fraudCheck->fraudCheck($request->input('customer_phone'));

            // Cache clear
            // $cacheService->cacheClear(["order-source-{$request->header('shop-id')}", "order-count-{$request->header('shop-id')}", "order-list-{$request->header('shop-id')}", "order-source-ratio-{$request->header('shop-id')}"]);

            $uniqueOrderNumber = $this->createOrderNoBasedOnShopNo($request->header('shop-id'));

            $order = Order::query()->create([
                'order_no'          => $uniqueOrderNumber,
                'user_id'           => $request->header('id'),
                'shop_id'           => $request->header('shop-id'),
                'address'           => $request->input('customer_address'),
                'phone'             => $request->input('customer_phone'),
                'order_type'        => $request->input('order_type'),
                'customer_name'     => $request->input('customer_name'),
                'delivery_location' => $request->input('delivery_location'),
                'tracking_code'     => $this->generate_tracking_code(),
            ]);

            CustomerInfo::query()->updateOrCreate(
                [
                    'user_id'     => $request->header('id'),
                    'merchant_id' => $request->header('shop-id'),
                    'phone'       => $request->input('customer_phone'),
                ],
                [
                    'name'    => $request->input('customer_name'),
                    'address' => $request->input('customer_address'),
                    'type'    => 'customer',
                ]
            );

            $grandTotal = 0;
            $shippingCost = 0;
            $extraErrorMessages = [];

            foreach ($request->input('product_id') as $key => $item) {

                $product = Product::query()->find($item);

                if($request->variant_id[$key] != 0) {
                    $variant = ProductVariation::query()->find($request->variant_id[$key]);
                    $discounted_price = $variant->price;
                    $variantId = $variant->id;
                } else {
                    $discounted_price = OrderHelper::getProductDiscountCalculation($product->id);
                    $variantId = null;
                }

                if($shop->order_perm_status == 1) {
                    $this->createOrderDetails($order, $key, $item, $request, $discounted_price, $variantId);

                    $grandTotal += $discounted_price * $request->input('product_qty')[$key];
                    $shippingCost += $request->input('shipping_cost')[$key];
                } else {
                    if($request->variant_id[$key] != 0) {
                        if($variant->quantity >= $request->input('product_qty')[$key]) {
                            $this->createOrderDetails($order, $key, $item, $request, $discounted_price, $variantId);

                            $grandTotal += $discounted_price * $request->input('product_qty')[$key];
                            $shippingCost += $request->input('shipping_cost')[$key];
                        } else {
                            $extraErrorMessages['errorMessages'][] = [
                                'variant' => 'This ' . $variant->variant . ' variant has not enough quantity'
                            ];
                        }
                    } else {
                        if($product->product_qty >= $request->input('product_qty')[$key]) {
                            $this->createOrderDetails($order, $key, $item, $request, $discounted_price, $variantId);

                            $grandTotal += $discounted_price * $request->input('product_qty')[$key];
                            $shippingCost += $request->input('shipping_cost')[$key];
                        } else {
                            $extraErrorMessages['errorMessages'][] = [
                                'product' => 'This ' . $product->product_name . ' has not enough quantity'
                            ];
                        }
                    }
                }
            }

            /**
             * if variant product qty lower than requested qty. it will give you error & will delete what order created before.
             */
            if($grandTotal < 1) {
                DB::table('orders')->where('id', $order->id)->delete();

                return $this->sendApiResponse('', 'Products has not enough quantity', '', $extraErrorMessages);
            }

            $order->pricing()->create([
                'shipping_cost' => $shippingCost,
                'grand_total'   => $grandTotal,
                'due'           => $grandTotal + $shippingCost,
            ]);

            $types = [
                'landing'=>'Landing Page',
                'website'=>'Website',
                'social'=>'Social Media',
                'phone'=>'Phone Call',
                'others'=>'Others',
            ];

            $note = "Pending = Order is being placed successfully from {$types[$order->order_type]}. Order No- {$uniqueOrderNumber} Billing amount- {$order->pricing->due}";

            OrderTrackingTimeline::query()->create([
                'order_id' => $order->id,
                'event' => 'pending',
                'note' => $note,
            ]);

            if ($request->filled('note')) {
                OrderNote::query()->create([
                    'order_id' => $order->id,
                    'type'     => Order::PENDING,
                    'note'     => $request->input('note')
                ]);
            }

            $order->config()->create();
            $order->courier()->create();
            $order->load('order_details', 'pricing', 'order_attach_images');

            $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();
            $decodeOrderSms = json_decode($shop->order_sms);

            if ($decodeOrderSms->pending === '1') {
                SendSmsEvent::dispatch($order, $shop);
            }

            $dateOrder = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            $orderTime = $dateOrder->format("M d Y h:i A");
            $notifyInfo = [
                'text'       => "Order ID: " . $order->order_no . " has been Placed ",
                'shop_id'    => $request->header('shop-id'),
                'order_time' => $orderTime,
                'type'       => 'order'
            ];
            Notification::send($shop, new OrderNotification($notifyInfo));

            $order->refresh();
            // Order attach image store
            if($shop->order_attach_img_perm){
                if ($request->hasFile('order_attach_img')) {
                    $orderAttachImgs = $request->file('order_attach_img');
        
                    foreach ($orderAttachImgs as $image) {
                        $filePath = 'media/order-attach-img/' . $request->header('id');
                        Media::upload($order, $image, $filePath, 'order_attach_img');
                    }
                }
            }
            
            // Socket implement
            // $orderWebsocket = new OrderWebsocketService;
            // $orderWebsocket->orderCreateSocket($order);

            return $this->sendApiResponse(new MerchantOrderResource($order), 'Order Created Successfully', '', $extraErrorMessages);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $order = Order::query()
            ->with('order_details', 'pricing', 'order_attach_images')
            ->where('id', $id)
            ->where('shop_id', request()->header('shop-id'))
            ->first();

        if (!$order) {
            return $this->sendApiResponse('', 'Order not found');
        }

        return $this->sendApiResponse(new MerchantOrderResource($order));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $order = Order::query()->with('shop')->find($id);
            
            //Product quantity checking while order a product
            $productQtyCheckErrorMsg = OrderHelper::handleProductQtyCheck($request);

            if(count($productQtyCheckErrorMsg) > 0){
                return $this->sendApiResponse([], 'Invalid quantity', '', $productQtyCheckErrorMsg, 400);
            }

            // Delete previous order details
            $order->order_details()->delete();
            
            if ($request->filled('customer_name')) {
                $order->customer_name = $request->input('customer_name');
            }

            if ($request->filled('phone')) {
                $fraudCheck = new FraudCheckerService;
                $fraudCheck->fraudCheck($request->input('phone'));
                $order->phone = $request->input('phone');
            }

            if ($request->filled('order_type')) {
                $order->order_type = $request->input('order_type');
            }

            if ($request->filled('address')) {
                $order->address = $request->input('address');
            }

            if ($request->filled('order_status')) {
                $order->order_status = $request->input('order_status');
            }
            
            $order->save();
            $order->refresh();
            OrderHelper::handleOrderUpdateProcess($request, $order);

            $order->load('order_details', 'pricing', 'order_attach_images');
            $order = $this->orderService->updatePricing($order);

            // Order attach image update
            OrderHelper::handleOrderAttachImageUpdate($order, $request);
            
            return $this->sendApiResponse(new MerchantOrderResource($order), 'Order Updated Successfully');
        });
    }

    /**
     * @param OrderRequest $request
     * @return JsonResponse
     * @property string $order_status
     */
    public function order_status_update(OrderRequest $request): JsonResponse
    {
        $order = Order::query()->with('order_details')
            ->where('id', $request->input('order_id'))
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        if (!$order) {
            return $this->sendApiResponse('', 'Order Not Found', 'NotFound');
        }

        if ($request->input('status') === Order::CONFIRMED) {
            $order->order_status = $request->input('status');
            $order->status_update_date = now();

            OrderDate::query()->updateOrCreate([
                'order_id' => $order->id,
                'type'     => $order->order_status
            ], [
                'date' => Carbon::today()
            ]);

            foreach ($order->order_details as $details) {
                if($details->product) {
                    $details->product->update([
                        'product_qty' => $details->product->product_qty - $details->product_qty
                    ]);
                }

                if($details->variation) {
                    $details->variation->update([
                        'quantity' => $details->variation->quantity - $details->product_qty
                    ]);
                }
            }
            $productId = $order->order_details[0]->product_id;
            ProductStockEvent::dispatch($productId);

            $note = "Confirmed = order has been confirmed.";

            OrderTrackingTimeline::query()->create([
                'order_id' => $order->id,
                'event' => 'confirmed',
                'note' => $note,
            ]);
        }

        if ($request->input('status') === Order::PENDING) {
            $order->order_status = $request->input('status');
            $order->status_update_date = now();
        }

        if ($request->input('status') === Order::FOLLOWUP) {
            $order->order_status = $request->input('status');
            $order->status_update_date = now();
        }

        if ($request->input('status') === Order::CANCELLED) {
            $order->order_status = $request->input('status');
            $order->status_update_date = now();

            foreach ($order->order_details as $details) {
                $details->product->update([
                    'product_qty' => $details->product->product_qty + $details->product_qty
                ]);

                if($details->variation) {
                    $details->variation->update([
                        'quantity' => $details->variation->quantity + $details->product_qty
                    ]);
                }
            }

            $note = "Cancelled = Order has been cancelled.";

            OrderTrackingTimeline::query()->create([
                'order_id' => $order->id,
                'event' => 'cancelled',
                'note' => $note,
            ]);
        }

        if ($request->input('status') === Order::RETURNED) {
            $order->order_status = $request->input('status');
            $order->status_update_date = now();

            foreach ($order->order_details as $details) {
                $details->product->update([
                    'product_qty' => $details->product->product_qty + $details->product_qty
                ]);

                if($details->variation) {
                    $details->variation->update([
                        'quantity' => $details->variation->quantity + $details->product_qty
                    ]);
                }
            }
        }

        if ($request->input('status') === Order::SHIPPED) {
            $order->order_status = $request->input('status');
            $order->status_update_date = now();
        }

        if ($request->input('status') === Order::DELIVERED) {
            $order->order_status = $request->input('status');
            $order->status_update_date = now();
        }

        if ($request->input('status') === Order::HOLDON) {
            $order->order_status = $request->input('status');
            $order->status_update_date = now();
        }
        $order->save();

        OrderStatus::query()->create([
            'order_id' => $order->id,
            'type'     => $order->order_status
        ]);

        $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();

        if ($shop->sms_balance < 0.30) {
            return $this->sendApiResponse('', 'Insufficient Balance');
        }

        if ((!$shop->sms_balance) < .30 && $order->order_status !== 'follow_up') {

            $status = $order->order_status;

            if ($status !== 'follow_up' && $status !== 'hold_on') {

                $orderSmsStatus = json_decode($shop->order_sms, true);
                $orderStatus = $orderSmsStatus[$status];
                $shopId = $shop->id;

                if ($orderStatus === "1") {
                    $shop->sms_balance -= 0.30;
                    $shop->sms_sent = $shop->sms_sent + 1;
                    $shop->save();

                    $sms = new Sms();
                    $sms->orderStatusUpdate($order->phone, $order, $shop);
                    SmsBalanceEvent::dispatch($shopId);
                }
            }
        }

        return $this->sendApiResponse('', 'Order Status Update Successfully');
    }

    public function order_invoice(Request $request): JsonResponse
    {
        $orderIds = $request->input('order_id');

        if (!is_array($orderIds)) {
            $orderIds = explode(',', $orderIds);
        }

        $orders = Order::query()
            ->with('order_details', 'pricing', 'config', 'courier')
            ->whereIn('id', $orderIds)
            ->where('shop_id', $request->header('shop-id'))
            ->get();

        $shop = Shop::query()
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        if (!$shop) {
            return $this->sendApiResponse(null, 'Shop Not found', 'NotFound');
        }

        $result = [];

        foreach ($orders as $order) {
            if (!$order) {
                continue;
            }
            $order['shop'] = $shop;
            $invoiceOrderResource = new InvoiceOrderResource($order);
            $result[] = $invoiceOrderResource->toArray($request);
        }

        return $this->sendApiResponse($result);
    }

    public function advancePayment(Request $request, int $id): JsonResponse
    {
        $order = Order::query()->with('order_details', 'pricing')->find($id);
        $order->pricing->update([
            'advanced' => $request->input('advanced') ?? 0
        ]);
        $order = $this->orderService->updatePricing($order);
        $note = "Advance: {$order->pricing->advanced}TK advance has been deducted from your total order value, COD Amount - {$order->pricing->due}TK";

        OrderTrackingTimeline::query()->create([
            'order_id' => $order->id,
            'event' => 'advance',
            'note' => $note,
        ]);
        return $this->sendApiResponse(new MerchantOrderResource($order), 'Advance payment updated');
    }

    public function noteUpdateByStatus(Request $request, int $id): JsonResponse
    {
        $note = OrderNote::query()->updateOrCreate([
            'order_id' => $id,
            'type'     => 'order'
        ], [
            'note' => $request->input('note'),
        ]);

        foreach (['invoice_note', 'courier_note'] as $noteType) {
            if ($request->filled($noteType)) {
                OrderNote::query()->updateOrCreate([
                    'order_id' => $id,
                    'type'     => $noteType === 'invoice_note' ? 'invoice' : 'courier'
                ], [
                    'note' => $request->input($noteType),
                ]);
            }
        }

        return $this->sendApiResponse($note, 'Note updated Successfully');
    }

    public function dateUpdateByStatus(Request $request, int $id): JsonResponse
    {
        $type = $this->checkStatusValidity($request->input('type'));

        if ($type === false) {
            return $this->sendApiResponse('', 'Please add valid Status type');
        }
        $note = OrderDate::query()->updateOrCreate([
            'order_id' => $id,
            'type'     => $type
        ], [
            'date' => $request->input('date'),
        ]);

        return $this->sendApiResponse($note, 'Date updated for ' . $type . ' order');
    }

    public function updateDiscount(Request $request, int $id): JsonResponse
    {
        $order = Order::query()->where('id', $id)->first();

        if (Str::contains($request->input('discount'), '%')) {
            $discount = Str::replace('%', '', $request->input('discount'));
            $type = Order::PERCENT;
        } else {
            $discount = $request->input('discount');
            $type = Order::AMOUNT;
        }

        $order->pricing->update([
            'discount'      => $discount,
            'discount_type' => $type,
        ]);

        $order = $this->orderService->updatePricing($order);
        $grand_total = $order->pricing->grand_total;

        if ($type === Order::PERCENT) {
            $discount_tk = ceil($grand_total * ($order->pricing->discount / 100));
        } else {
            $discount_tk = ceil($order->pricing->discount);
        }

        $note = "Discount: {$discount_tk}Tk discount has been given.";

        OrderTrackingTimeline::query()->create([
            'order_id' => $order->id,
            'event' => 'discount',
            'note' => $note,
        ]);

        return $this->sendApiResponse(new MerchantOrderResource($order), 'Discount added successfully');
    }

    public function getOrderCounts(Request $request): JsonResponse
    {
        $data = $this->orderService->orderCounts($request->header('shop-id'));
        return $this->sendApiResponse($data);
    }

    public function orderStatistic(Request $request): JsonResponse
    {
        $data['total'] = $this->orderService->getTotalStatistic($request);
        $data['confirmed'] = $this->orderService->getConfirmedStatistic($request);
        $data['pending'] = $this->orderService->getPendingStatistic($request);
        $data['cancel'] = $this->orderService->getCancelStatistic($request);
        $data['sales'] = $this->orderService->getSalesStatistic($request);
        $data['phone_call'] = $this->orderService->getPhoneStatistic($request);
        $data['social'] = $this->orderService->getSocialStatistic($request);
        $data['multipage'] = $this->orderService->getMultipleStatistic($request);
        $data['landing'] = $this->orderService->getLandingStatistic($request);
        $data['advance_payment'] = $this->orderService->getAdvancePaymentStatistic($request);
        $data['discount_payment'] = $this->orderService->getDiscountPaymentStatistic($request);

        return $this->sendApiResponse($data, 'Order statistics');
    }

    public function deliveryReport(Request $request): JsonResponse
    {
        $data = $this->orderService->getDeliveryReport($request);

        return $this->sendApiResponse($data);
    }

    public function delete(int $id): JsonResponse
    {
        $OD = OrderDate::query()->where('order_id', $id)->first();
        $ON = OrderNote::query()->where('order_id', $id)->first();
        $OS = OrderStatus::query()->where('order_id', $id)->first();
        $ODS = OrderDetails::query()->where('order_id', $id)->first();
        $OP = OrderPricing::query()->where('order_id', $id)->first();
        $OC = OrderCourier::query()->where('order_id', $id)->first();

        if ($OD) {
            $OD->delete();
        }

        if ($ON) {
            $ON->delete();
        }

        if ($OS) {
            $OS->delete();
        }

        if ($ODS) {
            $ODS->delete();
        }

        if ($OP) {
            $OP->delete();
        }

        if ($OC) {
            $OC->delete();
        }

        $order = Order::query()->findOrFail($id);
        $order->delete();

        return $this->sendApiResponse('', 'Order deleted successfully');
    }

    public function bulkdelete(Request $request): JsonResponse
    {
        $orderIds = $request->input('orders');

        $OD = OrderDate::query()->whereIn('order_id', $orderIds)->pluck('id')->toArray();
        $ON = OrderNote::query()->whereIn('order_id', $orderIds)->pluck('id')->toArray();
        $OS = OrderStatus::query()->whereIn('order_id', $orderIds)->pluck('id')->toArray();
        $ODS = OrderDetails::query()->whereIn('order_id', $orderIds)->pluck('id')->toArray();
        $OP = OrderPricing::query()->whereIn('order_id', $orderIds)->pluck('id')->toArray();
        $OC = OrderCourier::query()->whereIn('order_id', $orderIds)->pluck('id')->toArray();

        OrderDate::query()->whereIn('id', $OD)->delete();
        OrderNote::query()->whereIn('id', $ON)->delete();
        OrderStatus::query()->whereIn('id', $OS)->delete();
        OrderDetails::query()->whereIn('id', $ODS)->delete();
        OrderPricing::query()->whereIn('id', $OP)->delete();
        OrderCourier::query()->whereIn('id', $OC)->delete();

        Order::query()->whereIn('id', $orderIds)->delete();

        return $this->sendApiResponse('', 'Orders deleted successfully');
    }

    /**
     * @param $value
     * @return string
     */
    public function checkStatusValidity(string $value): string
    {
        if ($value === Order::PENDING) {
            return Order::PENDING;
        }

        if ($value === Order::CONFIRMED) {
            return Order::CONFIRMED;
        }

        if ($value === Order::FOLLOWUP) {
            return Order::FOLLOWUP;
        }

        if ($value === Order::CANCELLED) {
            return Order::CANCELLED;
        }

        if ($value === Order::RETURNED) {
            return Order::RETURNED;
        }

        if ($value === Order::SHIPPED) {
            return Order::SHIPPED;
        }

        if ($value === Order::DELIVERED) {
            return Order::DELIVERED;
        }

        return false;
    }

    // recent order
    public function recentOrder(Request $request): JsonResponse
    {
        $orders = Order::with('pricing', 'order_details')
            ->where('shop_id', $request->header('shop-id'))
            ->OrderBy('id', 'desc')->take(5)
            ->get();

        return $this->sendApiResponse($orders, 'Recent order products');
    }

    public function printSelectedOrders(Request $request): JsonResponse
    {

        $orderIds = $request->input('order_ids');

        if (empty($orderIds)) {
            return $this->sendApiResponse('', 'No order IDs selected', 'NotFound');
        }

        $orders = Order::query()->with('order_details', 'pricing')
            ->whereIn('id', $orderIds)
            ->where('shop_id', $request->header('shop-id'))
            ->get();

        if ($orders->isEmpty()) {
            return $this->sendApiResponse('', 'No orders found with the selected Orders', 'NotFound');
        }
        $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();

        foreach ($orders as $order) {
            $order['shop'] = $shop;
        }

        return $this->sendApiResponse(collect(new InvoiceOrderResource($orders)));
    }

    public function orderGlobalSearch(Request $request): JsonResponse
    {
        $terms = '%' . $request->search . '%';
        $result = Order::query()
            ->with('order_details')
            ->where(function ($query) use ($terms) {
                $query->where('customer_name', 'like', $terms)
                    ->orWhere('phone', 'like', $terms)
                    ->orWhere('order_no', 'like', $terms)
                    ->orWhere('address', 'like', $terms)
                    ->orWhereHas('courier', function ($query) use ($terms) {
                        $query->where('consignment_id', 'like', $terms);
                    });
            })
            ->where('shop_id', $request->header('shop-id'))
            ->get();

        if (count($result) === 0) {
            return $this->sendApiResponse('', 'Data not found !');
        }

        return $this->sendApiResponse(MerchantOrderResource::collection($result), 'Order searching result');
    }

    public function orderDelete($id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return $this->sendApiResponse('', 'Order Not Found', 'false');
        }
        $order->delete();

        return $this->sendApiResponse('', 'Order Removed Successfully');
    }

    public function orderTrashedList(Request $request): JsonResponse
    {
        $perPage = $request->perPage ? $request->perPage : 10;
        $softDeleteList = Order::onlyTrashed()
            ->with(['order_details', 'pricing'])
            ->where('shop_id', $request->header('shop-id'))
            ->orderByDesc('deleted_at')
            ->paginate($perPage);

        if (!$softDeleteList) {
            return $this->sendApiResponse('', 'Data not found !', 'false');
        }

        return $this->sendApiResponse(MerchantOrderResource::collection($softDeleteList));
    }

    private function createOrderDetails($order, $key, $item, $request, $discounted_price, $variantId)
    {
        $order->order_details()->create(
            [   
                'product_id'    => $item,
                'product_qty'   => $request->input('product_qty')[$key],
                'unit_price'    => $discounted_price,
                'shipping_cost' => $request->input('shipping_cost')[$key],
                'variant'       => $variantId
            ]
        );
    }

    private function generate_tracking_code(int $length = 10, int $tried = 0): ?string
    {
        $uniqueTrackingCode = Str::upper(Str::random($length));

        if(Order::where('tracking_code', $uniqueTrackingCode)->exists()) {
            if ($tried >= 10) {
                return null;
            }
            $tried++;
            return $this->generate_tracking_code($length, $tried);
        }
        return $uniqueTrackingCode;
    }
}