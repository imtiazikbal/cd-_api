<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    protected $key = 'payment';

    private function config()
    {
        return config("services.{$this->key}.production");
    }

    private function request(): PendingRequest
    {
        return Http::withHeaders(['X-Requested-With' => 'XMLHttpRequest'])->asJson();
    }

    public function makePayment($request, $data)
    {
        $response = $this->request()->withHeaders(['id' => $request->header('id')])->get($this->config(), [
            'amount'     => $request->input('amount'),
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'addons_id'  => $request->input('addons_id'),
            'order_type' => $request->input('order_type')
        ]);

        return $response->getBody()->getContents();
    }
}
