<?php

namespace App\Console\Commands;

use App\Models\MerchantCourier;
use App\Models\Shop;
use App\Services\Courier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class CourierBalanceUpdate extends Command
{
    protected $signature = 'courier:balance-update';

    protected $description = 'Update steadfast courier balance in the database via API';

    public function handle()
    {
        DB::connection('mysql')->setPdo(new PDO(
            "mysql:host=" . config('database.connections.mysql.host') .
            ";port=" . config('database.connections.mysql.port') .
            ";dbname=" . config('database.connections.mysql.database'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password')
        ));

        $courier_merchants = DB::connection('mysql')
            ->table('merchant_couriers')
            ->where('provider', MerchantCourier::STEADFAST)
            ->whereNotNull('config')
            ->where('status', 'active')
            ->get();

        foreach ($courier_merchants as $merchant) {
            $courier = new Courier();
            $credentials = collect(json_decode($merchant->config))->toArray();
            $response = $courier->checkBalance($credentials, '/get_balance');
            $status = json_decode($response->body());

            if ($status !== null && property_exists($status, 'status') && $status->status === 200) {
                $shop = Shop::query()->where('shop_id', $merchant->shop_id)->first();
                $shop->update([
                    'courier_balance' => $status->current_balance
                ]);
            }
        }

        DB::connection('mysql')->disconnect();
      
        $this->info('All shops courier balance updated');
    }
}