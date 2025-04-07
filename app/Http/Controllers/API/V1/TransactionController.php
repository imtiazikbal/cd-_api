<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\MyAddons;
use App\Models\Order;
use App\Models\Package;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PDF;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        if ($request->data['status'] === 'VALID' || $request->data['status'] === 'VALIDATED') {

            $transaction_invoice = Transaction::query()->latest()->first();

            if ($transaction_invoice->invoice_num !== null) {
                $invoice_num = $transaction_invoice->invoice_num + 1;
            } else {
                $number = 10;
                $invoice_num = date('Y') . $number;
            }

            $transaction = Transaction::query()->with('user')->create([
                'user_id'     => $request->user_id,
                'invoice_num' => $invoice_num,
                'addons_id'   => $request->addons_id,
                'trxid'       => uniqid('', true),
                'type'        => $request->data['params']['order_type'],
                'amount'      => $request->data['amount'],
                'response'    => json_encode($request->data),
                'status'      => $request->data['status'],
                'gateway'     => 'ssl',
                'sub_gateway' => $request->data['card_type'],
            ]);

            if ($request->data['params']['order_type'] === 'sms') {
                $shops = Shop::query()->where('user_id', $request->user_id)->first();
                $shops->sms_balance = $shops->sms_balance + $request->data['amount'];
                $shops->save();

                return $transaction;
            }

            if ($request->data['params']['order_type'] === 'plugin') {
                $shops = Shop::query()->where('user_id', $request->user_id)->first();
                $plugins = MyAddons::query()->create([
                    'shop_id'   => $shops->shop_id,
                    'addons_id' => $request->data['params']['addons_id'],
                    'status'    => false
                ]);

                return $transaction;
            }

            if ($request->data['params']['order_type'] === 'package') {
                $subscription = Subscription::query()->create([
                    'user_id'       => $request->user_id,
                    'amount'        => $request->data['amount'],
                    'gateway'       => 'ssl',
                    'sub_gateway'   => $request->data['card_type'],
                    'gateway_trxid' => $request->data['tran_id'],
                    'api_response'  => json_encode($request->data),
                    'next_due_date' => Carbon::now()->addDays(30),
                    'status'        => $request->data['params']['order_type']
                ]);

                if ($subscription) {
                    $user = User::query()->find($request->user_id);
                    $user->payment_status = User::PAID;
                    $user->status = User::STATUS_ACTIVE;
                    $user->save();
                }

                return $transaction;
            }

            return $transaction;
        }

        return false;
    }

    /**
     * Get resource of all transactions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $perPage = 10;

        if ($request->query('perPage')) {
            $perPage = $request->query('perPage');
        }

        $userId = $request->header('id');
        $shopId = $request->header('shop-id');

        $transaction = Transaction::with('addons')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->paginate($perPage);

        $merchant = User::query()->find($userId);
        $end_date = $merchant->next_due_date;
        $start_date = Carbon::parse($end_date)->subDays(30);

        $orders = Order::query()
            ->where('shop_id', $request->header('shop-id'))
            ->whereBetween('created_at', [$start_date->toDateString(), $merchant->next_due_date])
            ->withTrashed()
            ->count();

        if (Carbon::today() >= Carbon::parse($end_date)) {
            $orders = Order::query()
                ->where('shop_id', $shopId)
                ->whereBetween('created_at', [Carbon::parse($end_date), Carbon::today()])
                ->withTrashed()
                ->count();
        }

        if ($transaction->isEmpty()) {
            return $this->sendApiResponse('', "Transactions not found", 'false', extra: ['orders' => $orders]);
        }

        return $this->sendApiResponse($transaction, 'Transactions list', '', ['orders' => $orders]);
    }

    public function packageInfo($id): JsonResponse
    {
        $package = Package::query()->find($id);

        return $this->sendApiResponse($package);
    }

    public function generateTransactionPDF(Request $request, $id)
    {
        $transaction = Transaction::query()->where('user_id', $request->header('id'))
            ->find($id);

        if ($transaction->gateway === 'ssl') {
            $paymentType = 'SslCommerz';
        } elseif ($transaction->gateway === 'bKash' || $transaction->gateway === 'Bkash') {
            $paymentType = 'bKash';
        } elseif ($transaction->gateway === 'nagad') {
            $paymentType = 'Nagad';
        } else {
            $paymentType = null;
        }
        $html = view('panel.pdf.transaction_pdf', compact('transaction', 'paymentType'));
        $pdf = PDF::loadHTML($html);

        return $pdf->download('transaction.pdf');
    }

    public function paymentStatusUpdate($id)
    {
        $transaction = Transaction::where('id', $id)
        ->where('type', 'package')
        ->first();

        if(!$transaction){
            toastr()->error('Transaction not found !');
            return redirect()->back();
        }

        if($transaction->status == 'unpaid'){
            $transaction->status = 'paid';
            $transaction->update();
            
            $transaction->user->next_due_date = Carbon::parse($transaction->user->next_due_date)->addMonth();
            $transaction->user->payment_status = 'paid';
            $transaction->user->status = 'active';
            $transaction->user->update();
        }else {
            $transaction->status = 'unpaid';
            $transaction->update();
            
            $transaction->user->next_due_date = Carbon::parse($transaction->user->next_due_date)->subMonth();
            $transaction->user->payment_status = 'unpaid';
            $transaction->user->status = 'expire';
            $transaction->user->update();
             
        }

        toastr()->error('Transaction payment status updated');
        return redirect()->back();
        
    }
}