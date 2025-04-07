<?php

namespace App\Services;

use App\Models\AdminCourier;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// class PathaoFraudChecker
// {
//     public const DOMAIN = 'https://merchant.pathao.com';

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
//         'Accept-Language' => 'en-US,en;q=0.9',
//         'x-requested-with' => 'XMLHttpRequest',
//     ];

//     public function make_config(string $email, string $password): object
//     {
//         $result = (object)['status' => 'error', 'message' => 'Unknown error'];

//         if (!$this->validate_email($email)) {
//             $result->message = 'Invalid email address provided';
//             return $result;
//         }

//         $login_url = self::DOMAIN . '/api/v1/login';
//         $this->headers['referer'] = self::DOMAIN . '/';
//         $fields = ['username' => $email, 'password' => $password];
//         $response = null;

//         try {
//             $response = $this->request()->post($login_url, $fields);
//         } catch (\Exception $e) {
//             $errorMessage = $e->getMessage();
//             if (app()->environment() == 'local') {
//                 Log::error($errorMessage);
//             }
//             $result->message = $errorMessage;
//             return $result;
//         }

//         $data = $response?->object();

//         if (isset($data->type) && $data->type == 'error') {
//             $result->message = $data->message;
//             return $result;
//         } elseif (!isset($data->type) && !isset($data->access_token)) {
//             $result->message = 'Unknown data returned';
//             return $result;
//         }

//         $result->status = 'success';
//         $result->message = 'Successfully logged in';
//         $result->config = (object)[
//             'expires_in' => $data->expires_in,
//             'access_token' => $data->access_token,
//             'refresh_token' => $data->refresh_token,
//         ];

//         return $result;
//     }

//     public function check(string $number, bool $retry = true): object
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
//             'courier' => 'pathao',
//         ];

//         if (!$adminCouriers = Cache::get('admin_couriers')) {
//             $adminCouriers = AdminCourier::query()->get();
//             Cache::put('admin_couriers', $adminCouriers);
//         }

//         $courier = $adminCouriers->where('courier', 'pathao')->first();
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

//         $fraud_check_url = self::DOMAIN . '/api/v1/user/success';

//         $this->headers['referer'] = self::DOMAIN . '/courier/orders/create';
//         $this->headers['authorization'] = 'Bearer ' . $config->access_token;
//         $this->headers['x-requested-with'] = 'XMLHttpRequest';

//         $fields = ['phone' => $number];

//         $response = null;

//         try {
//             $response = $this->request()->acceptJson()
//                 ->asJson()->post($fraud_check_url, $fields);
//         } catch (\Exception $e) {
//             $errorMessage = $e->getMessage();
//             if (app()->environment('local')) {
//                 Log::error($errorMessage);
//             }
//             $result->message = $errorMessage;
//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

//         $require_login = $this->require_login($response->body(), $response->status());

//         if ($require_login === true) {
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

//             if ($retry === true) {
//                 return $this->check($number, false);
//             }

//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

//         $json_data = $response->object() ?? null;

//         if (!$json_data || !isset($json_data->type)) {
//             $result->message = 'Unknown data returned';
//             $result->fraud_info = $fraud_info;
//             return $result;
//         } elseif ($json_data->type == 'error') {
//             $result->message = $json_data->message;
//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

//         $data = $json_data->data;
//         $customer = $data->customer ?? null;

//         $total_orders = $customer->total_delivery ?? 0;
//         $delivered_orders = $customer->successful_delivery ?? 0;
//         $cancelled_orders = $data->fraud_count ?? 0;

//         if ($data->fraud_reason) {
//             $fraud_info->details = [
//                 (object)[
//                     'id' => $customer->customer_id ?? null,
//                     'phone' => $customer->customer_number ?? $number,
//                     'name' => 'N/A',
//                     'details' => $data->fraud_reason,
//                     'created_at' => null,
//                 ]
//             ];
//         }

//         if ($total_orders === 0) {
//             $result->status = 'success';
//             $result->message = 'Successfully fetched fraud info';
//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

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
//         return Http::withHeaders($this->headers)
//             ->acceptJson()->asJson();
//     }

//     /**
//      * Require login
//      * @param string $content
//      * @param array $content
//      * @return bool
//      * @version 1.0
//      * @since 1.0
//      */
//     private function require_login(string $content, int $status_code): bool
//     {
//         if (strpos($content, 'Unauthorized') !== false) {
//             return true;
//         } elseif ($status_code === 401) {
//             return true;
//         }
//         return false;
//     }

//     /**
//      * Email validation
//      * @param string $email
//      * @return bool
//      * @version 1.0
//      * @since 1.0
//      */
//     private function validate_email(string $email): bool
//     {
//         $validator = Validator::make(['email' => $email], [
//             'email' => 'required|email',
//         ]);

//         return $validator->passes();
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
//         return '0' . ltrim($number, '0');
//     }
// }