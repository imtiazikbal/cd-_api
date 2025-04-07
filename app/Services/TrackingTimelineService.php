<?php

namespace App\Services;

use App\Models\MerchantCourier;
use App\Services\PathaoFraudChecker;
use App\Services\RedxFraudChecker;
use App\Services\SteadfastFraudChecker;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrackingTimelineService
{
    private $headers = [
        'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="99", "Google Chrome";v="99"',
        'sec-ch-ua-mobile' => '?0',
        'sec-ch-ua-platform' => '"Windows"',
        'Upgrade-Insecure-Requests' => '1',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.51 Safari/537.36',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'Sec-Fetch-Site' => 'none',
        'Sec-Fetch-Mode' => 'navigate',
        'Sec-Fetch-User' => '?1',
        'Sec-Fetch-Dest' => 'document',
        'Accept-Language' => 'en-US,en;q=0.9'
    ];

    private $courier = null;
    private $allowed_couriers = [
        MerchantCourier::PATHAO,
        MerchantCourier::REDX,
        MerchantCourier::STEADFAST,
    ];


    public function __construct(string $courier)
    {
        $this->courier = $courier;
    }

    public function timeline(string $tracking_code, string $number = null): object
    {
        $result = (object)['status' => 'error', 'message' => 'Unknown error'];

        if (!\in_array($this->courier, $this->allowed_couriers)) {
            $result->message = 'Your target courier not implemented yet';
            return $result;
        } elseif ($this->courier == MerchantCourier::PATHAO) {
            $result = $this->pathao($tracking_code, $number);

            if ($result->status == 'error') {
                return $result;
            }

            $timeline = $result->timeline;
            $rider_info = [];
            $pattern = '/assigned to \((.+)\) for delivery/i';

            foreach ($timeline as $key => $line) {
                $created_at = Carbon::createFromFormat('M j, Y g:i A', $line->created_at);
                $formatted_date = $created_at->utc()->format('Y-m-d\TH:i:s.u\Z');
                $timeline[$key] = [
                    'note' => $line->desc,
                    'created_at' => $formatted_date,
                ];

                if (preg_match($pattern, $line->desc, $matches)) {
                    $info = $matches[1];

                    if (\strpos($info, '(') !== false) {
                        $parts = \explode('(', $info);
                    } else {
                        $parts = \explode('-', $info);
                    }

                    $rider_info = [
                        'rider_name' => isset($parts[0]) ? $parts[0] : '',
                        'rider_number' => isset($parts[1]) && \is_numeric($parts[1]) ? $parts[1] : '',
                    ];
                }
            }

            $result->timeline = $timeline;
            $result->rider_info = $rider_info;
        } elseif ($this->courier == MerchantCourier::REDX) {
            $result = $this->redx($tracking_code, $number);

            if ($result->status == 'error') {
                return $result;
            }

            $timeline = $result->timeline;
            $rider_info = [];
            $pattern = '/delivery by (.+)\((\d+)\)/i';

            foreach ($timeline as $key => $line) {
                $timeline[$key] = [
                    'note' => $line->messageBn,
                    'created_at' => $line->time,
                ];

                if (preg_match($pattern, $line->messageEn, $m)) {
                    $rider_info = [
                        'rider_name' => isset($m[1]) ? $m[1] : '',
                        'rider_number' => isset($m[2]) ? $m[2] : '',
                    ];
                }
            }

            $result->timeline = $timeline;
            $result->rider_info = $rider_info;
        } elseif ($this->courier == MerchantCourier::STEADFAST) {
            $result = $this->steadfast($tracking_code);

            if ($result->status == 'error') {
                return $result;
            }

            $timeline = $result->timeline;

            foreach ($timeline as $key => $line) {
                $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $line->created_at);
                $formatted_date = $created_at->utc()->format('Y-m-d\TH:i:s.u\Z');
                $timeline[$key] = [
                    'note' => $line->text,
                    'created_at' => $formatted_date,
                ];
            }

            if ($result->rider_info ?? null) {
                $phone = $result->rider_info->phone;
                $result->rider_info = [
                    'rider_name' => $result->rider_info->name,
                    'rider_number' => $phone == 0 ? null : $phone,
                ];
            } else {
                $result->rider_info = [];
            }

            $result->timeline = \array_reverse($timeline);
        }

        return $result;
    }

    private function pathao(string $tracking_code, string $number = null): object
    {
        $result = (object)['status' => 'error', 'message' => 'Unknown error'];

        $referer = "https://merchant.pathao.com/tracking?consignment_id={$tracking_code}";
        $tracking_url = 'https://merchant.pathao.com/api/v1/user/tracking';
        $fields = ['consignment_id' => $tracking_code];

        if ($number !== null) {
            $referer .= "&phone={$number}";
            $fields['phone_no'] = $number;
        }

        $this->headers['referer'] = $referer;
        $response = null;

        try {
            $response = $this->request()->post($tracking_url, $fields);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if (app()->environment('local')) {
                Log::error($errorMessage);
            }
            $result->message = $errorMessage;
            return $result;
        }

        $data = $response?->object();

        if (isset($data->type) && $data->type == 'error') {
            $result->message = $data->message;
            return $result;
        } elseif (!isset($data->type) && !isset($data->data->log)) {
            $result->message = 'Unknown data returned';
            return $result;
        }

        $result->status = 'success';
        $result->message = 'Successfully order tracking timeline fetched';
        $result->timeline = $data->data->log;

        return $result;
    }

    private function redx(string $tracking_code): object
    {
        $result = (object)['status' => 'error', 'message' => 'Unknown error'];

        $tracking_url = "https://api.redx.com.bd/v1/logistics/global-tracking/{$tracking_code}";
        $this->headers['referer'] = 'https://redx.com.bd/';
        $response = null;

        try {
            $response = $this->request()->get($tracking_url);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if (app()->environment('local')) {
                Log::error($errorMessage);
            }
            $result->message = $errorMessage;
            return $result;
        }

        $data = $response?->object();

        if (isset($data->isError) && $data->isError) {
            $result->message = $data->message;
            return $result;
        } elseif (!isset($data->tracking)) {
            $result->message = 'Unknown data returned';
            return $result;
        }

        $result->status = 'success';
        $result->message = 'Successfully order tracking timeline fetched';
        $result->timeline = $data->tracking;

        return $result;
    }

    private function steadfast(string $tracking_code): object
    {
        $result = (object)['status' => 'error', 'message' => 'Unknown error'];

        $referer = "https://steadfast.com.bd/t/{$tracking_code}";
        $tracking_url = "https://steadfast.com.bd/track/consignment/{$tracking_code}";

        $this->headers['referer'] = $referer;
        $this->headers['x-requested-with'] = 'XMLHttpRequest';
        $response = null;

        try {
            $response = $this->request()->get($tracking_url);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if (app()->environment('local')) {
                Log::error($errorMessage);
            }
            $result->message = $errorMessage;
            return $result;
        }

        $data = $response?->object();

        if (!$data || !isset($data[0], $data[1])) {
            $result->message = 'Unknown data returned';
            return $result;
        }

        $result->status = 'success';
        $result->message = 'Successfully order tracking timeline fetched';
        $result->rider_info = $data[0]->rider ?? null;
        $result->timeline = $data[1];

        return $result;
    }

    private function request(): PendingRequest
    {
        return Http::withHeaders($this->headers)->acceptJson();
    }
}
