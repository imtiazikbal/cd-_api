<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\Courier;
use App\Models\MerchantCourier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class ProcessOrderStatus implements ShouldQueue
{
    use Dispatchable;

    use InteractsWithQueue;

    use Queueable;

    use SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::connection('mysql')->setPdo(new PDO(
            "mysql:host=" . config('database.connections.mysql.host') .
            ";port=" . config('database.connections.mysql.port') .
            ";dbname=" . config('database.connections.mysql.database'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password')
        ));
        $check_courier_status = MerchantCourier::query()
            ->where('provider', MerchantCourier::STEADFAST)
            ->where('shop_id', $this->order->shop_id)
            ->where('status', 'active')
            ->first();

        if ($check_courier_status && $check_courier_status->config !== null) {
            $courier = new Courier();
            $credentials = json_decode($check_courier_status->config, true);
            $response = $courier->trackOrder($credentials, '/status_by_trackingcode/' . $this->order->courier->tracking_code);
            $status = json_decode($response);
            Log::info('courier', ['status' => $status, 'tracking_code' => $this->order->courier->tracking_code]);

            // if ($status !== null){
            if (isset($status->delivery_status)) {
                $orderStatus = null;

                switch ($status->delivery_status) {
                    case Courier::status('cancelled'):
                    case Courier::status('cancelled_approval_pending'):
                        $orderStatus = Order::RETURNED;

                        break;
                    case Courier::status('unknown'):
                        $orderStatus = Order::CANCELLED;

                        break;
                    case Courier::status('pending'):
                    case Courier::status('in_review'):
                        $orderStatus = Order::SHIPPED;

                        break;
                    case Courier::status('delivered'):
                        $orderStatus = Order::DELIVERED;

                        break;
                }

                if ($orderStatus !== null) {
                    $this->order->courier()->update([
                        'status' => $status->delivery_status
                    ]);

                    $this->order->order_status = $orderStatus;
                    $this->order->save();

                    OrderStatus::query()->create([
                        'order_id' => $this->order->id,
                        'type'     => $orderStatus
                    ]);
                }
            }
            // }else{

            //     switch ($status == null){
            //         case Courier::status('delivered'):
            //             $orderStatus = Order::DELIVERED;
            //             break;
            //     }
            //     if ($orderStatus !== null) {
            //         $this->order->courier()->update([
            //             'status' => $orderStatus
            //         ]);

            //         $this->order->order_status = $orderStatus;
            //         $this->order->save();
            //         Log::info('courier', ['status-null' => $this->order->order_status, 'tracking_code' => $this->order->courier->tracking_code]);

            //         OrderStatus::query()->updateOrCreate([
            //             'order_id' => $this->order->id,
            //             'type' => $orderStatus
            //         ]);
            //         Log::info('courier', ['status-OrderStatus-orderID' => $this->order->id, 'tracking_code' => $this->order->courier->tracking_code]);
            //     }

            // }

        }
    }
}
