<?php

namespace App\Http\Controllers\API\V1\Client\SalesTarget;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesTargetRequest;
use App\Models\Order;
use App\Models\SalesTarget;
use App\Traits\sendApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SalesTargetController extends Controller
{
    use sendApiResponse;

    public function sales_target(Request $request): JsonResponse
    {
        $amounts = [
            'daily_total'   => 0,
            'monthly_total' => 0,
            'custom_total'  => 0,
        ];
        $salesTarget = SalesTarget::query()->where('shop_id', $request->header('shop-id'))->first();

        if (!$salesTarget) {
            return $this->sendApiResponse('', 'Sales target not available right now', 'NotAvailable');
        }

        Order::with('pricing')
            ->where('shop_id', $request->header('shop-id'))
            ->whereHas('status', function ($query) {
                return $query->where('type', Order::CONFIRMED)->whereDate('created_at', Carbon::today()->toDateString());
            })->each(function ($q) use (&$amounts) {
                $total = discountedTotal($q->pricing);
                $amounts['daily_total'] += $total;
            });

        Order::with('pricing')
            ->where('shop_id', $request->header('shop-id'))
            ->whereHas('status', function ($q) {
                return $q->where('type', Order::CONFIRMED)->whereMonth('created_at', Carbon::today()->month);
            })->each(function ($q) use (&$amounts) {
                $total = discountedTotal($q->pricing);
                $amounts['monthly_total'] += $total;
            });
        Order::with('pricing')
            ->where('shop_id', $request->header('shop-id'))
            ->whereHas('status', function ($query) use ($salesTarget) {
                return $query->where('type', Order::CONFIRMED)->whereBetween('created_at', [$salesTarget->from_date, $salesTarget->to_date]);
            })->each(function ($q) use (&$amounts) {
                $total = discountedTotal($q->pricing);
                $amounts['custom_total'] += $total;
            });

        $salesTarget['daily_completed'] = targetCalculate($amounts['daily_total'], $salesTarget->daily);
        $salesTarget['monthly_completed'] = targetCalculate($amounts['monthly_total'], $salesTarget->monthly);
        $salesTarget['custom_completed'] = targetCalculate($amounts['custom_total'], $salesTarget->custom);
        $salesTarget['amounts'] = $amounts;

        return $this->sendApiResponse($salesTarget);
    }

    public function sales_target_update(SalesTargetRequest $request): JsonResponse
    {
        $salesTarget = SalesTarget::query()->updateOrCreate([
            'user_id' => $request->header('id'),
            'shop_id' => $request->header('shop-id')
        ], [
            'daily'     => $request->input('daily') ?? 0,
            'monthly'   => $request->input('monthly') ?? 0,
            'custom'    => $request->input('custom') ?? 0,
            'from_date' => $request->input('from_date'),
            'to_date'   => $request->input('to_date'),
        ]);

        return $this->sendApiResponse($salesTarget, 'Sales target updated successfully');
    }
}
