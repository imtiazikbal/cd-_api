<?php

namespace App\Http\Controllers\API\V1\Customer;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'        => 'required|min:6',
            'credentials' => 'required',
        ]);

        if ($request->hasHeader('shop-id') && $request->header('shop-id') !== null) {

            $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();

            if (!$shop) {
                throw ValidationException::withMessages([
                    'shop_id' => 'Invalid Shop Id'
                ]);
            }

            if (is_numeric($request->input('credentials'))) {

                $user = User::query()->where('phone', $request->input('credentials'))
                    ->whereHas('customer_info', function ($query) use ($shop) {
                        return $query->where('merchant_id', $shop->user_id);
                    })->first();

                if (!$user) {
                    $register = User::query()->create([
                        'name'  => $request->input('name'),
                        'phone' => $request->input('credentials'),
                        'role'  => User::CUSTOMER,
                    ]);

                    $register->customer_info()->create([
                        'type'        => 'user',
                        'merchant_id' => $shop->user_id,
                    ]);

                    return response()->json(['message' => 'Registration Successful']);
                }

                return response()->json(['message' => 'User already exists with this phone number']);
            }

            if (filter_var($request->input('credentials'), FILTER_VALIDATE_EMAIL)) {
                $user = User::query()->where('email', $request->input('credentials'))
                    ->whereHas('customer_info', function ($query) use ($shop) {
                        return $query->where('merchant_id', $shop->user_id);
                    })->first();

                if (!$user) {
                    $register = User::query()->create([
                        'name'  => $request->input('name'),
                        'email' => $request->input('credentials'),
                        'role'  => User::CUSTOMER,
                    ]);

                    $register->customer_info()->create([
                        'type'        => 'user',
                        'merchant_id' => $shop->user_id,
                    ]);

                    return response()->json(['message' => 'Registration Successful']);
                }

                return response()->json(['message' => 'User already exists with this email']);
            }
        }

        return response()->json(['error' => 'Shop id not found. Please check the shop id']);

    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'credentials' => 'required',
        ]);

        if ($request->hasHeader('shop_id') && $request->header('shop-id') !== null) {
            $shop = Shop::query()->where('shop_id', $request->header('shop-id'))->first();

            if (!$shop) {
                throw ValidationException::withMessages([
                    'shop_id' => 'Invalid Shop Id'
                ]);
            }

            if (is_numeric($request->input('credentials'))) {

                $user = User::query()->where('phone', $request->input('credentials'))
                    ->whereHas('customer_info', function ($query) use ($shop) {
                        return $query->where('merchant_id', $shop->user_id);
                    })->first();

                if ($user) {
                    if ($request->input('otp') && $user->otp === $request->input('otp')) {
                        $user->createApiToken();

                        return response()->json($user);
                    } else {
                        $user->otp = mt_rand(1111, 9999);
                        $user->save();

                        return response()->json(['message' => 'Otp Has been sent to your mobile']);
                    }
                }

                return response()->json(['message' => 'Invalid Credentials']);
            }

            if (filter_var($request->input('credentials'), FILTER_VALIDATE_EMAIL)) {
                $user = User::query()->where('email', $request->input('credentials'))
                    ->whereHas('customer_info', function ($query) use ($shop) {
                        return $query->where('merchant_id', $shop->user_id);
                    })->first();

                if ($user) {
                    if ($request->input('otp') && $user->otp === $request->input('otp')) {
                        $user->createApiToken();

                        return response()->json($user);
                    } else {
                        $user->otp = mt_rand(1111, 9999);
                        $user->save();

                        return response()->json(['message' => 'Otp Has been sent to your email']);
                    }
                }

                return response()->json(['message' => 'User already exists with this email']);
            }

        }

        return response()->json(['message' => 'Invalid Credentials']);
    }
}
