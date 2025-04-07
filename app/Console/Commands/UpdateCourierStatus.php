<?php

namespace App\Console\Commands;

use App\Models\MerchantCourier;
use App\Models\Order;
use App\Jobs\ProcessOrderStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class UpdateCourierStatus extends Command
{
    protected $signature = 'courier:status';

    protected $description = 'update steadfast courier status in database via api';

    public function handle()
    {
        DB::connection('mysql')->setPdo(new PDO(
            "mysql:host=" . config('database.connections.mysql.host') .
            ";port=" . config('database.connections.mysql.port') .
            ";dbname=" . config('database.connections.mysql.database'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password')
        ));
        $batchSize = 1000;

        $totalOrders = Order::with('courier')
                ->whereHas('courier', function ($query) {
                    $query->where('provider', MerchantCourier::STEADFAST);
                })->where('order_status', 'shipped')->count();

        $processedOrders = 0;

        if ($totalOrders === 0) {
            $this->info('Currently no orders found for update');

            return;
        }

        while ($processedOrders < $totalOrders) {
            $orders = Order::with('courier')
                ->whereHas('courier', function ($query) {
                    $query->whereNotNull('tracking_code');
                })
                ->where('order_status', 'shipped')
                ->orderBy('id', 'desc')
                ->skip($processedOrders)
                ->take($batchSize)
                ->get();

            $orders->each(function ($order) {

                dispatch(new ProcessOrderStatus($order));
            });

            $processedOrders += $batchSize;


            $this->info("Processed $processedOrders out of $totalOrders orders.");
        }

        $this->info('Orders are being processed asynchronously.');
    }
}