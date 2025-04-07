<?php

namespace App\Services;

use App\Models\Fraud;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FraudCheckerService
{
    private $fraud_check_host_url;
    private $fraud_check_access_token;
    private $serverType;

    public function __construct()
    {
        // checking it's staging or production URL
        $serverStatus = Fraud::SERVERTYPE;
        $this->serverType = (preg_match("/{$serverStatus}/i", env('APP_URL')) == true) ? "staging" : "production";
        
        $this->fraud_check_host_url = env('FRAUD_CHECK_URL');
        $this->fraud_check_access_token = env('FRAUD_CHECK_ACCESS_TOKEN');
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl($this->fraud_check_host_url)
        ->withHeaders([
            'X-ACCESS-TOKEN' => $this->fraud_check_access_token,
        ])
        ->asJson();
    }

    public function fraudCheck(string $number)
    {
        $data = [
                'phone' => $number,
                'serverType' => $this->serverType
            ];

        $this->request()->post('api/fraud-check', $data)->getBody();
        Log::info('Fraud checking start');
    }

    // public function check(string $number)
    // {
    //     $data = ['phone' => $number];
    //     $this->request()->post('api/fraud-check', $data)->getBody();
    //     Log::info('Fraud checking start');

    //     $result = (object)['status' => 'error', 'message' => 'Unknown error'];
    //     $frauds = [];

    //     try {
    //         $checker = new PathaoFraudChecker();
    //         $fraud = $checker->check($number);
    //         $frauds[] = $fraud->fraud_info;

    //         $checker = new RedxFraudChecker();
    //         $fraud = $checker->check($number);
    //         $frauds[] = $fraud->fraud_info;

    //         $checker = new SteadfastFraudChecker();
    //         $fraud = $checker->check($number);
    //         $fraud_info = $fraud->fraud_info;

    //         $checker = new SteadfastCourierFraudChecker();
    //         $fraud = $checker->check($number);

    //         $fraud_info->total_orders += $fraud->fraud_info->total_orders;
    //         $fraud_info->delivered_orders += $fraud->fraud_info->delivered_orders;
    //         $fraud_info->cancelled_orders += $fraud->fraud_info->cancelled_orders;
    //         $fraud_info->cancel_percent += $fraud->fraud_info->cancel_percent;
    //         $fraud_info->success_percent += $fraud->fraud_info->success_percent;
    //         $fraud_info->fraud_count += $fraud->fraud_info->fraud_count;
    //         $fraud_info->details = $fraud_info->details + $fraud->fraud_info->details;
    //         $fraud_info->success_percent = \floor($fraud_info->success_percent / 2);

    //         $frauds[] = $fraud_info;
    //     } catch (\Throwable $th) {
    //         if (app()->environment('local')) {
    //             Log::error($th->getMessage());
    //         }

    //         $result->message = 'Internal server error';
    //         $result->frauds = $frauds;
    //         return $result;
    //     }

    //     $result->status = 'success';
    //     $result->message = 'Successfully fetched frauds info';
    //     $result->frauds = $frauds;
    //     return $result;
    // }

    // public function cache(string $number)
    // {
    //     $frauds_report = Fraud::query()
    //         ->select(
    //             DB::raw('SUM(frauds.orders) as fraud_entry'),
    //             DB::raw('SUM(frauds.delivered) as fraud_delivery'),
    //             DB::raw('SUM(frauds.cancelled) as fraud_return'),
    //             DB::raw('COUNT(fraud_notes.id) as fraud_report'),
    //         )
    //         ->leftJoin('fraud_notes', 'frauds.id', '=', 'fraud_notes.fraud_id')
    //         ->where('number', $number)->first();

    //     if ($frauds_report == null || $frauds_report->fraud_entry == null) {
    //         $frauds_report = (object) [
    //             'fraud_entry' => 0,
    //             'fraud_delivery' => 0,
    //             'fraud_return' => 0,
    //             'fraud_report' => 0,
    //             'fraud_processing' => true,
    //         ];
    //     } else {
    //         $frauds_report->fraud_processing = false;
    //     }

    //     $cache_key = "fraud_by_{$number}";
    //     Cache::put($cache_key, $frauds_report);

    //     $frauds = Fraud::query()
    //     ->select(
    //         'frauds.id',
    //         'frauds.courier',
    //         'frauds.orders',
    //         'frauds.delivered',
    //         'frauds.cancelled',
    //         DB::raw('COUNT(fraud_notes.id) as fraud_report')
    //     )
    //     ->leftJoin('fraud_notes', 'frauds.id', '=', 'fraud_notes.fraud_id')
    //     ->where('number', $number)
    //     ->groupBy('frauds.id', 'frauds.courier', 'frauds.orders', 'frauds.delivered', 'frauds.cancelled')
    //     ->get();

    //     $resources = FraudResource::collection($frauds);
    //     $cache_f_couriers_key = "f_couriers_by_{$number}";
    //     Cache::put($cache_f_couriers_key, $resources);

    //     return $frauds_report;
    // }
}