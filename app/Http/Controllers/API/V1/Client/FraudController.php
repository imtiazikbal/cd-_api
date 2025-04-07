<?php

namespace App\Http\Controllers\API\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\FraudNoteResource;
use App\Http\Resources\FraudReportResource;
use App\Http\Resources\FraudResource;
use App\Models\Fraud;
use App\Models\FraudNote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FraudController extends Controller
{
    public function index(string $number)
    {
        $cache_key = "fraud_by_{$number}";

        if (Cache::has($cache_key)) {

                $report = json_decode(Cache::get($cache_key));

            $frauds_report = (object)[
                'order_placed' => $report->order_placed,
                'order_delivered' => $report->order_delivered,
                'order_returned' => $report->order_returned,
                'fraud_report' => $report->fraud_report,
                'fraud_processing' => $report->fraud_processing,
            ];
        } else {
            $frauds_report = Fraud::query()
            ->select(
                DB::raw('SUM(frauds.orders) as order_placed'),
                DB::raw('SUM(frauds.delivered) as order_delivered'),
                DB::raw('SUM(frauds.cancelled) as order_returned'),
                DB::raw('COUNT(fraud_notes.id) as fraud_report'),
            )
            ->leftJoin('fraud_notes', 'frauds.id', '=', 'fraud_notes.fraud_id')
            ->where('number', $number)->first();
        }

        $cache_f_couriers_key = "f_couriers_by_{$number}";

        if (Cache::has($cache_f_couriers_key)) {

            $frauds = Cache::get($cache_f_couriers_key);
        } else {
            $frauds = Fraud::query()
                ->select(
                    'frauds.id',
                    'frauds.number',
                    'frauds.courier',
                    'frauds.orders',
                    'frauds.delivered',
                    'frauds.cancelled',
                    DB::raw('COUNT(fraud_notes.id) as fraud_report')
                )
                ->leftJoin('fraud_notes', 'frauds.id', '=', 'fraud_notes.fraud_id')
                ->where('number', $number)
                ->groupBy('frauds.id', 'frauds.number', 'frauds.courier', 'frauds.orders', 'frauds.delivered', 'frauds.cancelled')
                ->get();

            $resources = FraudResource::collection($frauds);
            Cache::put($cache_f_couriers_key, $resources);
        }

        if ($frauds_report == null || $frauds_report->order_placed == null) {
            $frauds_report = (object) [
                'order_placed' => 0,
                'order_delivered' => 0,
                'order_returned' => 0,
                'fraud_report' => 0,
                'fraud_processing' => true,
            ];
        } else {
            $frauds_report->fraud_processing = false;
        }

        $frauds_report->frauds = $frauds;
        return $this->sendApiResponse(
            new FraudReportResource($frauds_report),
            'Fraud verification info fetched'
        );
    }

    public function note(Fraud $fraud)
    {
        $fraud_notes = FraudNote::query()
            ->where('fraud_id', $fraud->id)
            ->get();

        return $this->sendApiResponse(
            FraudNoteResource::collection($fraud_notes),
            'Fraud notes fetched'
        );
    }
}