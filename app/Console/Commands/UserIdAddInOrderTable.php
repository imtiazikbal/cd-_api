<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use PDO;

class UserIdAddInOrderTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order-table:user-id-add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $orders = Order::query()->with('shop')->get();
        foreach($orders as $order){
            $order->user_id = $order->shop->user_id ?? 0;
            $order->save();
        }

        $this->info('User id adde in each orders.');
    }
}