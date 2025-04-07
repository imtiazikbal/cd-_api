<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\SubscriptionNotification;
use App\Services\Sms;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class SubscriptionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:reminder {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
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

        $sms = new Sms();
        $user_id = $this->argument('user_id');
        $merchants = User::query()
            ->with('shop', 'transactions')
            ->where('role', User::MERCHANT);

        if ($user_id) {
            $merchants->where('id', $user_id);
        }

        $merchants = $merchants->get();

        foreach ($merchants as $merchant) {
            if ($merchant->shop) {
                $start_date = Carbon::parse($merchant->next_due_date)->subDays(30);
                $expired_at = $merchant->next_due_date;
                $order = Order::query()
                    ->where('shop_id', $merchant->shop->shop_id)
                    ->whereBetween('created_at', [$start_date->toDateString(), $merchant->next_due_date])
                    ->withTrashed()
                    ->count();

                $package = Package::query()
                    ->where('min_order', '<=', $order)
                    ->where('max_order', '>=', $order)
                    ->first();

                $days_diff_with_due_date = Carbon::today()->diffInDays($expired_at);

                if ($merchant->shop && $order > 20) {
                    if ($days_diff_with_due_date === 0) {

                        $exitTransaction = Transaction::query()->where('user_id', $merchant->id)->where('type', 'package')->where('status', 'unpaid')->first();

                        if(!$exitTransaction) {

                            $transaction = Transaction::query()->create([
                                'user_id'     => $merchant->id,
                                'invoice_num' => random_int(11111111, 99999999),
                                'type'        => 'package',
                                'amount'      => $package->price,
                                'status'      => 'unpaid',
                                'due_date'    => $merchant->next_due_date,
                                'package_id'  => $package->id,
                                'order_count' => $order
                            ]);
                            $transaction['createdDate'] = Carbon::parse($transaction->created_at)->format('d M, Y');
                            $transaction['nexDueDate'] = Carbon::parse($transaction->next_due_date)->format('d M, Y');
                            $transaction['days_diff'] = $days_diff_with_due_date;
                            $sms->sendSmsForSubscriptionReminder($merchant, $days_diff_with_due_date, $merchant->next_due_date);
                            $merchant->notify(new SubscriptionNotification($transaction));
                        }
                    }

                    if (Carbon::today()->gt($expired_at)) {
                        $days_diff = [7, 4, 3, 2, 1];

                        $transaction = Transaction::query()->where('user_id', $merchant->id)->first();

                        if($transaction) {
                            foreach ($days_diff as $days) {
                                if ($days_diff_with_due_date === $days) {

                                    // $transaction = Transaction::query()->update(
                                    //     [
                                    //         'user_id' => $merchant->id,
                                    //         'type' => 'package',
                                    //         'amount' => $package->price,
                                    //     ],
                                    //     [
                                    //         'invoice_num' => random_int(11111111, 99999999),
                                    //         'status' => 'unpaid',
                                    //         'due_date' => $merchant->next_due_date,
                                    //         'package_id' => $package->id
                                    //     ]
                                    // );

                                    $transaction['days_diff'] = $merchant->next_due_date;
                                    $sms->sendSmsForSubscriptionReminder($merchant, $days_diff_with_due_date, $merchant->next_due_date);
                                    $merchant->notify(new SubscriptionNotification($transaction));

                                    break;
                                }
                            }
                        }
                    }

                } elseif($days_diff_with_due_date === 0) {
                    $user = User::find($merchant->id);
                    $user->status = 'active';
                    $user->payment_status = 'paid';
                    $user->next_due_date = Carbon::parse($merchant->next_due_date)->addDays(30);
                    $user->update();
                }
            }
        }

    }
}
