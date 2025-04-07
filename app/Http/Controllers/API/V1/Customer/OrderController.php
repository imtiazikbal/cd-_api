<?php

namespace App\Http\Controllers\API\V1\Customer;

use App\Events\SendSmsEvent;
use App\Helpers\OrderHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerOrderStoreRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Jobs\FraudChecker;
use App\Models\CustomerInfo;
use App\Models\MyAddons;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\OrderNote;
use App\Models\OrderTrackingTimeline;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ShippingSetting;
use App\Models\Shop;
use App\Notifications\OrderNotification;
use App\Services\FraudCheckerService;
use App\Services\OrderWebsocketService;
use App\Services\Sms;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(CustomerOrderStoreRequest $request): JsonResponse
    {
        $shop = Shop::where('shop_id', $request->header('shop-id'))->first();

        return DB::transaction(function () use ($request, $shop) {
            $fraudCheck = new FraudCheckerService;
            $fraudCheck->fraudCheck($request->input('customer_phone'));

            $identity = DB::table('orders')
                ->where('visitor_id', $request->input('visitor_id'))
                ->where('shop_id', $request->header('shop-id'))
                ->latest()
                ->first();

            $t = optional($identity)->created_at;

            // shop wise shipping cost
            $deliveryLocation = $request->input('delivery_location');
            $separateShippingCost = ShippingSetting::query()
                    ->where('shop_id', $request->header('shop-id'))
                    ->where('status', 1)
                    ->first();

            $sevenDaysAgo = Carbon::now()->subDays(7);
            $myAddons = MyAddons::where('shop_id', $request->header('shop-id'))
                ->where('addons_id', '13')
                ->where('status', '1')
                ->first();

            // default location define with order_type
            $defaultDeliveryLocation = $this->orderTypeDefineWithDefaultLocation($separateShippingCost, $shop, $request);

            $orderOtpPermStatus = Shop::query()
            ->select('id', 'shop_id', 'order_otp_perm')
            ->where('shop_id', $request->header('shop-id'))
            ->where('order_otp_perm', 1)
            ->first();

            if($myAddons && ($orderOtpPermStatus || $t <= $sevenDaysAgo == false)) {
                $hrs = false;
            } else {
                $hrs = true;
            }

            $uniqueOrderNumber = $this->createOrderNoBasedOnShopNo($request->header('shop-id'));

            $order = Order::create([
                'order_no'          => $uniqueOrderNumber,
                'user_id'           => $shop->user_id,
                'shop_id'           => $request->header('shop-id'),
                'address'           => $request->input('customer_address'),
                'phone'             => $request->input('customer_phone'),
                'order_type'        => $request->input('order_type'),
                'customer_name'     => $request->input('customer_name'),
                'delivery_location' => ($separateShippingCost) ? $defaultDeliveryLocation : $request->input('delivery_location'),
                'visitor_id'        => $request->input('visitor_id'),
                'cod'               => $request->input('cod'),
                'otp_verified'      => 0,
                'order_status'      => $hrs ? 'pending' : 'unverified',
                'otp_sent'          => $hrs ? 0 : 1,
                'tracking_code' => $this->generate_tracking_code(),
            ]);

            CustomerInfo::updateOrCreate(
                [
                    'user_id'     => $shop->user_id,
                    'merchant_id' => $request->header('shop-id'),
                    'phone'       => $request->input('customer_phone'),
                ],
                [
                    'name'    => $request->input('customer_name'),
                    'address' => $request->input('customer_address'),
                    'type'    => 'guest',
                ]
            );

            $grandTotal = 0;
            $shippingCost = $separateShippingCost->$deliveryLocation ?? 0;
            $extraErrorMessages = [];

            foreach ($request->input('product_id') as $key => $item) {
                $product = Product::find($item);

                if ($request->variant_id[$key] != 0) {
                    $variant = ProductVariation::find($request->variant_id[$key]);
                    $discountedPrice = $variant->price;
                    $variantId = $variant->id;
                } else {
                    $discountedPrice = OrderHelper::getProductDiscountCalculation($product->id);
                    $variantId = null;
                }

                if($request->input('order_type') == 'landing') {
                    if ($product->delivery_charge === Product::PAID) {
                        $orderDetails = OrderDetails::where('order_id', $order->id)->where('product_id', $item)->first();

                        if(!$orderDetails) {
                            $shippingCost += $product[$request->input('delivery_location')];
                        }
                    }
                } else {
                    if(!$separateShippingCost) {
                        if ($product->delivery_charge === Product::PAID) {
                            $orderDetails = OrderDetails::where('order_id', $order->id)->where('product_id', $item)->first();

                            if(!$orderDetails) {
                                $shippingCost += $product[$request->input('delivery_location')];
                            }
                        }
                    }
                }

                if($shop->order_perm_status == 1) {
                    $this->createOrderDetails($order, $key, $item, $request, $discountedPrice, $variantId);
                    $grandTotal += $discountedPrice * $request->input('product_qty')[$key];
                } else {
                    if($request->variant_id[$key] != 0) {
                        if($variant->quantity >= $request->input('product_qty')[$key]) {
                            $this->createOrderDetails($order, $key, $item, $request, $discountedPrice, $variantId);
                            $grandTotal += $discountedPrice * $request->input('product_qty')[$key];
                        } else {
                            $extraErrorMessages['errorMessages'][] = [
                                'variant' => 'This ' . $variant->variant . ' variant has not enough quantity'
                            ];
                        }
                    } else {
                        if($product->product_qty >= $request->input('product_qty')[$key]) {
                            $this->createOrderDetails($order, $key, $item, $request, $discountedPrice, $variantId);
                            $grandTotal += $discountedPrice * $request->input('product_qty')[$key];
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
            ];

            $note = "Pending = Order is being placed successfully from {$types[$order->order_type]}. Order No- {$uniqueOrderNumber} Billing amount- {$order->pricing->due}";

            OrderTrackingTimeline::query()->create([
                'order_id' => $order->id,
                'event' => 'pending',
                'note' => $note,
            ]);

            if ($request->filled('note')) {
                OrderNote::create([
                    'order_id' => $order->id,
                    'type'     => 'order',
                    'note'     => $request->input('note'),
                ]);
            }

            $order->config()->create();
            $order->courier()->create();

            $this->orderNoiticationSend($request, $order, $shop);

            if (!$hrs) {
                if ($shop->sms_balance > 0.30) {
                    $shop->sms_balance -= 0.30;
                    $shop->sms_sent++;
                    $shop->save();

                    $send = new Sms();
                    $response = $send->identifyOtp($order, $shop);

                    $order->refresh();
                    $orderWebsocket = new OrderWebsocketService;
                    $orderWebsocket->orderCreateSocket($order);

                    if ($response->status() == 200) {
                        return response()->json([
                            'message' => 'OTP Sent Successfully',
                            'data'    => ['otp_sent' => $order->otp_sent],
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'message' => 'SMS balance is insufficient, Please contact our support',
                        'data'    => '',
                    ], 251);
                }
            } else {
                $decodeOrderSms = json_decode($shop->order_sms);

                if ($decodeOrderSms->pending === '1') {
                    SendSmsEvent::dispatch($order, $shop);
                }
            }

            $order->load('order_details');
            $order->load('pricing');

            $order->refresh();
            $orderWebsocket = new OrderWebsocketService;
            $orderWebsocket->orderCreateSocket($order);

            return $this->sendApiResponse(new OrderResource($order), 'Order Placed Successfully', '', $extraErrorMessages);
        });
    }

    public function orderNoiticationSend($request, $order, $shop)
    {
        $dateOrder = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
        $orderTime = $dateOrder->format("M d Y h:i A");
        $notifyInfo = [
            'text'       => "Order ID: " . $order->order_no . " has been Placed ",
            'shop_id'    => $request->header('shop-id'),
            'order_time' => $orderTime,
            'type'       => 'order',
        ];

        Notification::send($shop, new OrderNotification($notifyInfo));
    }

    /**
     *  Order type define with default location
     */
    public function orderTypeDefineWithDefaultLocation(object $separateShippingCost = null, object $shop, object $request)
    {
        if($shop->default_delivery_location) {
            if($separateShippingCost && $request->input('order_type') == 'landing') {
                $defaultDeliveryLocation = match($request->input('delivery_location')) {
                    'inside_dhaka'  => 'inside_' . $shop->default_delivery_location,
                    'outside_dhaka' => 'outside_' . $shop->default_delivery_location,
                    'subarea'       => 'subarea_' . $shop->default_delivery_location,
                };
            } elseif($separateShippingCost) {
                $defaultDeliveryLocation = $request->input('delivery_location') . '_' . $shop->default_delivery_location;
            } else {
                $defaultDeliveryLocation = '';
            }
        } else {
            if($separateShippingCost && $request->input('order_type') == 'landing') {
                $defaultDeliveryLocation = match($request->input('delivery_location')) {
                    'inside_dhaka'  => 'inside_dhaka',
                    'outside_dhaka' => 'outside_dhaka',
                    'subarea'       => 'subarea_dhaka',
                };
            } elseif($separateShippingCost) {
                $defaultDeliveryLocation = $request->input('delivery_location') . '_' . 'dhaka';
            } else {
                $defaultDeliveryLocation = '';
            }
        }

        return $defaultDeliveryLocation;
    }

    /**
     * Verify the OTP and complete the checkout process.
     *
     * @param OrderRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function verify(OrderRequest $request): JsonResponse
    {
        $order = Order::query()
            ->where('shop_id', $request->header('shop-id'))
            ->OrWhere('phone', $request->input('customer_phone'))
            ->orderByDesc('id')
            ->first();

        if ($order->identify_otp === $request->input('otp')) {

            $order->otp_verified = 1;
            $order->order_status = 'pending';
            $order->save();

            $identity = Order::query()
                ->where('shop_id', $request->header('shop-id'))
                ->OrWhere('phone', $request->input('customer_phone'))
                ->OrWhere('order_status', 'unverified')
                ->first();

            if ($identity->otp_verified === '0') {
                DB::table('orders')
                    ->where('phone', $request->input('phone'))
                    ->OrWhere('shop_id', $request->header('shop-id'))
                    ->OrWhere('order_status', 'unverified')
                    ->delete();
            }

            $shop = Shop::query()
                ->where('shop_id', $request->header('shop-id'))
                ->first();
            $decode_orderSms = json_decode($shop->order_sms);

            if ($decode_orderSms->pending === '1') {
                SendSmsEvent::dispatch($order, $shop);
            }

            $date_order = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
            $order_time = $date_order->format("M d Y h:i A");
            $notify_info = [
                'text'       => "Order ID: " . $order->order_no . " has been Placed ",
                'shop_id'    => $request->header('shop-id'),
                'order_time' => $order_time,
                'type'       => 'order'
            ];
            Notification::send($shop, new OrderNotification($notify_info));

            return $this->sendApiResponse($order, 'Checkout completed successfully');
        }

        return response()->json([
            'message' => 'Invalid OTP',
            'data'    => ['otp_verified' => $order->otp_verified],
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::query()
            ->with('order_details', 'pricing', 'order_details.variation')
            ->where('id', $id)->where('shop_id', request()->header('shop-id'))->first();
        if (!$order) {
            return $this->sendApiResponse('', 'The requested order could not be found.', 'not_found');
        }

        return $this->sendApiResponse($order);
    }

    public function ResendOTP(OrderRequest $request): JsonResponse
    {
        $order = Order::query()
            ->where('shop_id', $request->header('shop-id'))
            ->where('phone', $request->input('customer_phone'))
            ->where('order_status', 'unverified')->first();

        if ($order) {
            $shop = Shop::query()->where('shop_id', $order->shop_id)->first();

            if ($shop) {
                if ($shop->sms_balance > 0.30) {

                    $shop->sms_balance -= 0.30;
                    ++$shop->sms_sent;
                    $shop->update();

                    $resend_otp = new Sms();
                    $resend_otp->identifyOtp($order, $shop);

                    return response()->json([
                        'message' => 'Resend OTP Sent Successfully',
                        'data'    => ['otp_sent' => $order->otp_sent],
                    ], 200);
                }

                return $this->sendApiResponse('SMS balance is insufficient, please contact our support');
            }

            return $this->sendApiResponse('Invalid shop-id');
        }

        return $this->sendApiResponse('Order not found !');
    }

    private function createOrderDetails($order, $key, $item, $request, $discountedPrice, $variantId)
    {
        $order->order_details()->create([
            'product_id'  => $item,
            'product_qty' => $request->input('product_qty')[$key],
            'unit_price'  => $discountedPrice,
            'variant'     => $variantId,
        ]);
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