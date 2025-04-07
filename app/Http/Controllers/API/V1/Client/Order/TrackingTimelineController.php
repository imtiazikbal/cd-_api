<?php

namespace App\Http\Controllers\API\V1\Client\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderTrackerResource;
use App\Http\Resources\TrackingTimelineResource;
use App\Models\Order;
use App\Models\OrderCourier;
use App\Models\OrderTrackingTimeline;
use App\Services\TrackingTimelineService;
use Illuminate\Support\Facades\Cache;

class TrackingTimelineController extends Controller
{
    public function tracking_timeline(int $order_id)
    {
        $our_timeline = OrderTrackingTimeline::query()
            ->where('order_id', $order_id)->get();

        if ($our_timeline->isEmpty()) {
            return $this->sendApiResponse('', 'Order tracking timeline not found', 'notFound');
        }

        $order_courier = OrderCourier::where('order_id', $order_id)->first();
        $order_timeline = $tracking_code = $provider = null;

        if ($order_courier) {
            $tracking_code = $order_courier->tracking_code;
            $provider = $order_courier->provider;
            $tl_cache_key = "tc_tl_{$provider}_{$tracking_code}"; // Tracking Timeline Cache Key
            $ri_cache_key = "tc_ri_{$provider}_{$tracking_code}"; // Rider Info Cache Key

            if ((!$order_timeline = Cache::get($tl_cache_key)) && $provider) {
                $tracker = new TrackingTimelineService($provider);
                $result = $tracker->timeline($tracking_code);

                if ($result->status == 'success') {
                    $order_timeline = $result->timeline;
                    $rider_info = $result->rider_info;
                    Cache::put($tl_cache_key, $order_timeline, 120);
                    Cache::put($ri_cache_key, $rider_info, 120);
                }
            }
        }

        $our_timeline = $our_timeline->toArray();

        if ($order_timeline) {
            $our_timeline = \array_merge($our_timeline, $order_timeline);
        }

        $resources = TrackingTimelineResource::collection(collect($our_timeline));
        return $this->sendApiResponse($resources, 'Order tracking timeline generated');
    }

    public function order_tracker(string $order_tracking_code)
    {
        $order = Order::query()->with('order_details', 'pricing', 'shop', 'courier')
            ->where('tracking_code', $order_tracking_code)->first();

        if ($order == null) {
            return $this->sendApiResponse('', 'Order not found', 'notFound');
        }

        $our_timeline = OrderTrackingTimeline::query()
            ->where('order_id', $order->id)->get();

        if ($our_timeline->isEmpty()) {
            return $this->sendApiResponse('', 'Order tracking timeline not found', 'notFound');
        }

        $order_timeline = $rider_info = $tracking_code = $provider = null;

        if ($order->courier->tracking_code ?? null) {
            $tracking_code = $order->courier->tracking_code;
            $provider = $order->courier->provider;
            $tl_cache_key = "tc_tl_{$provider}_{$tracking_code}"; // Tracking Timeline Cache Key
            $ri_cache_key = "tc_ri_{$provider}_{$tracking_code}"; // Rider Info Cache Key

            $order_timeline = Cache::get($tl_cache_key);
            $rider_info = Cache::get($ri_cache_key);

            if ((!$order_timeline || !$rider_info) && $provider) {
                $tracker = new TrackingTimelineService($provider);
                $result = $tracker->timeline($tracking_code);

                if ($result->status == 'success') {
                    $order_timeline = $result->timeline;
                    $rider_info = $result->rider_info;
                    Cache::put($tl_cache_key, $order_timeline, 120);
                    Cache::put($ri_cache_key, $rider_info, 120);
                }
            }
        }

        $our_timeline = $our_timeline->toArray();

        if ($order_timeline) {
            $our_timeline = \array_merge($our_timeline, $order_timeline);
        }

        $order->order_timeline = collect($our_timeline);
        $order->rider_info = (object)$rider_info;

        $resources = new OrderTrackerResource($order);
        return $this->sendApiResponse($resources, 'Order tracking timeline generated');
    }
}
