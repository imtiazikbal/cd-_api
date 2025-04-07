<?php

namespace App\Console\Commands;

use App\Models\MerchantCourier;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Services\RedxCourier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PDO;

class UpdateRedxStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redx:status-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update redx courier status update via api';

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
        ->with('courier')
        ->whereRelation('courier', 'provider', '=', 'redx')
        ->whereRelation('courier', 'tracking_code', '!=', null)
        ->get();
        
        foreach($orders as $order){

            $courier = MerchantCourier::query()
            ->where([
                'shop_id'   => $order->shop_id, 
                'provider'  => MerchantCourier::REDX,
                'status'    => 'active'
                ])
            ->whereNotNull('config')
            ->first();

            if($courier) {
                $credentials = json_decode($courier->config, true);
                $trackId = $order->courier->tracking_code;
               
                $redx = new RedxCourier;
                $apiResponse = $redx->orderDetails($credentials, $trackId);
                
                if(array_key_exists('parcel', $apiResponse)){
                    $courierStatus = $apiResponse['parcel']['status'];
    
                    $order->courier()->update([
                        'status'    => $courierStatus
                    ]);
    
                    if($courierStatus == 'pickup-pending'){
                        $order->order_status = Order::SHIPPED;
                    }
                    
                    if($courierStatus == 'delivered'){
                        $order->order_status = Order::DELIVERED;
                    }
    
                    if($courierStatus == 'rejected'){
                        $order->order_status = Order::RETURNED;
                    }
    
                    if($courierStatus == 'deleted'){
                        $order->order_status = Order::CANCELLED;
                    }
    
                    $order->save();
                    OrderStatus::query()->create([
                        'order_id' => $order->id,
                        'type' => $order->order_status
                    ]);
    
                }else {
                    $this->info($apiResponse);
                    continue;
                }
            }else {
                $this->info('Courier not found of this shop : '. $order->shop_id);
                continue;
            }
        }

        $this->info('Redx courier status updated successfully');
    }
}