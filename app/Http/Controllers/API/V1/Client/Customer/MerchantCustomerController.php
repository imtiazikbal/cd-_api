<?php

namespace App\Http\Controllers\API\V1\Client\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\CustomerInfo;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class MerchantCustomerController extends controller
{
    public function getCustomerByMerchant($id): JsonResponse
    {
        $customers = User::query()
            ->where('role', User::CUSTOMER)
            ->whereHas('customer_info', function ($q) use ($id) {
                return $q->where('merchant_id', $id);
            })->get();

        return response()->json(CustomerResource::collection($customers));
    }

    // customer pending order list
    public function customerOrderList(Request $request)
    {
        $customer = CustomerInfo::query()
            ->select('id', 'merchant_id', 'phone', 'name', 'created_at', 'address')
            ->with(['orders' => function ($q) use ($request) {
                $ordersData = $q->where('order_status', $request->order_status)->get();

                foreach ($ordersData as $order) {
                    if ($order->order_status === Order::PENDING) {
                        return $order;
                    }

                    if ($order->order_status === Order::FOLLOWUP) {
                        return $order;
                    }

                    if ($order->order_status === Order::DELIVERED) {
                        return $order;
                    }

                    if ($order->order_status === Order::CONFIRMED) {
                        return $order;
                    }

                    if ($order->order_status === Order::RETURNED) {
                        return $order;
                    }

                    if ($order->order_status === Order::CANCELLED) {
                        return $order;
                    }
                }
            }])
            ->where('merchant_id', $request->header('shop-id'))
            ->orderByDesc('id')
            ->get();

        if ($request->order_status === Order::ALL) {
            $customer = CustomerInfo::query()->with('orders')->where('merchant_id', $request->header('shop-id'))
                ->orderByDesc('id')->get();
        }

        $customer = $customer->filter(function ($q) {
            $orderCount = $q->orders->count();
            $q->order_count = $orderCount;
            $q->makeHidden('orders');

            return $orderCount > 0; // Only keep items with order_count greater than 0
        });

        // Paginate the filtered collection
        $perPage = $this->limit();
        $currentPage = Paginator::resolveCurrentPage();
        $pagedData = $customer->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $customer = new LengthAwarePaginator($pagedData, count($customer), $perPage);

        if(!$customer) {
            return $this->sendApiResponse('', 'Customer not found!', 'false');
        }

        return $this->sendApiResponse($customer, 'Customer order list');
    }

    public function customerListSearch(Request $request)
    {
        $terms = '%' . $request->search . '%';
        $search = CustomerInfo::query()
            ->where('merchant_id', $request->header('shop-id'))
            ->where(function ($query) use ($terms) {
                $query->where('name', 'LIKE', $terms)
                    ->orWhere('phone', 'LIKE', $terms)
                    ->orWhere('address', 'LIKE', $terms);
            })
            ->get();

        if(count($search) === 0) {
            return $this->sendApiResponse('', 'Customer search result not found !', 'false');
        }

        return $this->sendApiResponse($search, 'Customer search result !');
    }
}