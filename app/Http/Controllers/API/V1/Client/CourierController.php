<?php

namespace App\Http\Controllers\API\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourierProviderRequest;
use App\Http\Requests\CourierSendRequest;
use App\Http\Requests\RedxPickupStoreCreateRequest;
use App\Http\Resources\MerchantOrderResource;
use App\Models\MerchantCourier;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderTrackingTimeline;
use App\Models\Shop;
use App\Services\Courier;
use App\Services\PathaoService;
use App\Services\RedxCourier;
use App\Helpers\OrderHelper;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Class CourierController
 * @package App\Http\Controllers\API\V1\Client
 * @property PathaoService $pathaoService
 */
class CourierController extends Controller
{
    use sendApiResponse;

    protected $pathaoService;

    public function __construct(PathaoService $pathaoService)
    {
        $this->pathaoService = $pathaoService;
    }

    public function index(Request $request): JsonResponse
    {
        $couriers = MerchantCourier::query()
            ->where('shop_id', $request->header('shop-id'))
            ->get();

        return $this->sendApiResponse($couriers);
    }

    public function store(CourierProviderRequest $request): JsonResponse
    {
        $courier = MerchantCourier::query()->updateOrCreate([
            'shop_id'  => $request->header('shop-id'),
            'provider' => $request->input('provider'),
        ], [
            'status' => $request->input('status'),
            'config' => $request->input('config'),
        ]);

        return $this->sendApiResponse($courier, 'Provider Updated Successfully');
    }

    public function sendOrderToCourier(CourierSendRequest $request): JsonResponse
    {
        $orderIds = $request->input('order_id');

        if (!is_array($orderIds)) {
            $orderIds = [$orderIds];
        }

        $courier = $this->getMerchantCourier($request->header('shop-id'), $request->input('provider'), true);

        if (!$courier) {
            throw ValidationException::withMessages([
                'notfound' => 'Invalid provider or merchant',
            ]);
        }
        
        $credentials = collect(json_decode($courier->config))->toArray();
        $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();
        
        $results = [];
        $orders = Order::with('pricing')->whereIn('id', $orderIds)->get();

        foreach ($orders as $order) {

            if (!$order) {
                $results[] = [
                    'order_id'   => $order->id,
                    'error'      => 'Order not found',
                    'error_type' => 'not_found',
                ];

                continue;
            }

            $note = OrderHelper::getNote($order->id, 'courier') ?? '';

            if ($request->input('provider') === MerchantCourier::STEADFAST) {
                $provider = new Courier();

                try {
                    $response = $provider->createOrder($credentials, $order, $note)->json() ?? [];
                } catch (\Exception $e) {
                    $results[] = [
                        'order_id'   => $order->id,
                        'message'    => 'Exception occurred: ' . $e->getMessage(),
                        'error_type' => 'exception',
                    ];

                    continue;
                }

                if (array_key_exists('consignment', $response)) {
                    $order->update([
                        'order_status' => Order::SHIPPED
                    ]);

                    $order->courier()->update([
                        'tracking_code'  => $response['consignment']['tracking_code'] ?? null,
                        'consignment_id' => $response['consignment']['consignment_id'] ?? null,
                        'status'         => $response['consignment']['status'] ?? null,
                        'provider'       => MerchantCourier::STEADFAST,
                    ]);

                    $order->config()->update([
                        'courier_entry' => true
                    ]);

                    OrderStatus::query()->create([
                        'order_id' => $order->id,
                        'type'     => Order::SHIPPED
                    ]);

                    $results[] = [
                        'order_id' => $order->id,
                        'message'  => 'Order has been sent to ' . MerchantCourier::STEADFAST,
                    ];

                    $note = "Shipped = Your order has been sent to our delivery partner (Stedfast Courier)";

                    OrderTrackingTimeline::query()->create([
                        'order_id' => $order->id,
                        'event' => 'shipped',
                        'note' => $note,
                    ]);
                } elseif (isset($response['errors'])) {
                    foreach ($response['errors'] as $key => $error) {
                        $results[] = [
                            'order_id'   => $order->id,
                            'message'    => $error[0],
                            'error_type' => $key,
                        ];
                    }
                } else {
                    $results[] = [
                        'order_id'   => $order->id,
                        'message'    => 'Unexpected response format or null response',
                        'error_type' => 'unexpected_response',
                    ];
                }
            } elseif ($request->input('provider') === MerchantCourier::PATHAO) {
                return $this->sendToPathao($credentials, $order, $shop, $note, $request);
            }elseif($request->input('provider') === MerchantCourier::REDX){
                $results[] = $this->sendToRedx($credentials, $order, $request, $note);
            }
        }

        return $this->sendApiResponse($results);
    }

    /**
     *  Pathao Courier 
     */
    public function sendToPathao(array $credentials, Order $data, Shop $shop, string $note, object $request): JsonResponse
    {
        $response = $this->pathaoService->createOrder($credentials, $data, $shop, $note, $request);

        if (isset($response->code, $response->data->consignment_id, $response->data->order_status) && $response->code === 200) {
            $data->courier->update([
                'tracking_code'  => $response->data->consignment_id,
                'consignment_id' => $response->data->consignment_id,
                'status'         => $response->data->order_status,
                'provider'       => MerchantCourier::PATHAO,
            ]);
            $data->config->update([
                'courier_entry' => true
            ]);
            $data->order_status = Order::SHIPPED;
            $data->save();
            OrderStatus::query()->create([
                'order_id' => $data->id,
                'type'     => Order::SHIPPED
            ]);

            $note = "Shipped = Your order has been sent to our delivery partner (Pathao Courier";

            OrderTrackingTimeline::query()->create([
                'order_id' => $data->id,
                'event' => 'shipped',
                'note' => $note,
            ]);

            return $this->sendApiResponse(new MerchantOrderResource($data), 'Order has been sent to ' . MerchantCourier::PATHAO);
        }

        return $this->sendApiResponse('', $response->message, $response->type, [$response], $response->code);
    }

    public function getCities(Request $request): JsonResponse
    {
        $courier = $this->getMerchantCourier($request->header('shop-id'), MerchantCourier::PATHAO, true);
        $credentials = collect(json_decode($courier->config))->toArray();
        $data = $this->pathaoService->getCity($credentials);

        if ($data->code === 200) {
            return $this->sendApiResponse($data->data->data);
        }

        return $this->sendApiResponse('', 'Not found', 'NotFound');
    }

    public function getZones(Request $request): JsonResponse
    {
        $courier = $this->getMerchantCourier($request->header('shop-id'), MerchantCourier::PATHAO, true);
        $credentials = collect(json_decode($courier->config))->toArray();
        $data = $this->pathaoService->getZone($credentials, $request->input('city_id'));

        if ($data->code === 200) {
            return $this->sendApiResponse($data->data->data);
        }

        return $this->sendApiResponse('', 'Not found', 'NotFound');
    }

    public function getArea(Request $request): JsonResponse
    {
        $courier = $this->getMerchantCourier($request->header('shop-id'), MerchantCourier::PATHAO, true);
        $credentials = collect(json_decode($courier->config))->toArray();
        $data = $this->pathaoService->getArea($credentials, $request->input('zone_id'));

        if ($data->code === 200) {
            return $this->sendApiResponse($data->data->data);
        }

        return $this->sendApiResponse('', 'Not found', 'NotFound');
    }

    private function getMerchantCourier(string $shopId, string $provider, bool $activeOnly = false): object|null
    {
        $query = MerchantCourier::query()->where('shop_id', $shopId)
            ->where('provider', $provider);

        if ($activeOnly) {
            $query->where('status', 'active');
        }

        return $query->first();
    }

    /**
     *  Redx Courier 
     */
    public function sendToRedx(array $credentials, Order $data, $request, string $note)
    {
        $credential = MerchantCourier::where('shop_id')->first();
        $redx = new RedxCourier();
        $response = $redx->createParcel($credentials, $data, $request, $note, $credential);
   
        if(array_key_exists('tracking_id', $response)){
            $data->update([
                'order_status' => Order::SHIPPED
            ]);
            $data->courier()->update([
                'tracking_code' => $response['tracking_id'],
                'status' => 'pickup-pending',
                'provider' => MerchantCourier::REDX,
            ]);
    
            $data->config()->update([
                'courier_entry' => true
            ]);
    
            OrderStatus::query()->create([
                'order_id' => $data->id,
                'type' => Order::SHIPPED
            ]);

            $responseArr= [
                'order_id'  => $data->id,
                'message'   =>'Order has been sent to '.MerchantCourier::REDX,
            ];

            $note = "Shipped = Your order has been sent to our delivery partner (Redx Courier)";

            OrderTrackingTimeline::query()->create([
                'order_id' => $data->id,
                'event' => 'shipped',
                'note' => $note,
            ]);
        }else if(array_key_exists('validation_errors', $response)) {
            foreach($response['validation_errors'] as $key => $item){
                $responseArr = [
                    'errorMessage' => $item
                ]; 
            }
        }else {
            $responseArr = [
                'message'   => 'Wrong credentials',
                'status_code'   => $response['status_code'],
            ];
        }
        return $responseArr;
        
    }

    public function redxGetAreaDiscrictWise(Request $request){
        $credentials = $this->merchantCouriereProviderCheck($request, MerchantCourier::REDX);
        $district = $request->input('district_name');
        $redx = new RedxCourier();
        $apiResponse = $redx->redxGetAreaDiscrictWise($credentials, $district);
        
        return $this->sendApiResponse($apiResponse);
    }

    public function redxGetArea(Request $request){
        $credentials = $this->merchantCouriereProviderCheck($request, MerchantCourier::REDX);
        $redx = new RedxCourier();
        $apiResponse = $redx->getArea($credentials);
        
        return $this->sendApiResponse($apiResponse);
    }

    public function redxPickupStoreCreate(RedxPickupStoreCreateRequest $request) 
    {
        $credentials = $this->merchantCouriereProviderCheck($request, MerchantCourier::REDX);
        $redx = new RedxCourier();
        $apiResponse = $redx->createPickupStore($credentials, $request);

        return $this->sendApiResponse($apiResponse, 'Pickup store created successfully');
    }

    public function redxOrderDetails(Request $request)
    {
        $credentials = $this->merchantCouriereProviderCheck($request, MerchantCourier::REDX);
        $trackId = $request->trackId;
        $redx = new RedxCourier();
        $apiResponse = $redx->orderDetails($credentials, $trackId);

        return $this->sendApiResponse($apiResponse, 'Redx courier order details. Order tracking id - '.$trackId);
    }

    public function merchantCouriereProviderCheck($request, $provider)
    {
        $courier = $this->getMerchantCourier($request->header('shop-id'), $provider, true);
        if(!$courier) {
            throw ValidationException::withMessages([
                'notfound' => 'Invalid provider or merchant',
            ]);
        }
        $credentials = json_decode($courier->config, true);
        return $credentials;
    }

}