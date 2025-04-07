<?php

namespace App\Services;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Courier
{
    public const statuses = [
        'in_review'                          => 'In Review',
        'pending'                            => 'Pending',
        'hold'                               => 'Hold',
        'cancelled'                          => 'Returned',
        'delivered'                          => 'Delivered',
        'cancelled_approval_pending'         => 'Cancel',
        'unknown_approval_pending'           => 'Need Approval',
        'partial_delivered_approval_pending' => 'Partial Delivered',
        'delivered_approval_pending'         => 'Delivered',
        'partial_delivered'                  => 'Partial Delivered',
        'unknown'                            => 'Unknown'
    ];

    public static function status($value): ?string
    {
        foreach (self::statuses as $key => $item) {
            if($key === $value) {
                return $key;
            }
        }

        return null;
    }

    public function request($credentials): \Illuminate\Http\Client\PendingRequest
    {
        $credentials['Content-Type'] = 'application/json';

        return Http::baseUrl('https://portal.packzy.com/api/v1/')
            ->withHeaders($credentials)
            ->asJson();
    }

    public function createOrder($credentials, $data, string $note = ''): PromiseInterface|Response
    {

        $invoiceNumber = 'FL-' . $data['order_no'];
        $array = [
            'invoice'           => $invoiceNumber,
            'recipient_name'    => $data['customer_name'],
            'recipient_phone'   => $data['phone'],
            'recipient_address' => $data['address'] ?: 'not found',
            'cod_amount'        => $data['pricing']['due'],
            'note'              => $note
        ];

        return $this->request($credentials)->post('create_order', $array);
    }

    public function trackOrder($credentials, $url)
    {
        return $this->request($credentials)->get($url);
    }

    public function checkBalance($credentials, $url)
    {
        return $this->request($credentials)->get($url);
    }
}