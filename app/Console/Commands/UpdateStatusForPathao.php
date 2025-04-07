<?php

namespace App\Console\Commands;

use App\Models\MerchantCourier;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\PathaoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class UpdateStatusForPathao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courier:pathao-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update pathao courier status in database via api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
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

        $orders = Order::query()
            ->with('courier', 'config')
            ->whereRelation('courier', 'tracking_code', '!=', null)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Currently no order found for update');
        }

        foreach ($orders as $order) {
            $check_courier_status = MerchantCourier::query()
                ->where('provider', MerchantCourier::PATHAO)
                ->where('shop_id', $order->shop_id)
                ->where('status', 'active')
                ->whereNotNull('config')
                ->first();

            if ($check_courier_status && $check_courier_status->config !== null) {
                $courier = new PathaoService();
                $credentials = collect(json_decode($check_courier_status->config))->toArray();
                $response = $courier->orderDetails($credentials, $order->courier->tracking_code);

                if ($response !== null && property_exists($response, 'data')) {
                    $status = collect($response->data)->toArray();

                    if (isset($status['order_status'])) {

                        $order->courier()->update([
                            'status' => $status['order_status']
                        ]);

                        if ($status['order_status'] === "Delivered") {
                            $order->order_status = Order::DELIVERED;
                        }

                        if ($status['order_status'] === "Pickup_Cancelled") {
                            $order->order_status = Order::CANCELLED;
                        }

                        if ($status['order_status'] === "Return") {
                            $order->order_status = Order::RETURNED;
                        }

                        $order->save();

                        OrderStatus::query()->create([
                            'order_id' => $order->id,
                            'type'     => $order->order_status
                        ]);

                    }
                }
            }
        }

        $this->info('Pathao courier status updated successfully');
    }
}