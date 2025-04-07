<?php

namespace App\Http\Controllers\API\V1\Client\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\orderSourceStatisticsRequest;
use App\Models\Order;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RatioStatisticsController extends Controller
{
    public $orderService;

    /**
     * OrderController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function orderSourceStatistic(orderSourceStatisticsRequest $request): JsonResponse
    {
        // $channelArr = Cache::remember("order-source-{$request->header('shop-id')}", 84600, function() use ($request){
            if ($request->channel_status == 'today') {
                $today = Carbon::today()->toDateString();
                $orders = Order::query()
                    ->where('shop_id', $request->header('shop-id'))
                    ->whereDate('created_at', $today)
                    ->withTrashed()
                    ->get();
            } elseif ($request->channel_status == 'yesterday') {
                $yesterday = Carbon::yesterday()->toDateString();

                $orders = Order::query()
                    ->where('shop_id', $request->header('shop-id'))
                    ->whereDate('created_at', $yesterday)
                    ->withTrashed()
                    ->get();
            } elseif ($request->channel_status == 'weekly') {
                $weekStartDate = Carbon::now()->subDays(7)->startOfDay();
                $weekEndDate = Carbon::now()->endOfDay();

                $orders = Order::query()
                    ->where('shop_id', $request->header('shop-id'))
                    ->whereBetween('created_at', [$weekStartDate, $weekEndDate])
                    ->withTrashed()
                    ->get();
            } elseif ($request->channel_status == 'monthly') {
                $orders = Order::query()
                    ->where('shop_id', $request->header('shop-id'))
                    ->whereMonth('created_at', Carbon::today()->month)
                    ->withTrashed()
                    ->get();
            } elseif ($request->channel_status == 'custom') {
                $startDate = Carbon::parse($request->start_date)->toDateString();
                $endDate = Carbon::parse($request->end_date)->toDateString();

                if($startDate === $endDate) {
                    $orders = Order::query()
                        ->where('shop_id', $request->header('shop-id'))
                        ->whereDate('created_at', $startDate)
                        ->withTrashed()
                        ->get();
                }

                if($startDate !== $endDate) {
                    $orders = Order::query()
                        ->where('shop_id', $request->header('shop-id'))
                        ->where(function ($query) use ($startDate, $endDate) {
                            $query->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate);
                        })
                        ->withTrashed()
                        ->get();
                }
            } else {
                return $this->sendApiResponse('', 'Channel statistics not found !');
            }

            $groupedOrders = $orders->groupBy('order_type');
            $channelArr = [];

            foreach ($groupedOrders as $item) {
                $channelArr[] = [
                    'name'  => ucwords($item[0]->order_type),
                    'value' => $item->count()
                ];
            }

            //return $channelArr;

        // });

        return $this->sendApiResponse($channelArr, 'Channel statistics');
    }

    public function ratioCalculation(Request $request): JsonResponse
    {
        $data['total_order_ratio'] = $this->orderService->totalOrderRatioCaculation($request);
        $data['confirmed_order_ratio'] = $this->orderService->confirmedOrderRatioCaculation($request);
        $data['cancel_order_ratio'] = $this->orderService->cancelOrderRatioCaculation($request);
        $data['sales_amount_ratio'] = $this->orderService->salesAmountRatioCaculation($request);
        $data['discount_amount_ratio'] = $this->orderService->discountAmountRatioCaculation($request);
        $data['advance_amount_ratio'] = $this->orderService->advanceAmountRatioCaculation($request);

        return $this->sendApiResponse($data, 'Ratio calculation');
    }

    public function orderSourceRatioCalculation(Request $request): JsonResponse
    {
        // $data = Cache::remember("order-source-ratio-{$request->header('shop-id')}", 84600, function() use ($request){
            $data['landing'] = $this->orderService->channelLandingRatioCaculation($request);
            $data['website'] = $this->orderService->channelWebsiteRatioCaculation($request);
            $data['phone'] = $this->orderService->channelPhoneRatioCaculation($request);
            $data['social'] = $this->orderService->channelSocialRatioCaculation($request);

            //return $data;
        // });
        
        return $this->sendApiResponse($data, 'Channel ratio calculation');
    }

    public function chartStatistic(Request $request): JsonResponse
    {
        $this->validate($request, [
            'chart-status' => 'required'
        ]);

        if ($request->input('chart-status') == 'custom' || $request->input('chart-status') == 'year') {
            $result = $this->orderService->getCustomOrYearChatStatistics($request);
        } else {
            $result = $this->orderService->getChartStatistics($request);
        }

        return $this->sendApiResponse($result, 'Chart statistics result');
    }
}