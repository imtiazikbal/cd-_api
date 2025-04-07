<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDO;

class DeleteUnverifiedOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:delete-unverified';

    protected $description = 'Delete unverified orders older than 24 hours';

    public function handle()
    {
        DB::connection('mysql')->setPdo(new PDO(
            "mysql:host=" . config('database.connections.mysql.host') .
            ";port=" . config('database.connections.mysql.port') .
            ";dbname=" . config('database.connections.mysql.database'),
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password')
        ));

        $yesterday = Carbon::now()->subDay();

        $unverifiedOrders = Order::where('order_status', 'unverified')
            ->where('created_at', '<=', $yesterday)
            ->get();

        foreach ($unverifiedOrders as $order) {
            $order->delete();
        }

        $this->info('Unverified orders older than 24 hours have been deleted.');
    }
}
