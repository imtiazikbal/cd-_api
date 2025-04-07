<?php

namespace App\Services;

use App\Models\AdminCourier;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// class SteadfastCourierFraudChecker
// {
//     public const DOMAIN = 'https://steadfastcourier.com';

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

//     public function make_config(string $email, string $password): object
//     {
//         $result = (object)['status' => 'error', 'message' => 'Unknown error'];

//         if (!$this->validate_email($email)) {
//             $result->message = 'Invalid email address provided';
//             return $result;
//         }

//         unset($this->headers['cookie']);
//         unset($this->headers['x-csrf-token']);
//         unset($this->headers['x-xsrf-token']);

//         $login_url = self::DOMAIN . '/login';
//         $this->headers['referer'] = self::DOMAIN . '/';
//         $response = null;

//         try {
//             $response = $this->request()->get($login_url);
//         } catch (\Exception $e) {
//             $errorMessage = $e->getMessage();
//             if (app()->environment() == 'local') {
//                 Log::error($errorMessage);
//             }
//             $result->message = $errorMessage;
//             return $result;
//         }

//         $content = $response?->body() ?? '';
//         $doc = $this->docHTML($content);
//         $xpath = new \DOMXPath($doc);
//         $nodes = $xpath->query('//input[@name="_token"]');

//         if (!$nodes || !$nodes->length) {
//             $result->message = 'Unable to parse login form.';
//             return $result;
//         }

//         $_token = $nodes->item(0)->getAttribute('value');

//         $fields = [
//             '_token' => $_token,
//             'email' => $email,
//             'password' => $password,
//             'remember' => 'on'
//         ];

//         $cookies = $response->cookies();
//         $this->headers['referer'] = $login_url;
//         $response = null;

//         try {
//             $response = $this->request()->asForm()
//                 ->withOptions(['cookies' => $cookies])
//                 ->post($login_url, $fields);
//         } catch (\Exception $e) {
//             $errorMessage = $e->getMessage();
//             if (app()->environment() == 'local') {
//                 Log::error($errorMessage);
//             }
//             $result->message = $errorMessage;
//             return $result;
//         }

//         $content = $response?->body() ?? '';
//         $doc = $this->docHTML($content);
//         $xpath = new \DOMXPath($doc);
//         $nodes = $xpath->query('//div[contains(@class, "invalid-alert")]');

//         if ($nodes->length) {
//             $result->message = trim($nodes->item(0)->textContent);
//             return $result;
//         }

//         $effective_url = (string) $response->effectiveUri() ?? '';

//         if (strpos($effective_url, '/login') !== false) {
//             return $result;
//         }

//         $cookie_str = $xsrf_token = $csrf_token = '';

//         foreach ($cookies as $cookie) {
//             $name = $cookie->getName();
//             $value = $cookie->getValue();

//             if (!empty($cookie_str)) {
//                 $cookie_str .= '; ';
//             }

//             $cookie_str .= "{$name}={$value}";

//             if ($name == 'XSRF-TOKEN') {
//                 $xsrf_token = rawurldecode($value);
//             }
//         }

//         $content = $response?->body() ?? '';
//         $doc = $this->docHTML($content);
//         $xpath = new \DOMXPath($doc);
//         $nodes = $xpath->query('//meta[@name="csrf-token"]');

//         if (!$nodes || !$nodes->length) {
//             $result->message = 'Unable to fetch dashboard page';
//             return $result;
//         }

//         $csrf_token = $nodes->item(0)->getAttribute('content');

//         $result->status = 'success';
//         $result->message = 'Successfully logged in';
//         $result->config = (object)[
//             'cookie_str' => $cookie_str,
//             'csrf_token' => $csrf_token,
//             'xsrf_token' => $xsrf_token,
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
//             'courier' => 'steadfast',
//         ];

//         if (!$adminCouriers = Cache::get('admin_couriers')) {
//             $adminCouriers = AdminCourier::query()->get();
//             Cache::put('admin_couriers', $adminCouriers);
//         }

//         $courier = $adminCouriers->where('courier', 'steadfastcourier')->first();
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

//         $fraud_check_url = self::DOMAIN . '/user/fraud-check/' . $number;

//         $this->headers['referer'] = self::DOMAIN . '/user/fraud-check';
//         $this->headers['cookie'] = $config->cookie_str;
//         $this->headers['x-csrf-token'] = $config->csrf_token;
//         $this->headers['x-requested-with'] = 'XMLHttpRequest';
//         $this->headers['x-xsrf-token'] = $config->xsrf_token;

//         $response = null;

//         try {
//             $response = $this->request()->acceptJson()
//                 ->get($fraud_check_url);
//         } catch (\Exception $e) {
//             $errorMessage = $e->getMessage();
//             if (app()->environment('local')) {
//                 Log::error($errorMessage);
//             }
//             $result->message = $errorMessage;
//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

//         $effective_url = $response->effectiveUri();

//         if ($effective_url == self::DOMAIN . '/login') {
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

//         $data = $response->object() ?? null;

//         if (!$data || !isset($data[0])) {
//             $result->message = 'Unknown data returned';
//             $result->fraud_info = $fraud_info;
//             return $result;
//         }

//         $delivered_orders = (int)$data[0];
//         $cancelled_orders = (int)$data[1];
//         $total_orders = $delivered_orders + $cancelled_orders;
//         $fraud_info->details = $data[2];

//         foreach ($fraud_info->details as $key => $detail) {
//             $fraud_info->details[$key] = (object)[
//                 'id' => $detail->id,
//                 'phone' => $detail->phone,
//                 'name' => $detail->name,
//                 'details' => $detail->details,
//                 'created_at' => $detail->created_at,
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
//         return Http::withHeaders($this->headers);
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
//         return '0' . ltrim(ltrim($number, '88'), '0');
//     }

//     /**
//      * HTML content to DOMDocument
//      * @param string $contents
//      * @return object DOMDocument
//      * @version 1.0
//      * @since 1.0
//      */
//     private function docHTML(string $content = '')
//     {
//         if (!$content) {
//             $content = '<!DOCTYPE html>
// 			<html lang="en" dir="ltr">
// 			<head>
// 			<meta charset="utf-8">
// 			<title>ERROR</title>
// 			</head>
// 			<body>
// 			</body>
// 			</html>';
//         } else {
//             $content = '<meta charset="utf-8">' . $content;
//         }
//         $doc = new \DOMDocument();
//         @$doc->loadHTML($content, LIBXML_NOERROR);
//         return $doc;
//     }
// }