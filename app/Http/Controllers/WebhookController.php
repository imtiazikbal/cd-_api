<?php

namespace App\Http\Controllers;

use App\Models\Fraud;
use App\Models\FraudNote;
use App\Models\MerchantCourier;
use App\Models\Order;
use App\Models\OrderCourier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebhookController extends MerchantBaseController
{
    public function pathaoWebhookHandler(Request $request, $secret): JsonResponse
    {
        $webhookSecret = $request->header('X-PATHAO-Signature');

        if ($webhookSecret) {
            $order_courier = OrderCourier::query()
                ->where('tracking_code', $request->consignment_id)
                ->where('provider', 'pathao')
                ->first();

            if ($order_courier) {
                $order_courier->status = $request->order_status_slug;
                $order_courier->save();
            }

            if ($request->order_status_slug === 'Delivered') {
                if($order_courier->order_id) {
                    $order = Order::query()->find($order_courier->order_id);
                    $order->order_status = Order::DELIVERED;
                    $order->save();
                }
            }

            if ($request->order_status_slug === 'Return') {
                if($order_courier->order_id) {
                    $order = Order::query()->find($order_courier->order_id);
                    $order->order_status = Order::RETURNED;
                    $order->save();
                }
            }

            if ($request->order_status_slug === 'Pickup_Cancelled') {
                if($order_courier->order_id) {
                    $order = Order::query()->find($order_courier->order_id);
                    $order->order_status = Order::CANCELLED;
                    $order->save();
                }
            }

            return response()->json(['message' => 'Webhook received successfully from pathao']);
        }


        return response()->json(['message' => 'You dont have access to this']);
    }

    public function redxWebhookHandler(Request $request): JsonResponse
    {
        Log::info('redx-webhook -' . $request);
        $requestedToken = $request->token;
        $tokenVerify = MerchantCourier::where(['provider' => 'redx', 'status' => 'active'])->value('config');
        $oldToken = optional(json_decode($tokenVerify))->token;
       
        if($oldToken == $requestedToken){

            $order_courier = OrderCourier::query()
            ->where(['tracking_code' => $request['tracking_number'], 'provider' => 'redx'])
            ->first();

            if($order_courier){
                $order = Order::find($order_courier->order_id);

                if($request['status'] === Order::DELIVERED){ 
                    $order->order_status = Order::DELIVERED;
                }

                if($request['status'] === Order::RETURNED){ 
                    $order->order_status = Order::RETURNED;
                }

                $order->save();
            }
            
            return response()->json(['message' => 'Webhook received successfully from redx']);
        }
        
        return response()->json(['message' => 'You don\'t have access to this']);
    }

    public function fraudCheckWebhookHandler(Request $request)
    {
        $data = $request->getContent();
        $response = json_decode($data, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
    
            if (isset($response['data']['couriers'])) {
                $couriers = $response['data']['couriers'];
              
                return DB::transaction(function() use ($couriers, $response) {
                    foreach ($couriers as $fraudData) {
                        if (is_array($fraudData)) {
                            Fraud::updateOrCreate([
                                'number'        => $fraudData['number'],
                                'courier'       => $fraudData['courier'],
                            ],[
                                'orders'        => $fraudData['orders'],
                                'delivered'     => $fraudData['delivered'],
                                'cancelled'     => $fraudData['cancelled'],
                                'cancel_percent'  => $this->getPercentage($fraudData['orders'] ?? 0, $fraudData['cancelled'] ?? 0),
                                'success_percent' => $this->getPercentage($fraudData['orders'] ?? 0, $fraudData['delivered'] ?? 0), 
                            ]);
                        }

                        // Caching fraud data
                       $this->cacheFraudData($fraudData);
                    }

                    // Caching fraud report data
                    $this->cacheFraudReport($couriers[0]['number'], $response);

                    // Fraud note create, it's called fraud report
                    if($response['data']['details']){
                        foreach($response['data']['details'] as $details){
                            $fraud = Fraud::query()
                            ->where('courier', $details['courier'])
                            ->where('number', $details['phone'])
                            ->first();

                            FraudNote::query()->create([
                                'fraud_id'      => $fraud->id,
                                'courier_uid'   => $details['id'],
                                'phone'         => $details['phone'],
                                'name'          => $details['name'],
                                'note'          => $details['details'],
                                'mark_at'       => $details['created_at'],
                            ]);

                            // Caching fraud data
                            $cache_f_couriers_key = "f_couriers_by_{$details['phone']}";
                            if (Cache::has($cache_f_couriers_key)) {
                                $getCacheData = Cache::get($cache_f_couriers_key);

                                $fraud_report = 0;
                                foreach ($getCacheData as &$cachedFraudData) {
                                    if ($cachedFraudData['number'] == $details['phone'] && $cachedFraudData['courier'] == $details['courier']) {
                                        
                                        $cachedFraudData['fraud_report'] = $fraud_report++;
                                    }
                                }

                                Cache::put($cache_f_couriers_key, $getCacheData);
                            }
                        }
                    }
                });
            } else {
                Log::error('Missing couriers data in webhook response', ['response' => $response]);
            }
        } else {
            Log::error('Failed to decode JSON from webhook response', ['raw_data' => $data]);
        }
    }
    public function cacheFraudData($fraudData){

        $cache_f_couriers_key = "f_couriers_by_{$fraudData['number']}";
        if (Cache::has($cache_f_couriers_key)) {
            $getCacheData = Cache::get($cache_f_couriers_key);

            // Check if the specific courier exists in the cached data
            $updated = false;
            foreach ($getCacheData as &$cachedFraudData) {
                if ($cachedFraudData['number'] == $fraudData['number'] && $cachedFraudData['courier'] == $fraudData['courier']) {
                    $cachedFraudData['orders'] = max($cachedFraudData['orders'], $fraudData['orders']);
                    $cachedFraudData['delivered'] = max($cachedFraudData['delivered'], $fraudData['delivered']);
                    $cachedFraudData['cancelled'] = max($cachedFraudData['cancelled'], $fraudData['cancelled']);
                    
                    $updated = true;
                    break;
                }
            }

            if (!$updated) {
                $getCacheData[] = $fraudData;
            }

            Cache::put($cache_f_couriers_key, $getCacheData);
        }
    }

    public function cacheFraudReport($number, $response){
       
        $cache_key = "fraud_by_{$number}";
        $cacheFraudReport = Cache::has($cache_key);

        if(!$cacheFraudReport || $cacheFraudReport){

            $frauds_report = (object)[
                'order_placed'      => $response['data']['fraud_entry'],
                'order_delivered'   => $response['data']['fraud_delivery'],
                'order_returned'    => $response['data']['fraud_return'],
                'fraud_report'      => $response['data']['fraud_report'],
                'fraud_processing'  => false,
            ];
            
            Cache::put($cache_key, json_encode($frauds_report));
        }

    }
    
    private function getPercentage(int $orders, int $value): int
    {
        if($orders > 0){
            return $value / $orders * 100;
        }else {
            return 0;
        }
    }
}