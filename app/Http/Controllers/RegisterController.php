<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\Bkash;
use App\Services\Nagad;
use Illuminate\Http\JsonResponse;

/**
 * Class RegisterController
 * @package App\Http\Controllers
 * @property Bkash $bkash
 * @property Nagad $nagad
 */
class RegisterController extends Controller
{
    public function __construct(Bkash $bkash, Nagad $nagad)
    {
        $this->bkash = $bkash;
        $this->nagad = $nagad;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $type = $request->input('type');
        $data = [
            'name'       => $request->input('name'),
            'phone'      => $request->input('phone'),
            'email'      => $request->input('email'),
            'amount'     => $request->input('amount'),
            'order_type' => 'package'
        ];

        $payment_url = match ($type) {
            'bkash' => $this->bkash->initPayment($data),
            'nagad' => $this->nagad->initPayment($data),
            default => false, // or whatever fallback action you want to take
        };

        return $this->sendApiResponse($payment_url);
    }
}
