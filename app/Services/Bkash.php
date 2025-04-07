<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Bkash
{
    private $key = 'bkash';

    private $type = 'production';

    private $username;

    private $url;

    private $password;

    private $app_key;

    private $app_secret;

    public function __construct()
    {
        $this->url = config("services.{$this->key}.{$this->type}_url");
        $this->username = config("services.{$this->key}.{$this->type}_user");
        $this->password = config("services.{$this->key}.{$this->type}_pass");
        $this->app_key = config("services.{$this->key}.{$this->type}_app_key");
        $this->app_secret = config("services.{$this->key}.{$this->type}_app_secret");
    }

    public function request(): PendingRequest
    {
        return Http::withHeaders(
            [
                'username'         => $this->username,
                'password'         => $this->password,
                'X-Requested-With' => 'XMLHttpRequest',
                'accept'           => 'application/json',
                'content-type'     => 'application/json'
            ]
        )->asJson();
    }

    public function getToken(): string
    {
        $data = $this->request()
            ->post(
                $this->url . 'token/grant',
                [
                    'app_key' => $this->app_key, 'app_secret' => $this->app_secret
                ]
            );

        return $data->getBody()->getContents();
    }

    public function initPayment(array $data): string
    {
        $token = json_decode($this->getToken());
        $data['authToken'] = $token->id_token;
        $pay = $this->request()
            ->withHeaders(
                [
                    'Authorization' => $token->id_token,
                    'X-App-Key'     => $this->app_key
                ]
            )
            ->post($this->url . 'create', [
                'amount'                => $data['amount'],
                'currency'              => 'BDT',
                'intent'                => 'sale',
                'merchantInvoiceNumber' => 'FL-' . random_int(1111, 9999),
                "callbackURL"           => route('payment.bkash.callback', ['data' => $data]),
                "payerReference"        => array_key_exists('name', $data) ? $data['name'] : $data['user_id'],
                "mode"                  => "0011"
            ]);

        return data_get(json_decode($pay->getBody()->getContents()), 'bkashURL');
    }

    public function executePayment(string $paymentID, string $token)
    {
        $pay = $this->request()
            ->withHeaders(
                [
                    'Authorization' => $token,
                    'X-App-Key'     => $this->app_key
                ]
            )
            ->post($this->url . 'execute', [
                'paymentID' => $paymentID
            ]);

        return $pay->getBody()->getContents();
    }
}
