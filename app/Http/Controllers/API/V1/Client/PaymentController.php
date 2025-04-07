<?php

/** @noinspection PhpMissingParentConstructorInspection */

/** @noinspection PhpUnusedLocalVariableInspection */

namespace App\Http\Controllers\API\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Bkash;
use App\Services\Nagad;
use App\Services\PaymentService;
use App\Traits\sendApiResponse;
use Illuminate\Http\Request;

/**
 * @property PaymentService $paymentService
 * @property Bkash $bkash
 * @property Nagad $nagad
 */
class PaymentController extends Controller
{
    use sendApiResponse;

    public function __construct(PaymentService $paymentService, Bkash $bkash, Nagad $nagad)
    {
        $this->paymentService = $paymentService;
        $this->bkash = $bkash;
        $this->nagad = $nagad;
    }

    public function pay(Request $request)
    {
        $user = User::query()->find($request->header('id'));

        return $this->paymentService->makePayment($request, $user);
    }

    public function bkashPay(Request $request): string
    {
        $data = [
            "amount"      => $request->input('amount'),
            "addons_id"   => $request->input('addons_id'),
            "order_type"  => $request->input('order_type'),
            'user_id'     => $request->header('id'),
            'invoice_num' => $request->input('invoice_num')
        ];

        $amount = Transaction::query()
            ->where('invoice_num', $data['invoice_num'])
            ->pluck('amount')
            ->first();

        if ($amount) {
            $data['amount'] = $amount;
        }

        return $this->bkash->initPayment($data);
    }

    public function nagadPay(Request $request)
    {
        $data = [
            "amount"      => $request->input('amount'),
            "addons_id"   => $request->input('addons_id'),
            "order_type"  => $request->input('order_type'),
            'user_id'     => $request->header('id'),
            'invoice_num' => $request->input('invoice_num')
        ];
        $amount = Transaction::query()
            ->where('invoice_num', $data['invoice_num'])
            ->pluck('amount')
            ->first();

        if ($amount) {
            $data['amount'] = $amount;
        }

        return $this->nagad->initPayment($data);
    }
}
