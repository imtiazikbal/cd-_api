<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class RedxCourier
{
    protected string $api_host;
    protected string $access_token;

    public function __construct()
    {
        $this->api_host= config('redx.sandbox') ? config('redx.sandbox_api_host'): config('redx.api_host');
        $this->access_token = config('redx.sandbox')  ? config('redx.sandbox_access_token'): '';
    }

    public function request(array $credentials)
    {
        $token = $this->access_token ? $this->access_token : $credentials['token'];
        return Http::baseUrl($this->api_host)
        ->withHeaders([
            'API-ACCESS-TOKEN' => 'Bearer '.$token,
            'Content-Type' => 'application/json',
        ])
        ->asJson();
    }

    public function createParcel(array $credentials, Order $data, $request, string $note)  
    { 
        $parcel_details_arr = [];
        foreach($data['order_details'] as $details){
            $parcel_details_arr[] = [
                "name" => $details->product->product_name,
                "category" => $details->product->category->name,
                "value" => $details->product->price,
                "product_code" => $details->product->product_code,
                "product_qty" => $details->product_qty,
            ];
        }
      
        $arrayData = [
            "customer_name" => $data['customer_name'],
            "customer_phone" => $data['phone'],
            "delivery_area" => $request->input('delivery_area'),
            "delivery_area_id" => intval($request->delivery_area_id),
            "pickup_store_id" => intval($request->pickup_store_id) ?? '',
            "customer_address" => $data['address'],
            "merchant_invoice_id" => "",
            "cash_collection_amount" => $data['pricing']['due'],
            "parcel_weight" => intval($request->input('parcel_weight')) ?? 0,
            "instruction" =>  $note,
            "value" => 0, // compensation amount
            "is_closed_box" => false,
            "parcel_details_json" => $parcel_details_arr
        ];
        
        $apiResponse = $this->request($credentials)->post('parcel', $arrayData)->getBody()->getContents();
        $arrayResponse = json_decode($apiResponse, true);
        return $arrayResponse;
    }

    public function redxGetAreaDiscrictWise(array $credentials, $district){
        $url = 'areas?district_name='.$district;
        $apiResponse = $this->request($credentials)->get($url)->getBody()->getContents();
        $arrayResponse = json_decode($apiResponse, true);
        
        return $arrayResponse;
    }
    
    public function getArea($credentials){
        $apiResponse = $this->request($credentials)->get('areas')->getBody()->getContents();
        $arrayResponse = json_decode($apiResponse, true);
        
        return $arrayResponse;
    }

    public function createPickupStore(array $credentials, $request){
        $requestData = $request->toArray();
        $apiResponse = $this->request($credentials)->post('pickup/store', $requestData)->getBody()->getContents();
        $arrayResponse = json_decode($apiResponse, true);
        
        return $arrayResponse;
    }

    public function orderDetails(array $credentials, $trackId)
    {
        $apiResponse = $this->request($credentials)->get('parcel/info/'.$trackId)->getBody()->getContents();
        $arrayResponse = json_decode($apiResponse, true);

        return $arrayResponse;
    }
}