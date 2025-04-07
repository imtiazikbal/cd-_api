<?php

/** @noinspection UnknownColumnInspection */

namespace App\Services;

use App\Models\Order;
use App\Models\OrderCourier;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function index(object $data): LengthAwarePaginator
    {
        $perPage = 10;

        if (request()->query('perPage')) {
            $perPage = request()->query('perPage');
        }

        $query = Order::with('order_details', 'pricing', 'config', 'courier', 'order_dates', 'order_attach_images')
            ->where('shop_id', $data->header('shop-id'))
            ->where('order_status', $data->type);

        // only all order show
        if ($data->type == Order::ALL) {
            $allOrderShopWise = $this->getAllOrderShopWise($data->header('shop-id'));
            return $allOrderShopWise->paginate($perPage);
        }

        if ($data->filled('search')) {
            $query->where(function ($q) use ($data) {
                return $q->where('order_no', 'like', "%$data->search%")
                    ->orWhere('customer_name', 'like', "%$data->search%")
                    ->orWhere('phone', 'like', "%$data->search%");
            });
        }

        if ($data->type === Order::SHIPPED && $data->provider !== null) {
            $query->whereHas('courier', function ($q) use ($data) {
                if ($data->courier_status !== null) {
                    return $q->where('provider', $data->provider)->where('status', $data->courier_status);
                }

                return $q->where('provider', $data->provider);
            });
        }

        if ($data->type === Order::FOLLOWUP && $data->filter_date !== null) {
            $query->whereHas('order_dates', function ($q) use ($data) {
                if ($data->filter_date === 'today') {
                    return $this->orderSearchDateWise($q, Order::FOLLOWUP, Carbon::today()->toDateString());
                }

                if ($data->filter_date === 'tomorrow') {
                    return $this->orderSearchDateWise($q, Order::FOLLOWUP, Carbon::tomorrow()->toDateString());
                }

                if ($data->filter_date === 'next_seven_days') {
                    return $q->where('type', Order::FOLLOWUP)
                        ->whereBetween(
                            'created_at',
                            [Carbon::today()->toDateString(), Carbon::today()->addDays(7)->toDateString()]
                        );
                }

                if ($data->filter_date === 'custom') {
                    if ($data->start_date === $data->end_date) {
                        return $q->where('type', Order::FOLLOWUP)
                            ->whereDate('created_at', Carbon::parse($data->start_date)->addDay()->toDateString());
                    }

                    return $q->where('type', Order::FOLLOWUP)
                        ->whereBetween('created_at', [
                            Carbon::parse($data->start_date)->toDateString(),
                            Carbon::parse($data->end_date)->toDateString()
                        ]);
                }

                return $q->where('type', Order::FOLLOWUP);
            });
        }

        if ($data->type === Order::CONFIRMED && $data->filter_date !== null) {

            $query->whereHas('order_dates', function ($q) use ($data) {
                if ($data->filter_date === 'today') {
                    return $this->orderSearchDateWise($q, Order::CONFIRMED, Carbon::today()->toDateString());
                }

                if ($data->filter_date === 'tomorrow') {
                    return $this->orderSearchDateWise($q, Order::CONFIRMED, Carbon::tomorrow()->toDateString());
                }

                if ($data->filter_date === 'next_seven_days') {
                    return $q->where('type', Order::CONFIRMED)
                        ->whereBetween(
                            'created_at',
                            [Carbon::today()->toDateString(), Carbon::today()->addDays(7)->toDateString()]
                        );
                }

                if ($data->filter_date === 'custom') {
                    if ($data->start_date === $data->end_date) {
                        return $q->where('type', Order::CONFIRMED)
                            ->whereDate('created_at', Carbon::parse($data->start_date)->addDay()->toDateString());
                    }

                    return $q->where('type', Order::CONFIRMED)
                        ->whereBetween('created_at', [
                            Carbon::parse($data->start_date)->toDateString(),
                            Carbon::parse($data->end_date)->toDateString()
                        ]);
                }

                if ($data->filter_date === 'previous') {
                    $today = date('Y-m-d');

                    return $q->where('type', Order::CONFIRMED)
                        ->where('created_at', '<', $today);
                }

                return $q->where('type', Order::CONFIRMED);
            });
        }

        // order status wise custom filter
        if ($data->filter_date === 'custom' && $data->type !== null) {

            if ($data->type === Order::TRASH) {
                $filterQuery = Order::with('order_details', 'pricing')
                    ->where('shop_id', $data->header('shop-id'))
                    ->onlyTrashed();
            } elseif ($data->type === Order::ALLFILTER) {
                $filterQuery = $this->getAllOrderShopWise($data->header('shop-id'));
            } else {
                $filterQuery = Order::with('order_details', 'pricing')
                    ->where('shop_id', $data->header('shop-id'))
                    ->where('order_status', $data->type);
            }

            if ($data->start_date === $data->end_date) {
                $filterQuery->whereDate('created_at', Carbon::parse($data->start_date)->toDateString());
                return $filterQuery->orderByDesc('updated_at')->paginate($perPage);
            }

            $result = $filterQuery->whereDate('created_at', '>=', Carbon::parse($data->start_date)->toDateString())
                ->whereDate('created_at', '<=', Carbon::parse($data->end_date)->toDateString())
                ->orderByDesc('updated_at')->paginate($perPage);

            return $result;
        }

        return $query->orderByDesc('updated_at')->paginate($perPage);
    }

    public function orderSearchDateWise($query, $type, $date)
    {
        return $query->where('type', $type)
            ->whereDate('date', $date);
    }

    public function orderCounts(string $shopId): array
    {
        $statusTypes = [
            Order::PENDING,
            Order::CONFIRMED,
            Order::RETURNED,
            Order::CANCELLED,
            Order::SHIPPED,
            Order::DELIVERED,
            Order::HOLDON,
            Order::FOLLOWUP,
            Order::UNVERIFIED,
            Order::TRASH,
        ];
        
        $statusCounts = [];
        foreach ($statusTypes as $statusType) {
            $statusCounts[$statusType] = $this->getOrderStatusWise($shopId, $statusType);
        }
        
        // $statusCounts['allOrderCount'] = Cache::remember("all-order-count-{$shopId}", 60 * 60 * 24, function() use ($shopId){
            $statusCounts['allOrderCount'] = $this->getAllOrderShopWise($shopId)->get()->count();
        // }); 
        
        // $providerStatusCounts = Cache::remember("order-courier-{$shopId}", 60 * 60 * 24, function() use ($shopId){
            $providerStatusCounts = OrderCourier::query()
            ->whereNotNull('provider')
            ->whereHas('order', function ($query) use ($shopId) {
                $query->where('shop_id', $shopId)
                    ->where('order_status', Order::SHIPPED);
            })
            ->select('provider', 'status', DB::raw('COUNT(*) as count'))
            ->groupBy('provider', 'status')
            ->get()
            ->toArray();
        // });

        $providers = [
            'pathao'    => 0,
            'steadfast' => 0
        ];

        $formattedResult = [];

        foreach ($providerStatusCounts as $row) {
            $provider = $row['provider'];
            $status = $row['status'];
            $count = $row['count'];

            if (!isset($providers[$provider])) {
                $providers[$provider] = 0;
            }

            if ($provider) {
                $providers[$provider] += $count;
            }
            $formattedResult[$provider . '_status'][$status] = $count;
        }

        return array_merge($statusCounts, $providers, $formattedResult);
    }

    public function getAllOrderShopWise($shopId)
    {
        return Order::with('order_details', 'pricing', 'config', 'courier', 'order_dates')
            ->where('shop_id', $shopId)
            ->orderByDesc('updated_at')
            ->withTrashed();

    }

    public function getOrderStatusWise(string $shopId, string $orderStatus): int
    {
        if ($orderStatus == Order::TRASH) {
            // return Cache::remember("trash-order-count-{$shopId}", 60 * 60 * 24, function() use ($shopId){
                return Order::onlyTrashed()
                ->where('shop_id', $shopId)
                ->withTrashed()
                ->count();
            // });
        } else {
            // return Cache::remember("{$orderStatus}-order-count-{$shopId}", 60 * 60 * 24, function() use ($shopId, $orderStatus){
                return Order::query()
                ->where('shop_id', $shopId)
                ->where('order_status', $orderStatus)
                ->count();
            // });
        }
    }

    public function getTotalStatistic($data): int
    {
        $type = $data->date;

        return Order::query()
            ->where('shop_id', $data->header('shop-id'))
            ->where(function ($q) use ($type, $data) {
                if ($type === 'today') {
                    return $q->whereDate('created_at', Carbon::today()->toDateString());
                }

                if ($type === 'yesterday') {
                    return $q->whereDate('created_at', Carbon::yesterday()->toDateString());
                }

                if ($type === 'weekly') {
                    return $q->whereBetween(
                        'created_at',
                        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                    );
                }

                if ($type === 'monthly') {
                    return $q->whereMonth('created_at', Carbon::today()->month);
                }

                if ($type === 'custom') {
                    $startDate = Carbon::parse($data->start_date)->toDateString();
                    $endDate = Carbon::parse($data->end_date)->toDateString();

                    if ($startDate === $endDate) {
                        return $q->whereDate('created_at', $startDate);
                    }

                    if ($startDate !== $endDate) {
                        return $q->where(function ($query) use ($startDate, $endDate) {
                            $query->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate);
                        });
                    }
                }

                return $q;
            })
            ->count();
    }

    public function getConfirmedStatistic($data): int
    {
        $type = $data->confirmed_date;

        return Order::query()
            ->where('shop_id', $data->header('shop-id'))
            ->whereHas('status', function ($q) use ($type, $data) {
                if ($type === 'today') {
                    return $q->where('type', Order::CONFIRMED)->whereDate(
                        'created_at',
                        Carbon::today()->toDateString()
                    );
                }

                if ($type === 'yesterday') {
                    return $q->where('type', Order::CONFIRMED)->whereDate(
                        'created_at',
                        Carbon::yesterday()->toDateString()
                    );
                }

                if ($type === 'weekly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->whereBetween(
                            'created_at',
                            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                        );
                }

                if ($type === 'monthly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->whereMonth('created_at', Carbon::today()->month);
                }

                if ($type === 'custom') {
                    $startDate = Carbon::parse($data->start_date)->toDateString();
                    $endDate = Carbon::parse($data->end_date)->toDateString();

                    if ($startDate === $endDate) {
                        return $q->where('type', Order::CONFIRMED)
                            ->whereDate('created_at', $startDate);
                    }

                    if ($startDate !== $endDate) {
                        return $q->where('type', Order::CONFIRMED)
                            ->where(function ($query) use ($startDate, $endDate) {
                                $query->whereDate('created_at', '>=', $startDate)
                                    ->whereDate('created_at', '<=', $endDate);
                            });
                    }
                }

                return $q->where('type', Order::CONFIRMED);
            })
            ->count();
    }

    public function getPendingStatistic($data): int
    {
        return Order::query()
            ->where('order_status', Order::PENDING)
            ->where('shop_id', $data->header('shop-id'))
            ->count();
    }

    public function getCancelStatistic($data): int
    {
        $type = $data->cancel_date;

        return Order::query()
            ->whereHas('status', function ($q) use ($type, $data) {
                if ($type === 'today') {
                    return $q->where('order_status', Order::CANCELLED)
                        ->whereDate('updated_at', Carbon::today()->toDateString());
                }

                if ($type === 'yesterday') {
                    return $q->where('order_status', Order::CANCELLED)
                        ->whereDate('updated_at', Carbon::yesterday()->toDateString());
                }

                if ($type === 'weekly') {
                    return $q->where('order_status', Order::CANCELLED)
                        ->whereBetween(
                            'updated_at',
                            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                        );
                }

                if ($type === 'monthly') {
                    return $q->where('order_status', Order::CANCELLED)
                        ->whereMonth('updated_at', Carbon::today()->month);
                }

                if ($type === 'custom') {
                    $startDate = Carbon::parse($data->start_date)->toDateString();
                    $endDate = Carbon::parse($data->end_date)->toDateString();

                    if ($startDate === $endDate) {
                        return $q->where('type', Order::CANCELLED)
                            ->whereDate('created_at', $startDate);
                    }

                    if ($startDate !== $endDate) {
                        return $q->where('type', Order::CANCELLED)
                            ->where(function ($query) use ($startDate, $endDate) {
                                $query->whereDate('created_at', '>=', $startDate)
                                    ->whereDate('created_at', '<=', $endDate);
                            });
                    }
                }

                return $q->where('order_status', Order::CANCELLED);
            })
            ->where('shop_id', $data->header('shop-id'))
            ->count();
    }

    public function getPhoneStatistic($data): int
    {
        $type = $data->phone_date;

        return Order::query()
            ->whereHas('status', function ($q) use ($type, $data) {
                if ($type === 'today') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'phone')
                        ->whereDate('created_at', Carbon::today()->toDateString());
                }

                if ($type === 'yesterday') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'phone')
                        ->whereDate('created_at', Carbon::yesterday()->toDateString());
                }

                if ($type === 'weekly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'phone')
                        ->whereBetween(
                            'created_at',
                            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                        );
                }

                if ($type === 'monthly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'phone')
                        ->whereMonth('created_at', Carbon::today()->month);
                }

                if ($type === 'custom') {
                    return $q->where('type', Order::CONFIRMED)
                        ->whereBetween('created_at', [
                            Carbon::parse($data->start_date)->toDateString(),
                            Carbon::parse($data->end_date)->toDateString()
                        ]);
                }

                return $q->where('type', Order::CONFIRMED)->where('order_type', 'phone');
            })
            ->where('shop_id', $data->header('shop-id'))
            ->count();
    }

    public function getSocialStatistic($data): int
    {
        $type = $data->social_date;

        return Order::query()
            ->whereHas('status', function ($q) use ($type, $data) {
                if ($type === 'today') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'social')
                        ->whereDate('created_at', Carbon::today()->toDateString());
                }

                if ($type === 'yesterday') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'social')
                        ->whereDate('created_at', Carbon::yesterday()->toDateString());
                }

                if ($type === 'weekly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'social')
                        ->whereBetween(
                            'created_at',
                            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                        );
                }

                if ($type === 'monthly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'social')
                        ->whereMonth('created_at', Carbon::today()->month);
                }

                if ($type === 'custom') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'social')
                        ->whereBetween('created_at', [
                            Carbon::parse($data->start_date)->toDateString(),
                            Carbon::parse($data->end_date)->toDateString()
                        ]);
                }

                return $q->where('type', Order::CONFIRMED)->where('order_type', 'social');
            })
            ->where('shop_id', $data->header('shop-id'))
            ->count();
    }

    public function getMultipleStatistic($data): int
    {
        $type = $data->multiple_date;

        return Order::query()
            ->whereHas('status', function ($q) use ($type, $data) {
                if ($type === 'today') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'multiple')
                        ->whereDate('created_at', Carbon::today()->toDateString());
                }

                if ($type === 'yesterday') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'multiple')
                        ->whereDate('created_at', Carbon::yesterday()->toDateString());
                }

                if ($type === 'weekly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'multiple')
                        ->whereBetween(
                            'created_at',
                            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                        );
                }

                if ($type === 'monthly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'multiple')
                        ->whereMonth('created_at', Carbon::today()->month);
                }

                if ($type === 'custom') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'multiple')
                        ->whereBetween('created_at', [
                            Carbon::parse($data->start_date)->toDateString(),
                            Carbon::parse($data->end_date)->toDateString()
                        ]);
                }

                return $q->where('type', Order::CONFIRMED)->where('order_type', 'multiple');
            })
            ->where('shop_id', $data->header('shop-id'))
            ->count();
    }

    public function getLandingStatistic($data): int
    {
        $type = $data->landing_date;

        return Order::query()
            ->whereHas('status', function ($q) use ($type, $data) {
                if ($type === 'today') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'landing')
                        ->whereDate('created_at', Carbon::today()->toDateString());
                }

                if ($type === 'yesterday') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'landing')
                        ->whereDate('created_at', Carbon::yesterday()->toDateString());
                }

                if ($type === 'weekly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'landing')
                        ->whereBetween(
                            'created_at',
                            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                        );
                }

                if ($type === 'monthly') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'landing')
                        ->whereMonth('created_at', Carbon::today()->month);
                }

                if ($type === 'custom') {
                    return $q->where('type', Order::CONFIRMED)
                        ->where('order_type', 'landing')
                        ->whereBetween('created_at', [
                            Carbon::parse($data->start_date)->toDateString(),
                            Carbon::parse($data->end_date)->toDateString()
                        ]);
                }

                return $q->where('type', Order::CONFIRMED)->where('order_type', 'landing');
            })
            ->where('shop_id', $data->header('shop-id'))
            ->count();
    }

    /**
     *  Salse amount calculation was mismatched by this function.
     */
    // public function getSalesStatistic($data)
    // {
    //     $type = $data->sales_date;
    //     $shopId = $data->header('shop-id');
    //     $amounts = 0;
    //     Order::with('pricing')
    //         ->where('shop_id', $shopId)
    //         ->where('order_status', Order::CONFIRMED)
    //         ->where(function ($q) use ($type, $data) {
    //             if ($type === 'today') {
    //                 return $q->whereDate(
    //                     'status_update_date',
    //                     Carbon::today()->toDateString()
    //                 );
    //             }

    //             if ($type === 'yesterday') {
    //                 return $q->whereDate(
    //                     'status_update_date',
    //                     Carbon::yesterday()->toDateString()
    //                 );
    //             }

    //             if ($type === 'weekly') {
    //                 $weekStartDate = Carbon::now()->subDays(7)->startOfDay();
    //                 $weekEndDate = Carbon::now()->endOfDay();

    //                 return $q->whereBetween(
    //                     'status_update_date',
    //                     [$weekStartDate, $weekEndDate]
    //                 );
    //             }

    //             if ($type === 'monthly') {
    //                 return $q->whereMonth(
    //                     'status_update_date',
    //                     Carbon::today()->month
    //                 );
    //             }

    //             if ($type === 'custom') {
    //                 $startDate = Carbon::parse($data->start_date)->toDateString();
    //                 $endDate = Carbon::parse($data->end_date)->toDateString();

    //                 if ($startDate === $endDate) {
    //                     return $q->whereDate('status_update_date', $startDate);
    //                 }

    //                 if ($startDate !== $endDate) {
    //                     return $q->where(function ($query) use ($startDate, $endDate) {
    //                         $query->whereDate('status_update_date', '>=', $startDate)
    //                             ->whereDate('status_update_date', '<=', $endDate);
    //                     });
    //                 }

    //                 return $q;
    //             }
    //         })
    //         ->each(function ($a) use (&$amounts) {
    //             $total = discountedTotal($a->pricing);
    //             $amounts += $total;
    //         });

    //     return $amounts;
    // }

    public function getSalesStatistic($data): Int
    {
        $type = $data->sales_date;
        $shopId = $data->header('shop-id');
        $amounts = 0;
        Order::with('pricing')
            ->where('shop_id', $shopId)
            ->whereHas('status', function ($q) use ($type, $data) {
                if ($type === 'today') {
                    return $q->where('type', Order::CONFIRMED)->whereDate(
                        'created_at',
                        Carbon::today()->toDateString()
                    );
                }

                if ($type === 'yesterday') {
                    return $q->where('type', Order::CONFIRMED)->whereDate(
                        'created_at',
                        Carbon::yesterday()->toDateString()
                    );
                }

                if ($type === 'weekly') {
                    $weekStartDate = Carbon::now()->subDays(7)->startOfDay();
                    $weekEndDate = Carbon::now()->endOfDay();

                    return $q->where('type', Order::CONFIRMED)->whereBetween(
                        'created_at',
                        [$weekStartDate, $weekEndDate]
                    );
                }

                if ($type === 'monthly') {
                    return $q->where('type', Order::CONFIRMED)->whereMonth(
                        'created_at',
                        Carbon::today()->month
                    );
                }

                if ($type === 'custom') {
                    $startDate = Carbon::parse($data->start_date)->toDateString();
                    $endDate = Carbon::parse($data->end_date)->toDateString();

                    if ($startDate === $endDate) {
                        return $q->where('type', Order::CONFIRMED)->whereDate('created_at', $startDate);
                    }

                    if ($startDate !== $endDate) {
                        return $q->where(function ($query) use ($startDate, $endDate) {
                            $query->where('type', Order::CONFIRMED)->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate);
                        });
                    }

                    return $q;
                }
            })
            ->each(function ($a) use (&$amounts) {
                $total = discountedTotal($a->pricing);
                $amounts += $total;
            });

        return $amounts;
    }
    
    public function getDeliveryReport($request): array
    {
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();

        $date = $request->date;
        $delivered = $this->getOrderByType($request, $date, $startDate, $endDate, Order::DELIVERED);
        $returned = $this->getOrderByType($request, $date, $startDate, $endDate, Order::RETURNED);

        $total_order = $this->getTotalOrder($request, $date, $startDate, $endDate);
        $data['delivered'] = $delivered;
        $data['returned'] = $returned;
        $data['returned_ratio'] = targetCalculate($returned, $total_order);

        return $data;
    }

    public function getOrderByType($request, $date, $startDate, $endDate, $type): int
    {
        return Order::query()
            ->where(function ($q) use ($request, $date, $startDate, $endDate, $type) {
                if ($date === 'today') {
                    return $q->where('order_status', $type)->whereDate('created_at', Carbon::today()->toDateString());
                }

                if ($date === 'yesterday') {
                    return $q->where('order_status', $type)->whereDate('created_at', Carbon::yesterday()->toDateString());
                }

                if ($date === 'weekly') {
                    return $q->where('order_status', $type)->whereBetween(
                        'created_at',
                        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                    );
                }

                if ($date === 'monthly') {
                    return $q->where('order_status', $type)->whereMonth('created_at', Carbon::today()->month);
                }

                if ($date === 'yearly') {
                    return $q->where('order_status', $type)->whereYear('created_at', Carbon::today()->year);
                }

                if ($date === 'custom') {
                    if ($startDate === $endDate) {
                        return $q->where('order_status', $type)
                            ->whereDate('created_at', $startDate);
                    }

                    if ($startDate !== $endDate) {
                        return $q->where('order_status', $type)
                            ->where(function ($query) use ($startDate, $endDate) {
                                $query->whereDate('created_at', '>=', $startDate)
                                    ->whereDate('created_at', '<=', $endDate);
                            });
                    }
                }

                return $q->where('order_status', $type);
            })
            ->where('shop_id', $request->header('shop-id'))
            ->count();
    }

    public function getTotalOrder($data, $type, $startDate, $endDate): int
    {
        $query = Order::query()->where('shop_id', $data->header('shop-id'));

        if ($type === 'all') {
            return $query->count();
        }

        return $query->where(function ($q) use ($type, $startDate, $endDate) {
            if ($type === 'today') {
                return $q->whereDate('created_at', Carbon::today()->toDateString());
            }

            if ($type === 'yesterday') {
                return $q->whereDate('created_at', Carbon::yesterday()->toDateString());
            }

            if ($type === 'weekly') {
                return $q->whereBetween(
                    'created_at',
                    [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                );
            }

            if ($type === 'monthly') {
                return $q->whereMonth('created_at', Carbon::today()->month);
            }

            if ($type === 'yearly') {
                return $q->whereYear('created_at', Carbon::today()->year);
            }

            if ($type === 'custom') {
                return $q->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            }

            return $q->whereDate('created_at', Carbon::today()->toDateString());
        })->count();
    }

    public function updatePricing($order)
    {
        $grand_total = 0;
        $discount = 0;

        foreach ($order->order_details as $item) {
            $grand_total += $item->product_qty * $item->unit_price;
        }
        $due = ($grand_total + $order->pricing->shipping_cost) - $order->pricing->advanced;

        if ($order->pricing->discount > 0) {
            if ($order->pricing->discount_type === Product::PERCENT) {
                $discount = ceil($grand_total * ($order->pricing->discount / 100));
            } else {
                $discount = ceil($order->pricing->discount);
            }
        }
        $order->pricing->update([
            'grand_total' => $grand_total,
            'due'         => $due - $discount,
        ]);

        return $order;
    }

    // chart order weekly calculations
    public function getChartStatistics($request)
    {

        $filters = Order::with('order_details', 'pricing', 'status')
            ->whereHas('status', function ($q) use ($request) {
                if ($request->input('chart-status') === 'today') {
                    return $q->where('type', Order::CONFIRMED)->whereDate(
                        'created_at',
                        Carbon::today()->toDateString()
                    );
                } else {
                    if ($request->input('chart-status') === 'yesterday') {
                        return $q->where('type', Order::CONFIRMED)->whereDate(
                            'created_at',
                            Carbon::yesterday()->toDateString()
                        );
                    } else {
                        if ($request->input('chart-status') === 'week') {
                            $weekStartDate = Carbon::now()->subDays(7)->startOfDay();
                            $weekEndDate = Carbon::now()->endOfDay();

                            return $q->where('type', Order::CONFIRMED)->whereBetween(
                                'created_at',
                                [$weekStartDate, $weekEndDate]
                            );
                        } else {
                            if ($request->input('chart-status') === 'month') {
                                return $q->where('type', Order::CONFIRMED)->whereMonth(
                                    'created_at',
                                    Carbon::today()->month
                                );
                            } else {
                                if ($request->input('chart-status') === 'custom') {
                                    return $q->where('type', Order::CONFIRMED)->whereBetween('created_at', [
                                        Carbon::parse($request->start_date)->toDateString(),
                                        Carbon::parse($request->end_date)->toDateString()
                                    ]);
                                }
                            }
                        }
                    }
                }
            })
            ->where('shop_id', $request->header('shop-id'))
            ->get();

        // weekly filters & month filters & year filters
        $filterArr = [];

        foreach ($filters as $filter) {

            foreach ($filter->status as $status) {
                $date = substr($status['created_at'], 0, 10); // the date portion (YYYY-MM-DD)
            }

            if (!isset($filterArr[$date])) {
                $filterArr[$date] = [
                    'name'        => substr($date, 8, 2) . 'th', // the day portion and append 'th'
                    'product_qty' => 0,
                    'Amount'      => 0,
                    'Amt'         => 0,
                ];
            }

            foreach ($filter['order_details'] as $details) {
                $filterArr[$date]['product_qty'] += $details['product_qty'];
            }

            $filterArr[$date]['Amount'] += discountedTotal($filter->pricing);
            $filterArr[$date]['Amt'] += discountedTotal($filter->pricing);
        }

        // Convert the associative array to a sequential array
        ksort($filterArr);
        $filterArr = array_values($filterArr);

        return $filterArr;
    }

    // chart custom date wise order calculations
    public function getCustomOrYearChatStatistics($request)
    {
        // custom date format
        if ($request->input('chart-status') === 'custom') {
            // total days calculation
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $daysDiff = $start->diffInDays($end);

            //
            if ($daysDiff <= 30) {
                return $this->getChartStatistics($request);
            } else {
                if ($daysDiff > 30) {
                    $filters = Order::with('order_details', 'pricing', 'status')
                        ->whereHas('status', function ($q) use ($request) {
                            $q->where('type', Order::CONFIRMED)->whereBetween('created_at', [
                                Carbon::parse($request->start_date)->toDateString(),
                                Carbon::parse($request->end_date)->toDateString()
                            ]);
                        })
                        ->where('shop_id', $request->header('shop-id'))
                        ->get();

                    // month name
                    $months = [
                        "01" => 'January',
                        "02" => 'February',
                        "03" => 'March',
                        "04" => 'April',
                        "05" => 'May',
                        "06" => 'June',
                        "07" => 'July',
                        "08" => 'August',
                        "09" => 'September',
                        "10" => 'October',
                        "11" => 'November',
                        "12" => 'December',
                    ];

                    // custom filters
                    $filterArr = [];

                    foreach ($filters as $filter) {
                        foreach ($filter->status as $status) {
                            $date = substr($status['created_at'], 0, 7); // the date portion (YYYY-MM-DD)
                        }

                        if (!isset($filterArr[$date])) {

                            foreach ($months as $index => $mth) {
                                $info = substr($date, 5, 2);

                                if ($info == $index) {
                                    $filterArr[$date] = [
                                        'name'        => $mth,
                                        'product_qty' => 0,
                                        'Amount'      => 0,
                                        'Amt'         => 0,
                                    ];
                                }
                            }
                        }

                        foreach ($filter['order_details'] as $details) {
                            $filterArr[$date]['product_qty'] += $details['product_qty'];
                        }
                        $filterArr[$date]['Amount'] += discountedTotal($filter->pricing);
                        $filterArr[$date]['Amt'] += discountedTotal($filter->pricing);
                    }

                    // Convert the associative array to a sequential array
                    ksort($filterArr);
                    $filterArr = array_values($filterArr);

                    return $filterArr;
                }
            }
        } else {
            if ($request->input('chart-status') == 'year') {

                $filters = Order::with('order_details', 'pricing')
                    ->whereHas('status', function ($q) use ($request) {
                        $q->where('type', Order::CONFIRMED)->whereYear('created_at', Carbon::today()->year);
                    })
                    ->where('shop_id', $request->header('shop-id'))
                    ->whereYear('created_at', Carbon::today()->year)
                    ->get();

                // month name
                $months = [
                    "01" => 'January',
                    "02" => 'February',
                    "03" => 'March',
                    "04" => 'April',
                    "05" => 'May',
                    "06" => 'June',
                    "07" => 'July',
                    "08" => 'August',
                    "09" => 'September',
                    "10" => 'October',
                    "11" => 'November',
                    "12" => 'December',
                ];

                // custom filters
                $filterArr = [];

                foreach ($filters as $filter) {
                    foreach ($filter->status as $status) {
                        $date = substr($status['created_at'], 0, 7); // the date portion (YYYY-MM-DD)
                    }

                    if (!isset($filterArr[$date])) {
                        foreach ($months as $index => $mth) {
                            $info = substr($date, 5, 2);

                            if ($info == $index) {
                                $filterArr[$date] = [
                                    'name'        => $mth,
                                    'product_qty' => 0,
                                    'Amount'      => 0,
                                    'amt'         => 0,
                                ];
                            }
                        }
                    }

                    foreach ($filter['order_details'] as $details) {
                        $filterArr[$date]['product_qty'] += $details['product_qty'];
                    }
                    $filterArr[$date]['Amount'] += discountedTotal($filter->pricing);
                    $filterArr[$date]['amt'] += discountedTotal($filter->pricing);
                }

                // Convert the associative array to a sequential array
                ksort($filterArr);
                $filterArr = array_values($filterArr);

                return $filterArr;
            }
        }
    }

    // dashboard advance payment calculation
    public function getAdvancePaymentStatistic($data): int
    {
        $type = $data->advance_payment;
        $amounts = 0;
        $orders = Order::query()->with('pricing')
            ->where('shop_id', $data->header('shop-id'))
            ->whereHas('pricing', function ($q) use ($type, $data) {
                if ($type === 'today') {
                    $q->whereDate('created_at', Carbon::today()->toDateString());
                }

                if ($type === 'yesterday') {
                    $q->whereDate('created_at', Carbon::yesterday()->toDateString());
                }

                if ($type === 'weekly') {
                    $q->whereBetween(
                        'created_at',
                        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                    );
                }

                if ($type === 'monthly') {
                    $q->whereMonth('created_at', Carbon::today()->month);
                }

                if ($type === 'custom') {
                    $startDate = Carbon::parse($data->start_date)->toDateString();
                    $endDate = Carbon::parse($data->end_date)->toDateString();

                    if ($startDate === $endDate) {
                        $q->whereDate('created_at', $startDate);
                    }

                    if ($startDate !== $endDate) {
                        $q->where(function ($query) use ($startDate, $endDate) {
                            $query->whereDate('created_at', '>=', $startDate)
                                ->whereDate('created_at', '<=', $endDate);
                        });
                    }
                }
            })
            ->get();

        foreach ($orders as $order) {
            $amounts += $order->pricing->advanced;
        }

        return $amounts;
    }

    public function getDiscountPaymentStatistic($data): int
    {
        $type = $data->discount_payment;
        $amounts = 0;

        $orders = Order::with('pricing')
            ->where('shop_id', $data->header('shop-id'))
            ->whereHas('pricing', function ($q) use ($type, $data) {
                if ($type === 'today') {
                    $q->whereDate('created_at', Carbon::today()->toDateString());
                }

                if ($type === 'yesterday') {
                    $q->whereDate('created_at', Carbon::yesterday()->toDateString());
                }

                if ($type === 'weekly') {
                    $q->whereBetween(
                        'created_at',
                        [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()->toDateString()]
                    );
                }

                if ($type === 'monthly') {
                    $q->whereMonth('created_at', Carbon::today()->month);
                }

                if ($type === 'custom') {
                    $startDate = Carbon::parse($data->start_date)->toDateString();
                    $endDate = Carbon::parse($data->end_date)->toDateString();

                    if ($startDate === $endDate) {
                        $q->where('type', Order::CONFIRMED)
                            ->whereDate('created_at', $startDate);
                    }

                    if ($startDate !== $endDate) {
                        $q->where('type', Order::CONFIRMED)
                            ->where(function ($query) use ($startDate, $endDate) {
                                $query->whereDate('created_at', '>=', $startDate)
                                    ->whereDate('created_at', '<=', $endDate);
                            });
                    }
                }

                if ($type === 'all') {
                    $q;
                }
            })
            ->get();

        foreach ($orders as $order) {
            if ($order->pricing->discount_type == 'amount') {
                $calculation = $order->pricing->discount;
            } else {
                $calculation = $order->pricing->discount / 100 * $order->pricing->grand_total;
            }
            $amounts += $calculation;
        }

        return $amounts;
    }

    public function totalOrderRatioCaculation($request): string
    {
        $type = $request->total;

        if ($type == 'today') {
            return todayDashboardRatioStatistics($request);
        }

        if ($type == 'yesterday') {
            return yesterdayDashboardRatioStatistics($request);
        }

        if ($type == 'weekly') {
            return weeklyDashboardRatioStatistics($request);
        }

        if ($type == 'monthly') {
            return monthlyDashboardRatioStatistics($request);
        }

        if ($type == 'yearly') {
            return yearlyDashboardRatioStatistics($request);
        }
    }

    public function confirmedOrderRatioCaculation($request)
    {
        $type = $request->confirm;

        if ($type == 'today') {
            return todayOrderRatioStatistics($request);
        }

        if ($type == 'yesterday') {
            return yesterdayOrderRatioStatistics($request);
        }

        if ($type == 'weekly') {
            return weeklyOrderRatioStatistics($request);
        }

        if ($type == 'monthly') {
            return monthlyOrderRatioStatistics($request);
        }

        if ($type == 'yearly') {
            return yearlyOrderRatioStatistics($request);
        }
    }

    public function cancelOrderRatioCaculation($request)
    {
        $type = $request->cancel;

        if ($type == 'today') {
            return todayOrderRatioStatistics($request);
        }

        if ($type == 'yesterday') {
            return yesterdayOrderRatioStatistics($request);
        }

        if ($type == 'weekly') {
            return weeklyOrderRatioStatistics($request);
        }

        if ($type == 'monthly') {
            return monthlyOrderRatioStatistics($request);
        }

        if ($type == 'yearly') {
            return yearlyOrderRatioStatistics($request);
        }
    }

    public function salesAmountRatioCaculation($request)
    {
        $type = $request->sales_amount;

        if ($type == 'today') {

            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();

            $yesterday_order = Order::query()
                ->with('pricing')
                ->whereHas('status', function ($query) use ($yesterday) {
                    $query->where('type', Order::CONFIRMED)->whereDate('created_at', $yesterday);
                })
                ->where('shop_id', $request->header('shop-id'))
                ->get();

            $yesterday_amount = 0;

            foreach ($yesterday_order as $previous) {
                $calculation = $previous->pricing->grand_total - $previous->pricing->shipping_cost;
                $yesterday_amount += $calculation;
            }

            $today_order = Order::query()
                ->with('pricing')
                ->whereHas('status', function ($query) use ($today) {
                    $query->where('type', Order::CONFIRMED)->whereDate('created_at', $today);
                })
                ->where('shop_id', $request->header('shop-id'))
                ->get();

            $today_amount = 0;

            foreach ($today_order as $current) {
                $calculation = $current->pricing->grand_total - $current->pricing->shipping_cost;
                $today_amount += $calculation;
            }

            if ($today_amount == 0 && $yesterday_amount == 0) {
                $ratio = Order::Zero;
            } else {
                if ($today_amount != 0) {
                    $ratio = round((($today_amount - $yesterday_amount) / $today_amount) * 100, 2) . '%';
                } else {
                    $ratio = Order::Negative;
                }
            }
        } else {
            if ($type == 'yesterday') {

                $last_thirdDay = Carbon::today()->subDays(2)->toDateString();
                $yesterday = Carbon::yesterday()->toDateString();

                $last_thirdDay_order = Order::query()->with('pricing')
                    ->whereHas('status', function ($query) use ($last_thirdDay) {
                        $query->where('type', Order::CONFIRMED)->whereDate('created_at', $last_thirdDay);
                    })
                    ->where('shop_id', $request->header('shop-id'))
                    ->get();

                $last_thirdDay_order_amount = 0;

                foreach ($last_thirdDay_order as $previous) {
                    $calculation = $previous->pricing->grand_total - $previous->pricing->shipping_cost;
                    $last_thirdDay_order_amount += $calculation;
                }

                $yesterday_order = Order::query()->with('pricing')
                    ->whereHas('status', function ($query) use ($yesterday) {
                        $query->where('type', Order::CONFIRMED)->whereDate('created_at', $yesterday);
                    })
                    ->where('shop_id', $request->header('shop-id'))
                    ->get();

                $yesterday_order_amount = 0;

                foreach ($yesterday_order as $current) {
                    $calculation = $current->pricing->grand_total - $current->pricing->shipping_cost;
                    $yesterday_order_amount += $calculation;
                }

                if ($yesterday_order_amount == 0 && $last_thirdDay_order_amount == 0) {
                    $ratio = Order::Zero;
                } else {
                    if ($yesterday_order_amount != 0) {
                        $ratio = round(
                            (($yesterday_order_amount - $last_thirdDay_order_amount) / $yesterday_order_amount) * 100,
                            2
                        ) . '%';
                    } else {
                        $ratio = Order::Negative;
                    }
                }
            } else {
                if ($type == 'weekly') {

                    // last 7 days date
                    $firstDay_of_last7days = Carbon::today()->subDays(7)->startOfDay();
                    $lastDay_of_last7days = Carbon::today();

                    // before 7 days date
                    $firstDay_of_before7days = Carbon::today()->subDays(14)->startOfDay();
                    $lastDay_of_before7days = Carbon::today()->subDays(7)->startOfDay();

                    $before_7Days_order = Order::query()->with('pricing')
                        ->whereHas('status', function ($query) use ($firstDay_of_before7days, $lastDay_of_before7days) {
                            $query->where('type', Order::CONFIRMED)->whereBetween(
                                'created_at',
                                [$firstDay_of_before7days, $lastDay_of_before7days]
                            );
                        })
                        ->where('shop_id', $request->header('shop-id'))
                        ->get();

                    $before_7Days_order_amount = 0;

                    foreach ($before_7Days_order as $previous) {
                        $calculation = $previous->pricing->grand_total - $previous->pricing->shipping_cost;
                        $before_7Days_order_amount += $calculation;
                    }

                    $last_7Days_order = Order::query()->with('pricing')
                        ->whereHas('status', function ($query) use ($firstDay_of_last7days, $lastDay_of_last7days) {
                            $query->where('type', Order::CONFIRMED)->whereBetween(
                                'created_at',
                                [$firstDay_of_last7days, $lastDay_of_last7days]
                            );
                        })
                        ->where('shop_id', $request->header('shop-id'))
                        ->get();

                    $last_7Days_order_amount = 0;

                    foreach ($last_7Days_order as $current) {
                        $calculation = $current->pricing->grand_total - $current->pricing->shipping_cost;
                        $last_7Days_order_amount += $calculation;
                    }

                    if ($last_7Days_order_amount == 0 && $before_7Days_order_amount == 0) {
                        $ratio = Order::Zero;
                    } else {
                        if ($last_7Days_order_amount != 0) {
                            $ratio = round(
                                (($last_7Days_order_amount - $before_7Days_order_amount) / $last_7Days_order_amount) * 100,
                                2
                            ) . '%';
                        } else {
                            $ratio = Order::Negative;
                        }
                    }
                } else {
                    if ($type == 'monthly') {

                        $running_month = Carbon::today()->month;
                        $previous_month = Carbon::today()->month - 1;

                        $previous_month_order = Order::query()->with('pricing')
                            ->whereHas('status', function ($query) use ($previous_month) {
                                $query->where('type', Order::CONFIRMED)->whereMonth('created_at', $previous_month);
                            })
                            ->where('shop_id', $request->header('shop-id'))
                            ->get();

                        $previous_month_order_amount = 0;

                        foreach ($previous_month_order as $previous) {
                            $calculation = $previous->pricing->grand_total - $previous->pricing->shipping_cost;
                            $previous_month_order_amount += $calculation;
                        }

                        $running_month_order = Order::query()->with('pricing')
                            ->whereHas('status', function ($query) use ($running_month) {
                                $query->where('type', Order::CONFIRMED)->whereMonth('created_at', $running_month);
                            })
                            ->where('shop_id', $request->header('shop-id'))
                            ->get();

                        $running_month_order_amount = 0;

                        foreach ($running_month_order as $current) {
                            $calculation = $current->pricing->grand_total - $current->pricing->shipping_cost;
                            $running_month_order_amount += $calculation;
                        }

                        if ($running_month_order_amount == 0 && $previous_month_order_amount == 0) {
                            $ratio = Order::Zero;
                        } else {
                            if ($running_month_order_amount != 0) {
                                $ratio = round(
                                    (($running_month_order_amount - $previous_month_order_amount) / $running_month_order_amount) * 100,
                                    2
                                ) . '%';
                            } else {
                                $ratio = Order::Negative;
                            }
                        }
                    } else {
                        if ($type === 'yearly') {

                            $running_year = Carbon::today()->year;
                            $previous_year = Carbon::today()->year - 1;

                            $previous_year_order = Order::query()->with('pricing')
                                ->whereHas('status', function ($query) use ($previous_year) {
                                    $query->where('type', Order::CONFIRMED)->whereYear('created_at', $previous_year);
                                })
                                ->where('shop_id', $request->header('shop-id'))
                                ->get();

                            $previous_year_order_amount = 0;

                            foreach ($previous_year_order as $previous) {
                                $calculation = $previous->pricing->grand_total - $previous->pricing->shipping_cost;
                                $previous_year_order_amount += $calculation;
                            }

                            $running_year_order = Order::query()->with('pricing')
                                ->whereHas('status', function ($query) use ($running_year) {
                                    $query->where('type', Order::CONFIRMED)->whereYear('created_at', $running_year);
                                })
                                ->where('shop_id', $request->header('shop-id'))
                                ->get();

                            $running_year_order_amount = 0;

                            foreach ($running_year_order as $current) {
                                $calculation = $current->pricing->grand_total - $current->pricing->shipping_cost;
                                $running_year_order_amount += $calculation;
                            }

                            if ($running_year_order_amount == 0 && $previous_year_order_amount == 0) {
                                $ratio = Order::Zero;
                            } else {
                                if ($running_year_order_amount != 0) {
                                    $ratio = round(
                                        (($running_year_order_amount - $previous_year_order_amount) / $running_year_order_amount) * 100,
                                        2
                                    ) . '%';
                                } else {
                                    $ratio = Order::Negative;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $ratio;
    }

    public function discountAmountRatioCaculation($request)
    {
        $type = $request->discount_amount;

        if ($type === 'today') {

            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();

            $yesterday_order = Order::query()->with('pricing')
                ->whereHas('pricing', function ($query) use ($yesterday) {
                    $query->whereDate('created_at', $yesterday);
                })
                ->where('shop_id', $request->header('shop-id'))
                ->get();

            $yesterday_order_amount = 0;

            foreach ($yesterday_order as $previous) {
                if ($previous->pricing->discount_type == 'amount') {
                    $calculation = $previous->pricing->discount;
                } else {
                    $calculation = $previous->pricing->discount / 100 * $previous->pricing->grand_total;
                }
                $yesterday_order_amount += $calculation;
            }

            $today_order = Order::query()->with('pricing')
                ->whereHas('pricing', function ($query) use ($today) {
                    $query->whereDate('created_at', $today);
                })
                ->where('shop_id', $request->header('shop-id'))
                ->get();

            $today_order_amount = 0;

            foreach ($today_order as $current) {
                if ($current->pricing->discount_type == 'amount') {
                    $calculation = $current->pricing->discount;
                } else {
                    $calculation = $current->pricing->discount / 100 * $current->pricing->grand_total;
                }
                $today_order_amount += $calculation;
            }

            if ($today_order_amount == 0 && $yesterday_order_amount == 0) {
                $ratio = Order::Zero;
            } else {
                if ($today_order_amount != 0) {
                    if ($today_order_amount >= $yesterday_order_amount) {
                        $big_order_amount = $today_order_amount;
                    } else {
                        if ($today_order_amount <= $yesterday_order_amount) {
                            $big_order_amount = $yesterday_order_amount;
                        }
                    }
                    $ratio = round((($today_order_amount - $yesterday_order_amount) / $big_order_amount) * 100, 2) . '%';
                } else {
                    $ratio = Order::Negative;
                }
            }
        } else {
            if ($type == 'yesterday') {

                $last_thirdDay = Carbon::today()->subDays(2)->toDateString();
                $yesterday = Carbon::yesterday()->toDateString();

                $last_thirdDay_order = Order::query()->with('pricing')
                    ->whereHas('status', function ($query) use ($last_thirdDay) {
                        $query->whereDate('created_at', $last_thirdDay);
                    })
                    ->where('shop_id', $request->header('shop-id'))
                    ->get();

                $last_thirdDay_order_amount = 0;

                foreach ($last_thirdDay_order as $previous) {
                    if ($previous->pricing->discount_type == 'amount') {
                        $calculation = $previous->pricing->discount;
                    } else {
                        $calculation = $previous->pricing->discount / 100 * $previous->pricing->grand_total;
                    }
                    $last_thirdDay_order_amount += $calculation;
                }

                $yesterday_order = Order::query()->with('pricing')
                    ->whereHas('status', function ($query) use ($yesterday) {
                        $query->whereDate('created_at', $yesterday);
                    })
                    ->where('shop_id', $request->header('shop-id'))
                    ->get();

                $yesterday_order_amount = 0;

                foreach ($yesterday_order as $current) {
                    if ($current->pricing->discount_type == 'amount') {
                        $calculation = $current->pricing->discount;
                    } else {
                        $calculation = $current->pricing->discount / 100 * $current->pricing->grand_total;
                    }
                    $yesterday_order_amount += $calculation;
                }

                if ($yesterday_order_amount == 0 && $last_thirdDay_order_amount == 0) {
                    $ratio = Order::Zero;
                } else {
                    if ($yesterday_order_amount != 0) {
                        if ($yesterday_order_amount >= $last_thirdDay_order_amount) {
                            $big_order_amount = $yesterday_order_amount;
                        } else {
                            if ($yesterday_order_amount <= $last_thirdDay_order_amount) {
                                $big_order_amount = $last_thirdDay_order_amount;
                            }
                        }
                        $ratio = round(
                            (($yesterday_order_amount - $last_thirdDay_order_amount) / $big_order_amount) * 100,
                            2
                        ) . '%';
                    } else {
                        $ratio = Order::Negative;
                    }
                }
            } else {
                if ($type == 'weekly') {

                    // last 7 days date
                    $firstDay_of_last7days = Carbon::today()->subDays(7)->startOfDay();
                    $lastDay_of_last7days = Carbon::today();

                    // before 7 days date
                    $firstDay_of_before7days = Carbon::today()->subDays(14)->startOfDay();
                    $lastDay_of_before7days = Carbon::today()->subDays(7)->startOfDay();

                    $before_7Days_order = Order::query()->with('pricing')
                        ->whereHas('status', function ($query) use ($firstDay_of_before7days, $lastDay_of_before7days) {
                            $query->whereBetween('created_at', [$firstDay_of_before7days, $lastDay_of_before7days]);
                        })
                        ->where('shop_id', $request->header('shop-id'))
                        ->get();

                    $before_7Days_order_amount = 0;

                    foreach ($before_7Days_order as $previous) {
                        if ($previous->pricing->discount_type == 'amount') {
                            $calculation = $previous->pricing->discount;
                        } else {
                            $calculation = $previous->pricing->discount / 100 * $previous->pricing->grand_total;
                        }
                        $before_7Days_order_amount += $calculation;
                    }

                    $last_7Days_order = Order::query()->with('pricing')
                        ->whereHas('status', function ($query) use ($firstDay_of_last7days, $lastDay_of_last7days) {
                            $query->whereBetween('created_at', [$firstDay_of_last7days, $lastDay_of_last7days]);
                        })
                        ->where('shop_id', $request->header('shop-id'))
                        ->get();

                    $last_7Days_order_amount = 0;

                    foreach ($last_7Days_order as $current) {
                        if ($current->pricing->discount_type == 'amount') {
                            $calculation = $current->pricing->discount;
                        } else {
                            $calculation = $current->pricing->discount / 100 * $current->pricing->grand_total;
                        }
                        $last_7Days_order_amount += $calculation;
                    }

                    if ($last_7Days_order_amount == 0 && $before_7Days_order_amount == 0) {
                        $ratio = Order::Zero;
                    } else {
                        if ($last_7Days_order_amount != 0) {
                            if ($last_7Days_order_amount >= $before_7Days_order_amount) {
                                $big_order_amount = $last_7Days_order_amount;
                            } else {
                                if ($last_7Days_order_amount <= $before_7Days_order_amount) {
                                    $big_order_amount = $before_7Days_order_amount;
                                }
                            }
                            $ratio = round(
                                (($last_7Days_order_amount - $before_7Days_order_amount) / $big_order_amount) * 100,
                                2
                            ) . '%';
                        } else {
                            $ratio = Order::Negative;
                        }
                    }
                } else {
                    if ($type == 'monthly') {

                        $running_month = Carbon::today()->month;
                        $previous_month = Carbon::today()->month - 1;

                        $previous_month_order = Order::query()->with('pricing')
                            ->whereHas('status', function ($query) use ($previous_month) {
                                $query->whereMonth('created_at', $previous_month);
                            })
                            ->where('shop_id', $request->header('shop-id'))
                            ->get();

                        $previous_month_order_amount = 0;

                        foreach ($previous_month_order as $previous) {
                            if ($previous->pricing->discount_type == 'amount') {
                                $calculation = $previous->pricing->discount;
                            } else {
                                $calculation = $previous->pricing->discount / 100 * $previous->pricing->grand_total;
                            }
                            $previous_month_order_amount += $calculation;
                        }

                        $running_month_order = Order::query()->with('pricing')
                            ->whereHas('status', function ($query) use ($running_month) {
                                $query->whereMonth('created_at', $running_month);
                            })
                            ->where('shop_id', $request->header('shop-id'))
                            ->get();

                        $running_month_order_amount = 0;

                        foreach ($running_month_order as $current) {
                            if ($current->pricing->discount_type == 'amount') {
                                $calculation = $current->pricing->discount;
                            } else {
                                $calculation = $current->pricing->discount / 100 * $current->pricing->grand_total;
                            }
                            $running_month_order_amount += $calculation;
                        }

                        if ($running_month_order_amount == 0 && $previous_month_order_amount == 0) {
                            $ratio = Order::Zero;
                        } else {
                            if ($running_month_order_amount != 0) {
                                if ($running_month_order_amount >= $previous_month_order_amount) {
                                    $big_order_amount = $running_month_order_amount;
                                } else {
                                    if ($running_month_order_amount <= $previous_month_order_amount) {
                                        $big_order_amount = $previous_month_order_amount;
                                    }
                                }
                                $ratio = round(
                                    (($running_month_order_amount - $previous_month_order_amount) / $big_order_amount) * 100,
                                    2
                                ) . '%';
                            } else {
                                $ratio = Order::Negative;
                            }
                        }
                    } else {
                        if ($type == 'yearly') {

                            $running_year = Carbon::today()->year;
                            $previous_year = Carbon::today()->year - 1;

                            $previous_year_order = Order::query()->with('pricing')
                                ->whereHas('status', function ($query) use ($previous_year) {
                                    $query->whereYear('created_at', $previous_year);
                                })
                                ->where('shop_id', $request->header('shop-id'))
                                ->get();

                            $previous_year_order_amount = 0;

                            foreach ($previous_year_order as $previous) {
                                if ($previous->pricing->discount_type == 'amount') {
                                    $calculation = $previous->pricing->discount;
                                } else {
                                    $calculation = $previous->pricing->discount / 100 * $previous->pricing->grand_total;
                                }
                                $previous_year_order_amount += $calculation;
                            }

                            $running_year_order = Order::query()->with('pricing')
                                ->whereHas('status', function ($query) use ($running_year) {
                                    $query->whereYear('created_at', $running_year);
                                })
                                ->where('shop_id', $request->header('shop-id'))
                                ->get();

                            $running_year_order_amount = 0;

                            foreach ($running_year_order as $current) {
                                if ($current->pricing->discount_type == 'amount') {
                                    $calculation = $current->pricing->discount;
                                } else {
                                    $calculation = $current->pricing->discount / 100 * $current->pricing->grand_total;
                                }
                                $running_year_order_amount += $calculation;
                            }

                            if ($running_year_order_amount == 0 && $previous_year_order_amount == 0) {
                                $ratio = Order::Zero;
                            } else {
                                if ($running_year_order_amount != 0) {
                                    if ($running_year_order_amount >= $previous_year_order_amount) {
                                        $big_order_amount = $running_year_order_amount;
                                    } else {
                                        if ($running_year_order_amount <= $previous_year_order_amount) {
                                            $big_order_amount = $previous_year_order_amount;
                                        }
                                    }
                                    $ratio = round(
                                        (($running_year_order_amount - $previous_year_order_amount) / $big_order_amount) * 100,
                                        2
                                    ) . '%';
                                } else {
                                    $ratio = Order::Negative;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $ratio;
    }

    public function advanceAmountRatioCaculation($request)
    {
        $type = $request->advance_amount;

        if ($type == 'today') {

            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();

            $yesterday_order = Order::query()->with('pricing')
                ->whereHas('pricing', function ($query) use ($yesterday) {
                    $query->whereDate('created_at', $yesterday);
                })
                ->where('shop_id', $request->header('shop-id'))
                ->get();

            $yesterday_order_amount = 0;

            foreach ($yesterday_order as $previous) {
                $calculation = $previous->pricing->advanced;
                $yesterday_order_amount += $calculation;
            }

            $today_order = Order::query()->with('pricing')
                ->whereHas('pricing', function ($query) use ($today) {
                    $query->whereDate('created_at', $today);
                })
                ->where('shop_id', $request->header('shop-id'))
                ->get();

            $today_order_amount = 0;

            foreach ($today_order as $current) {
                $calculation = $current->pricing->advanced;
                $today_order_amount += $calculation;
            }

            if ($today_order_amount == 0 && $yesterday_order_amount == 0) {
                $ratio = Order::Zero;
            } else {
                if ($today_order_amount != 0) {
                    if ($today_order_amount >= $yesterday_order_amount) {
                        $big_order_amount = $today_order_amount;
                    } else {
                        if ($today_order_amount <= $yesterday_order_amount) {
                            $big_order_amount = $yesterday_order_amount;
                        }
                    }
                    $ratio = round((($today_order_amount - $yesterday_order_amount) / $big_order_amount) * 100, 2) . '%';
                } else {
                    $ratio = Order::Negative;
                }
            }
        } else {
            if ($type == 'yesterday') {

                $last_thirdDay = Carbon::today()->subDays(2)->toDateString();
                $yesterday = Carbon::yesterday()->toDateString();

                $last_thirdDay_order = Order::query()->with('pricing')
                    ->whereHas('status', function ($query) use ($last_thirdDay) {
                        $query->whereDate('created_at', $last_thirdDay);
                    })
                    ->where('shop_id', $request->header('shop-id'))
                    ->get();

                $last_thirdDay_order_amount = 0;

                foreach ($last_thirdDay_order as $previous) {
                    $calculation = $previous->pricing->advanced;
                    $last_thirdDay_order_amount += $calculation;
                }

                $yesterday_order = Order::query()->with('pricing')
                    ->whereHas('status', function ($query) use ($yesterday) {
                        $query->whereDate('created_at', $yesterday);
                    })
                    ->where('shop_id', $request->header('shop-id'))
                    ->get();

                $yesterday_order_amount = 0;

                foreach ($yesterday_order as $current) {
                    $calculation = $current->pricing->advanced;
                    $yesterday_order_amount += $calculation;
                }

                if ($yesterday_order_amount == 0 && $last_thirdDay_order_amount == 0) {
                    $ratio = Order::Zero;
                } else {
                    if ($yesterday_order_amount != 0) {
                        if ($yesterday_order_amount >= $last_thirdDay_order_amount) {
                            $big_order_amount = $yesterday_order_amount;
                        } else {
                            if ($yesterday_order_amount <= $last_thirdDay_order_amount) {
                                $big_order_amount = $last_thirdDay_order_amount;
                            }
                        }
                        $ratio = round(
                            (($yesterday_order_amount - $last_thirdDay_order_amount) / $big_order_amount) * 100,
                            2
                        ) . '%';
                    } else {
                        $ratio = Order::Negative;
                    }
                }
            } else {
                if ($type == 'weekly') {

                    // last 7 days date
                    $firstDay_of_last7days = Carbon::today()->subDays(7)->startOfDay();
                    $lastDay_of_last7days = Carbon::today();

                    // before 7 days date
                    $firstDay_of_before7days = Carbon::today()->subDays(14)->startOfDay();
                    $lastDay_of_before7days = Carbon::today()->subDays(7)->startOfDay();

                    $before_7Days_order = Order::query()->with('pricing')
                        ->whereHas('status', function ($query) use ($firstDay_of_before7days, $lastDay_of_before7days) {
                            $query->whereBetween('created_at', [$firstDay_of_before7days, $lastDay_of_before7days]);
                        })
                        ->where('shop_id', $request->header('shop-id'))
                        ->get();

                    $before_7Days_order_amount = 0;

                    foreach ($before_7Days_order as $previous) {
                        $calculation = $previous->pricing->advanced;
                        $before_7Days_order_amount += $calculation;
                    }

                    $last_7Days_order = Order::query()->with('pricing')
                        ->whereHas('status', function ($query) use ($firstDay_of_last7days, $lastDay_of_last7days) {
                            $query->whereBetween('created_at', [$firstDay_of_last7days, $lastDay_of_last7days]);
                        })
                        ->where('shop_id', $request->header('shop-id'))
                        ->get();

                    $last_7Days_order_amount = 0;

                    foreach ($last_7Days_order as $current) {
                        $calculation = $current->pricing->advanced;
                        $last_7Days_order_amount += $calculation;
                    }

                    if ($last_7Days_order_amount == 0 && $before_7Days_order_amount == 0) {
                        $ratio = Order::Zero;
                    } else {
                        if ($last_7Days_order_amount != 0) {
                            if ($last_7Days_order_amount >= $before_7Days_order_amount) {
                                $big_order_amount = $last_7Days_order_amount;
                            } else {
                                if ($last_7Days_order_amount <= $before_7Days_order_amount) {
                                    $big_order_amount = $before_7Days_order_amount;
                                }
                            }
                            $ratio = round(
                                (($last_7Days_order_amount - $before_7Days_order_amount) / $big_order_amount) * 100,
                                2
                            ) . '%';
                        } else {
                            $ratio = Order::Negative;
                        }
                    }
                } else {
                    if ($type == 'monthly') {

                        $running_month = Carbon::today()->month;
                        $previous_month = Carbon::today()->month - 1;

                        $previous_month_order = Order::query()->with('pricing')
                            ->whereHas('status', function ($query) use ($previous_month) {
                                $query->whereMonth('created_at', $previous_month);
                            })
                            ->where('shop_id', $request->header('shop-id'))
                            ->get();

                        $previous_month_order_amount = 0;

                        foreach ($previous_month_order as $previous) {
                            $calculation = $previous->pricing->advanced;
                            $previous_month_order_amount += $calculation;
                        }

                        $running_month_order = Order::query()->with('pricing')
                            ->whereHas('status', function ($query) use ($running_month) {
                                $query->whereMonth('created_at', $running_month);
                            })
                            ->where('shop_id', $request->header('shop-id'))
                            ->get();

                        $running_month_order_amount = 0;

                        foreach ($running_month_order as $current) {
                            $calculation = $current->pricing->advanced;
                            $running_month_order_amount += $calculation;
                        }

                        if ($running_month_order_amount == 0 && $previous_month_order_amount == 0) {
                            $ratio = Order::Zero;
                        } else {
                            if ($running_month_order_amount != 0) {
                                if ($running_month_order_amount >= $previous_month_order_amount) {
                                    $big_order_amount = $running_month_order_amount;
                                } else {
                                    if ($running_month_order_amount <= $previous_month_order_amount) {
                                        $big_order_amount = $previous_month_order_amount;
                                    }
                                }
                                $ratio = round(
                                    (($running_month_order_amount - $previous_month_order_amount) / $big_order_amount) * 100,
                                    2
                                ) . '%';
                            } else {
                                $ratio = Order::Negative;
                            }
                        }
                    } else {
                        if ($type == 'yearly') {

                            $running_year = Carbon::today()->year;
                            $previous_year = Carbon::today()->year - 1;

                            $previous_year_order = Order::query()->with('pricing')
                                ->whereHas('status', function ($query) use ($previous_year) {
                                    $query->whereYear('created_at', $previous_year);
                                })
                                ->where('shop_id', $request->header('shop-id'))
                                ->get();

                            $previous_year_order_amount = 0;

                            foreach ($previous_year_order as $previous) {
                                $calculation = $previous->pricing->advanced;
                                $previous_year_order_amount += $calculation;
                            }

                            $running_year_order = Order::query()->with('pricing')
                                ->whereHas('status', function ($query) use ($running_year) {
                                    $query->whereYear('created_at', $running_year);
                                })
                                ->where('shop_id', $request->header('shop-id'))
                                ->get();

                            $running_year_order_amount = 0;

                            foreach ($running_year_order as $current) {
                                $calculation = $current->pricing->advanced;
                                $running_year_order_amount += $calculation;
                            }

                            if ($running_year_order_amount == 0 && $previous_year_order_amount == 0) {
                                $ratio = Order::Zero;
                            } else {
                                if ($running_year_order_amount != 0) {
                                    if ($running_year_order_amount >= $previous_year_order_amount) {
                                        $big_order_amount = $running_year_order_amount;
                                    } else {
                                        if ($running_year_order_amount <= $previous_year_order_amount) {
                                            $big_order_amount = $previous_year_order_amount;
                                        }
                                    }
                                    $ratio = round(
                                        (($running_year_order_amount - $previous_year_order_amount) / $big_order_amount) * 100,
                                        2
                                    ) . '%';
                                } else {
                                    $ratio = Order::Negative;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $ratio;
    }

    // Dashboard Channel ratio
    public function channelLandingRatioCaculation($request)
    {
        $type = $request->landing;
        $order_type = 'landing';

        if ($type == 'today') {
            return todayChannelRatioStatistics($request, $order_type);
        }

        if ($type == 'yesterday') {
            return yesterdayChannelRatioStatistics($request, $order_type);
        }

        if ($type == 'weekly') {
            return weeklyChannelRatioStatistics($request, $order_type);
        }

        if ($type == 'monthly') {
            return monthlyChannelRatioStatistics($request, $order_type);
        }
    }

    public function channelPhoneRatioCaculation($request)
    {
        $type = $request->phone;
        $order_type = 'phone';

        if ($type == 'today') {
            return todayChannelRatioStatistics($request, $order_type);
        }

        if ($type == 'yesterday') {
            return yesterdayChannelRatioStatistics($request, $order_type);
        }

        if ($type == 'weekly') {
            return weeklyChannelRatioStatistics($request, $order_type);
        }

        if ($type == 'monthly') {
            return monthlyChannelRatioStatistics($request, $order_type);
        }
    }

    public function channelSocialRatioCaculation($request)
    {
        $type = $request->social;
        $order_type = 'social';

        if ($type == 'today') {
            return todayChannelRatioStatistics($request, $order_type);
        }

        if ($type == 'yesterday') {
            return yesterdayChannelRatioStatistics($request, $order_type);
        }

        if ($type == 'weekly') {
            return weeklyChannelRatioStatistics($request, $order_type);
        }

        if ($type == 'monthly') {
            return monthlyChannelRatioStatistics($request, $order_type);
        }
    }

    public function channelWebsiteRatioCaculation($request)
    {
        $type = $request->website;
        $order_type = 'website';

        if ($type === 'today') {
            $ratio = todayChannelRatioStatistics($request, $order_type);
        } elseif ($type === 'yesterday') {
            $ratio = yesterdayChannelRatioStatistics($request, $order_type);
        } elseif ($type === 'weekly') {
            $ratio = weeklyChannelRatioStatistics($request, $order_type);
        } elseif ($type === 'monthly') {
            $ratio = monthlyChannelRatioStatistics($request, $order_type);
        } else {
            $ratio = monthlyChannelRatioStatistics($request, $order_type);
        }

        return $ratio;
    }
}