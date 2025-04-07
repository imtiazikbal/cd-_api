<?php

use App\Models\Order;
use Illuminate\Support\Carbon;

// Dashboard Channel Ratio Feature
function todayChannelRatioStatistics($request, $orderType)
{
    $today = Carbon::today()->toDateString();
    $yesterday = Carbon::yesterday()->toDateString();

    $yesterdayOrder = orderCountByDate($yesterday, $orderType, $request->header('shop-id'));
    $todayOrder = orderCountByDate($today, $orderType, $request->header('shop-id'));

    if ($todayOrder == 0 && $yesterdayOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($todayOrder != 0) {
        if ($todayOrder >= $yesterdayOrder) {
            $bigOrderCount = $todayOrder;
        } elseif ($todayOrder <= $yesterdayOrder) {
            $bigOrderCount = $yesterdayOrder;
        }
        $ratio = round((($todayOrder - $yesterdayOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function yesterdayChannelRatioStatistics($request, $orderType)
{
    $lastThirdDay = Carbon::today()->subDays(2)->toDateString();
    $yesterday = Carbon::yesterday()->toDateString();

    $lastThirdDayOrder = orderCountByDate($lastThirdDay, $orderType, $request->header('shop-id'));
    $yesterdayOrder = orderCountByDate($yesterday, $orderType, $request->header('shop-id'));

    if ($yesterdayOrder == 0 && $lastThirdDayOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($yesterdayOrder != 0) {
        if ($yesterdayOrder >= $lastThirdDayOrder) {
            $bigOrderCount = $yesterdayOrder;
        } elseif ($yesterdayOrder <= $lastThirdDayOrder) {
            $bigOrderCount = $lastThirdDayOrder;
        }
        $ratio = round((($yesterdayOrder - $lastThirdDayOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function weeklyChannelRatioStatistics($request, $orderType)
{
    // last 7 days date
    $firstDayOfLast7days = Carbon::today()->subDays(7)->startOfDay();
    $lastDayOfLast7days = Carbon::today();

    // before 7 days date
    $firstDayOfBefore7days = Carbon::today()->subDays(14)->startOfDay();
    $lastDayOfBefore7days = Carbon::today()->subDays(7)->startOfDay();

    $before7DaysOrder = Order::query()
        ->whereBetween('created_at', [$firstDayOfBefore7days, $lastDayOfBefore7days])
        ->where('order_type', $orderType)
        ->where('shop_id', $request->header('shop-id'))
        ->count();

    $last7DaysOrder = Order::query()
        ->whereBetween('created_at', [$firstDayOfLast7days, $lastDayOfLast7days])
        ->where('order_type', $orderType)
        ->where('shop_id', $request->header('shop-id'))
        ->count();

    if ($last7DaysOrder == 0 && $before7DaysOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($last7DaysOrder != 0) {
        if ($last7DaysOrder >= $before7DaysOrder) {
            $bigOrderCount = $last7DaysOrder;
        } elseif ($last7DaysOrder <= $before7DaysOrder) {
            $bigOrderCount = $before7DaysOrder;
        }
        $ratio = round((($last7DaysOrder - $before7DaysOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}


function monthlyChannelRatioStatistics($request, $orderType)
{
    $runningMonth = Carbon::today()->month;
    $previousMonth = Carbon::today()->month - 1;

    $previousMonthOrder = Order::query()
        ->whereMonth('created_at', $previousMonth)
        ->where('order_type', $orderType)
        ->where('shop_id', $request->header('shop-id'))
        ->count();

    $runningMonthOrder = Order::query()
        ->whereMonth('created_at', $runningMonth)
        ->where('order_type', $orderType)
        ->where('shop_id', $request->header('shop-id'))
        ->count();

    if ($runningMonthOrder == 0 && $previousMonthOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($runningMonthOrder != 0) {
        if ($runningMonthOrder >= $previousMonthOrder) {
            $bigOrderCount = $runningMonthOrder;
        } elseif ($runningMonthOrder <= $previousMonthOrder) {
            $bigOrderCount = $previousMonthOrder;
        }

        $ratio = round((($runningMonthOrder - $previousMonthOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}



function orderCountByDate($value, $orderType, $shopId)
{
    return  Order::query()
        ->whereDate('created_at', $value)
        ->where('order_type', $orderType)
        ->where('shop_id', $shopId)
        ->count();
}


// Dashboard Total Order Ratio Feature
function todayDashboardRatioStatistics($request)
{
    $today = Carbon::today()->toDateString();
    $yesterday = Carbon::yesterday()->toDateString();

    $yesterdayOrder = getAllOrderByDate($yesterday, $request);
    $todayOrder = getAllOrderByDate($today, $request);

    if ($todayOrder == 0 && $yesterdayOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($todayOrder != 0) {
        if ($todayOrder >= $yesterdayOrder) {
            $bigOrderCount = $todayOrder;
        } elseif ($todayOrder <= $yesterdayOrder) {
            $bigOrderCount = $yesterdayOrder;
        }
        $ratio = round((($todayOrder - $yesterdayOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function yesterdayDashboardRatioStatistics($request)
{
    $lastThirdDay = Carbon::today()->subDays(2)->toDateString();
    $yesterday = Carbon::yesterday()->toDateString();

    $lastThirdDayOrder = getAllOrderByDate($lastThirdDay, $request);
    $yesterdayOrder = getAllOrderByDate($yesterday, $request);

    if ($yesterdayOrder == 0 && $lastThirdDayOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($yesterdayOrder != 0) {
        if ($yesterdayOrder >= $lastThirdDayOrder) {
            $bigOrderCount = $yesterdayOrder;
        } elseif ($yesterdayOrder <= $lastThirdDayOrder) {
            $bigOrderCount = $lastThirdDayOrder;
        }
        $ratio = round((($yesterdayOrder - $lastThirdDayOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}


function weeklyDashboardRatioStatistics($request)
{
    // last 7 days date
    $firstDayOfLast7days = Carbon::today()->subDays(7)->startOfDay();
    $lastDayOfLast7days = Carbon::today();

    // before 7 days date
    $firstDayOfBefore7days = Carbon::today()->subDays(14)->startOfDay();
    $lastDayOfBefore7days = Carbon::today()->subDays(7)->startOfDay();

    $before7DaysOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereBetween('created_at', [$firstDayOfBefore7days, $lastDayOfBefore7days])
        ->count();

    $last7DaysOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereBetween('created_at', [$firstDayOfLast7days, $lastDayOfLast7days])
        ->count();

    if ($last7DaysOrder == 0 && $before7DaysOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($last7DaysOrder != 0) {
        if ($last7DaysOrder >= $before7DaysOrder) {
            $bigOrderCount = $last7DaysOrder;
        } elseif ($last7DaysOrder <= $before7DaysOrder) {
            $bigOrderCount = $before7DaysOrder;
        }
        $ratio = round((($last7DaysOrder - $before7DaysOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function monthlyDashboardRatioStatistics($request)
{
    $runningMonth = Carbon::today()->month;
    $previousMonth = Carbon::today()->month - 1;

    $previousMonthOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereMonth('created_at', $previousMonth)
        ->count();

    $runningMonthOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereMonth('created_at', $runningMonth)
        ->count();

    if ($runningMonthOrder == 0 && $previousMonthOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($runningMonthOrder != 0) {
        if ($runningMonthOrder >= $previousMonthOrder) {
            $bigOrderCount = $runningMonthOrder;
        } elseif ($runningMonthOrder <= $previousMonthOrder) {
            $bigOrderCount = $previousMonthOrder;
        }
        $ratio = round((($runningMonthOrder - $previousMonthOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function yearlyDashboardRatioStatistics($request)
{
    $runningYear = Carbon::today()->year;
    $previousYear = Carbon::today()->year - 1;

    $previousYearOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereYear('created_at', $previousYear)
        ->count();

    $runningYearOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereYear('created_at', $runningYear)
        ->count();

    if ($runningYearOrder == 0 && $previousYearOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($runningYearOrder != 0) {
        if ($runningYearOrder >= $previousYearOrder) {
            $bigOrderCount = $runningYearOrder;
        } elseif ($runningYearOrder <= $previousYearOrder) {
            $bigOrderCount = $previousYearOrder;
        }
        $ratio = round((($runningYearOrder - $previousYearOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function getAllOrderByDate($value, $request)
{
    return Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereDate('created_at', $value)
        ->count();
}


// Dashboard Order Ratio Feature By Order Status
function todayOrderRatioStatistics($request)
{
    $today = Carbon::today()->toDateString();
    $yesterday = Carbon::yesterday()->toDateString();

    $yesterdayOrder = getOrderByOrderStatus($yesterday, $request);
    $todayOrder = getOrderByOrderStatus($today, $request);

    if ($todayOrder == 0 && $yesterdayOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($todayOrder != 0) {
        if ($todayOrder >= $yesterdayOrder) {
            $bigOrderCount = $todayOrder;
        } elseif ($todayOrder <= $yesterdayOrder) {
            $bigOrderCount = $yesterdayOrder;
        }
        $ratio = round((($todayOrder - $yesterdayOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function yesterdayOrderRatioStatistics($request)
{
    $lastThirdDay = Carbon::today()->subDays(2)->toDateString();
    $yesterday = Carbon::yesterday()->toDateString();

    $lastThirdDayOrder = getOrderByOrderStatus($lastThirdDay, $request);
    $yesterdayOrder = getOrderByOrderStatus($yesterday, $request);

    if ($yesterdayOrder == 0 && $lastThirdDayOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($yesterdayOrder != 0) {
        if ($yesterdayOrder >= $lastThirdDayOrder) {
            $bigOrderCount = $yesterdayOrder;
        } elseif ($yesterdayOrder <= $lastThirdDayOrder) {
            $bigOrderCount = $lastThirdDayOrder;
        }
        $ratio = round((($yesterdayOrder - $lastThirdDayOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function weeklyOrderRatioStatistics($request)
{
    // last 7 days date
    $firstDayOfLast7days = Carbon::today()->subDays(7)->startOfDay();
    $lastDayOfLast7days = Carbon::today();

    // before 7 days date
    $firstDayOfBefore7days = Carbon::today()->subDays(14)->startOfDay();
    $lastDayOfBefore7days = Carbon::today()->subDays(7)->startOfDay();

    $before7DaysOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereHas('status', function ($query) use ($firstDayOfBefore7days, $lastDayOfBefore7days) {
            $query->select('id', 'type', 'created_at')
            ->where('type', Order::CONFIRMED)
            ->whereBetween('created_at', [$firstDayOfBefore7days, $lastDayOfBefore7days]);
        })
        ->count();

    $last7DaysOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereHas('status', function ($query) use ($firstDayOfLast7days, $lastDayOfLast7days) {
            $query->select('id', 'type', 'created_at')
            ->where('type', Order::CONFIRMED)
            ->whereBetween('created_at', [$firstDayOfLast7days, $lastDayOfLast7days]);
        })
        ->count();

    if ($last7DaysOrder == 0 && $before7DaysOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($last7DaysOrder != 0) {
        if ($last7DaysOrder >= $before7DaysOrder) {
            $bigOrderCount = $last7DaysOrder;
        } elseif ($last7DaysOrder <= $before7DaysOrder) {
            $bigOrderCount = $before7DaysOrder;
        }
        $ratio = round((($last7DaysOrder - $before7DaysOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}


function monthlyOrderRatioStatistics($request)
{
    $runningMonth = Carbon::today()->month;
    $previousMonth = Carbon::today()->month - 1;

    $previousMonthOrder = Order::query()
        ->select('id', 'type', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereHas('status', function ($query) use ($previousMonth) {
            $query->select('id', 'type', 'created_at')
            ->where('type', Order::CONFIRMED)
            ->whereMonth('created_at', $previousMonth);
        })
        ->count();

    $runningMonthOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereHas('status', function ($query) use ($runningMonth) {
            $query->select('id', 'type', 'created_at')
            ->where('type', Order::CONFIRMED)
            ->whereMonth('created_at', $runningMonth);
        })
        ->count();

    if ($runningMonthOrder == 0 && $previousMonthOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($runningMonthOrder != 0) {
        if ($runningMonthOrder >= $previousMonthOrder) {
            $bigOrderCount = $runningMonthOrder;
        } elseif ($runningMonthOrder <= $previousMonthOrder) {
            $bigOrderCount = $previousMonthOrder;
        }
        $ratio = round((($runningMonthOrder - $previousMonthOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function yearlyOrderRatioStatistics($request)
{
    $runningYear = Carbon::today()->year;
    $previousYear = Carbon::today()->year - 1;

    $previousYearOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereHas('status', function ($query) use ($previousYear) {
            $query->select('id', 'type', 'created_at')
            ->where('type', Order::CONFIRMED)
            ->whereYear('created_at', $previousYear);
        })
        ->count();

    $runningYearOrder = Order::query()
        ->select('id', 'shop_id', 'created_at')
        ->where('shop_id', $request->header('shop-id'))
        ->whereHas('status', function ($query) use ($runningYear) {
            $query->select('id', 'type', 'created_at')
            ->where('type', Order::CONFIRMED)
            ->whereYear('created_at', $runningYear);
        })
        ->count();

    if ($runningYearOrder == 0 && $previousYearOrder == 0) {
        $ratio = Order::Zero;
    } elseif ($runningYearOrder != 0) {
        if ($runningYearOrder >= $previousYearOrder) {
            $bigOrderCount = $runningYearOrder;
        } elseif ($runningYearOrder <= $previousYearOrder) {
            $bigOrderCount = $previousYearOrder;
        }
        $ratio = round((($runningYearOrder - $previousYearOrder) / $bigOrderCount) * 100, 2) . '%';
    } else {
        $ratio = Order::Negative;
    }

    return $ratio;
}

function getOrderByOrderStatus($value, $request)
{
    return  Order::query()
        ->select('id', 'shop_id')
        ->where('shop_id', $request->header('shop-id'))
        ->whereHas('status', function ($query) use ($value) {
            $query->select('id', 'type', 'created_at')
            ->where('type', Order::CONFIRMED)
            ->whereDate('created_at', $value);
        })
        ->count();
}
