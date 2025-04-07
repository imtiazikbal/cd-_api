<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AdminBaseController;
use App\Models\Order;
use App\Models\User;
use App\Models\Transaction;

class DashboardController extends AdminBaseController
{
    public function index()
    {
        $valid_statuses = ['paid', 'Completed', 'success'];
        $total_amount = Transaction::whereIn('status', $valid_statuses)->where('type', 'package')->sum('amount');

        $response = Http::get('https://880sms.com/miscapi/R600191764bb7124559542.64067010/getBalance');

        if ($response->successful()) {
            $sms_balance = $this->extractBalance($response->body());
        } else {
            $sms_balance = 'Balance not available';
        }

        $sms_balance = 0;
        $orders = Order::query()->count();
        $merchants = User::query()->where('role', User::MERCHANT)->count();
        
        $userData = session('user_data');
        return view('panel.dashboard', compact('orders', 'merchants', 'sms_balance', 'total_amount'), ['userData' => $userData]);
    }

    private function extractBalance($response)
    {
        if (preg_match('/BDT (-?\d+(\.\d{1,2})?)/', $response, $matches)) {
            return 'BDT ' . number_format((float)$matches[1], 2);
        } else {
            return 'Balance not found';
        }
    }
}