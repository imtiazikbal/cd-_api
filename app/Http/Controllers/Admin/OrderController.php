<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orderFilter(Request $request)
    {
        $type = $request->filterType;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        
        $orders = Order::query()
        ->when($type === 'Today', function($q){
            $q->whereDate('created_at', Carbon::today());
        })
        ->when($type === 'Yesterday', function($q){
            $q->whereDate('created_at', Carbon::yesterday());
        })
        ->when($type === 'Custom' && $startDate && $endDate , function($q) use ($startDate, $endDate){
            if($startDate !== $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }else {
                $q->whereDate('created_at', $startDate);
            }
        })
        ->withTrashed()
        ->count();

        return response()->json([
            'orders'    => $orders
        ]);
    }
}