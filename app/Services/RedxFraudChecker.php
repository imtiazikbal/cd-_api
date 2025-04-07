<?php

namespace App\Services;

use App\Models\AdminCourier;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// class RedxFraudChecker
// {
//     public const DOMAIN = 'https://redx.com.bd';
//     public const API_DOMAIN = 'https://api.redx.com.bd';

//     private $headers = [
//         'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="99", "Google Chrome";v="99"',
//         'sec-ch-ua-mobile' => '?0',
//         'sec-ch-ua-platform' => '"Windows"',
//         'Upgrade-Insecure-Requests' => '1',
//         'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.51 Safari/537.36',
//         'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
//         'Sec-Fetch-Site' => 'none',
//         'Sec-Fetch-Mode' => 'navigate',
//         'Sec-Fetch-User' => '?1',
//         'Sec-Fetch-Dest' => 'document',
//         'Accept-Language' => 'en-US,en;q=0.9'
//     ];

//     public function make_config(string $number, string $password): object
//     {
//         $result = (object)['status' => 'error', 'message' => 'Unknown error'];

//         if (!$this->validate_number($number)) {
//             $result->message = 'Invalid mobile number provided';
//             return $result;
//         }

//         $login_url = self::API_DOMAIN . '/v4/auth/login';
//         $this->headers['referer'] = self::DOMAIN . '/';

//         $number = $this->format_number($number);
//         $fields = ['phone' => $number, 'password' => $password];
//         $response = null;

//         try {
//             $response = $this->request()->asJson()
//                 ->post($login_url, $fields);
//         } catch (\Exception $e) {
//             $errorMessage = $e->getMessage();
//             if (app()->environment() == 'local') {
//                 Log::error($errorMessage);
//             }
//             $result->message = $errorMessage;
//             return $result;
//         }

//         $data = $response?->object();

//         if (isset($data->error)) {
//             $result->message = $data->error->message;
//             return $result;
//         } elseif (!isset($data->data) && !isset($data->data->accessToken)) {
//             $result->message = 'Unknown data returned';
//             return $result;
//         }

//         $result->status = 'success';
//         $result->message = 'Successfully logged in';
//         $result->config = (object)[
//             'access_token' => $data->data->accessToken,
//         ];
//         return $result;
//     }

//     public function check(string $number): object
//     {
//         $result = (object)['status' => 'error', 'message' => 'Unknown error'];
//         $fraud_info = (object)[
//             'total_orders' => 0,
//             'delivered_orders' => 0,
//             'cancelled_orders' => 0,
//             'cancel_percent' => 0,
//             'success_percent' => 100,
//             'fraud_count' => 0,
//             'details' => [],
//             'courier' => 'redx',
//         ];

//         if (!$adminCouriers = Cache::get('admin_couriers')) {
//             $adminCouriers = AdminCourier::query()->get();
//             Cache::put('admin_couriers', $adminCouriers);
//         }

//         $courier = $adminCouriers->where('courier', 'redx')->first();
//         $config = $courier->config ?? null;

//         $number = $this->format_number($number);

//         if ($config == null) {
//             $config_res = $this->make_config($courier->email, $courier->password);

//             if ($config_res->status == 'error') {
//                 $courier->update(['notice' => $config_res->message]);

//                 $adminCouriers = AdminCourier::query()->get();
//                 Cache::put('admin_couriers', $adminCouriers);

//                 $result->message = $config_res->message;
//                 $result->fraud_info = $fraud_info;
//                 return $result;
//             }

//             $courier->update(['config' => \json_encode($config_res->config), 'notice' => null]);

//             $adminCouriers = AdminCourier::query()->get();
//             Cache::put('admin_couriers', $adminCouriers);

//             $config = $config_res->config;
//         } elseif (!$this->validate_number($number)) {
//             $result->message = 'Invalid mobile number';
//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

//         $fraud_check_url = self::DOMAIN . '/api/redx_se/admin/parcel/customer-success-return-rate';

//         $this->headers['referer'] = self::DOMAIN . '/create-parcel/';
//         $this->headers['x-access-token'] = "Bearer {$config->access_token}";

//         $response = null;

//         try {
//             $response = $this->request()->acceptJson()
//                 ->get($fraud_check_url, [
//                     'phoneNumber' => $number
//                 ]);
//         } catch (\Exception $e) {
//             $errorMessage = $e->getMessage();
//             if (app()->environment('local')) {
//                 Log::error($errorMessage);
//             }
//             $result->message = $errorMessage;
//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

//         $json_data = $response->object() ?? null;

//         if (!$json_data || !isset($json_data->isError)) {
//             $result->message = 'Unknown data returned';
//             $result->fraud_info = $fraud_info;
//             return $result;
//         } elseif ($json_data->isError) {
//             $result->message = $json_data->message;
//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

//         $data = $json_data->data;
//         $total_orders = (int)$data->totalParcels;

//         if ($total_orders === 0) {
//             $result->status = 'success';
//             $result->message = 'Successfully fetched fraud info';
//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

//         $delivered_orders = (int)$data->deliveredParcels;
//         $cancelled_orders = $total_orders - $delivered_orders;

//         $cancel_percent = round(($cancelled_orders / $total_orders) * 100, 1);
//         $success_percent = round(($delivered_orders / $total_orders) * 100, 1);

//         $fraud_info->delivered_orders = $delivered_orders;
//         $fraud_info->cancelled_orders = $cancelled_orders;
//         $fraud_info->total_orders = $total_orders;
//         $fraud_info->fraud_count = $cancelled_orders;
//         $fraud_info->cancel_percent = $cancel_percent;
//         $fraud_info->success_percent = $success_percent;

//         $result->status = 'success';
//         $result->message = 'Successfully fetched fraud info';
//         $result->fraud_info = $fraud_info;
//         return $result;
//     }

//     private function request(): PendingRequest
//     {
//         return Http::withHeaders($this->headers)->acceptJson();
//     }

//     /**
//      * Number validation
//      * @param string $number
//      * @return bool
//      * @version 1.0
//      * @since 1.0
//      */
//     private function validate_number(string $number): bool
//     {
//         $number = ltrim($number, '88');
//         $pattern = '/^(?=\d{11}$)(01)\d+/';
//         return (bool) preg_match($pattern, $number);
//     }

//     /**
//      * Format Number
//      * @param string $number
//      * @return string $number
//      * @version 1.0
//      * @since 1.0
//      */
//     private function format_number(string $number): string
//     {
//         $number = preg_replace('/[^\d+]/', '', $number);
//         $number = ltrim($number, '88');
//         return '880' . ltrim($number, '0');
//     }
// }
