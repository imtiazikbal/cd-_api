<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PathaoService
{
    private $key = 'pathao';

    public function request($credentials): PendingRequest
    {
        return Http::baseUrl(config("services.{$this->key}.production"))
            ->withBody(json_encode($credentials), 'application/json');
    }

    public function orderRequest(): PendingRequest
    {
        return Http::baseUrl(config("services.{$this->key}.production"))->asJson();
    }

    public function createOrder(array $credentials, $data, $shop, string $note, $request)
    {

        $quantity = $this->getQuantity($data);
        $access_token = $this->getAccessToken($credentials);

        $phone = $this->getValidPhone($data['phone']);

        $array = [
            'store_id'            => $credentials['store_id'],
            'merchant_order_id'   => $data['order_no'],
            'sender_name'         => $shop['name'],
            'sender_phone'        => $shop['phone'],
            'recipient_name'      => $data['customer_name'],
            'recipient_phone'     => $phone,
            'recipient_address'   => $data['address'],
            'recipient_city'      => $request->input('city_id'),
            'recipient_zone'      => $request->input('zone_id'),
            'recipient_area'      => $request->input('area_id'),
            'delivery_type'       => 48,
            'item_type'           => 2,
            'item_quantity'       => $quantity,
            'item_weight'         => 0.5,
            'amount_to_collect'   => $data['pricing']['due'],
            'special_instruction' => $note,
        ];

        $res = $this->orderRequest()
            ->withHeaders(['Authorization' => 'Bearer ' . $access_token['access_token']])
            ->post('aladdin/api/v1/orders', $array);

        return json_decode($res->body());
    }

    public function orderDetails(array $credentials, string $consignment_id)
    {
        $access_token = $this->getAccessToken($credentials);

        if (array_key_exists('access_token', $access_token)) {
            $res = $this->orderRequest()
                ->withHeaders(['Authorization' => 'Bearer ' . $access_token['access_token']])
                ->get('aladdin/api/v1/orders/' . $consignment_id);

            return json_decode($res->body());
        }
    }

    public function getQuantity($data): int
    {
        $total = 0;

        foreach ($data->order_details as $order_detail) {
            $total += $order_detail->product_qty;
        }

        return $total;
    }

    public function getValidPhone($phone)
    {
        if (Str::contains($phone, '+88')) {
            return $phone = Str::replace('+88', '', $phone);
        }

        return $phone;
    }

    public function getAccessToken(array $credentials): array
    {
        $res = $this->request($credentials)->post('aladdin/api/v1/issue-token')
            ->getBody()
            ->getContents();

        return collect(json_decode($res))->toArray();
    }

    public function getStore(array $credentials)
    {
        $access_token = $this->getAccessToken($credentials);
        $res = $this->request($credentials)
            ->withHeaders(['Authorization' => 'Bearer ' . $access_token['access_token']])
            ->get('/aladdin/api/v1/stores');

        return json_decode($res->getBody()->getContents());
    }

    public function getCity($credentials)
    {
        return Cache::rememberForever('city-list', function () use ($credentials) {
            $access_token = $this->getAccessToken($credentials);
            $res = $this->request($credentials)
                ->withHeaders(['Authorization' => 'Bearer ' . $access_token['access_token']])
                ->get('/aladdin/api/v1/countries/1/city-list');

            return json_decode($res->getBody()->getContents());
        });
    }

    public function getZone($credentials, $city_id)
    {
        return Cache::rememberForever('zone-list-' . $city_id, function () use ($credentials, $city_id) {
            $access_token = $this->getAccessToken($credentials);
            $res = $this->request($credentials)
                ->withHeaders(['Authorization' => 'Bearer ' . $access_token['access_token']])
                ->get('/aladdin/api/v1/cities/' . $city_id . '/zone-list');

            return json_decode($res->getBody()->getContents());
        });
    }

    public function getArea($credentials, $zone_id)
    {
        return Cache::rememberForever('area-list-' . $zone_id, function () use ($credentials, $zone_id) {
            $access_token = $this->getAccessToken($credentials);
            $res = $this->request($credentials)
                ->withHeaders(['Authorization' => 'Bearer ' . $access_token['access_token']])
                ->get('/aladdin/api/v1/zones/' . $zone_id . '/area-list');

            return json_decode($res->getBody()->getContents());
        });
    }
}