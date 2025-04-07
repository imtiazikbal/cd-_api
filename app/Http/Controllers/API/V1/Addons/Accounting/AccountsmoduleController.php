<?php

namespace App\Http\Controllers\API\V1\Addons\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountLedgerRequest;
use App\Http\Requests\AccountPayorRequest;
use App\Http\Requests\CashInRequest;
use App\Http\Requests\PaymentMethodRequest;
use App\Http\Resources\AccountsModuleResources;
use App\Http\Resources\AccountsMultiSearchResources;
use App\Models\AccountLedger;
use App\Models\AccountPayor;
use App\Models\Accountsmodule;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountsmoduleController extends Controller
{
    private false|string $timeType;

    public function __construct()
    {
        $this->timeType = date("g:i a");
    }

    public function cashInPayment(CashInRequest $request): JsonResponse
    {
        $lastInsert = Accountsmodule::query()->where('shop_id', $request->header('shop-id'))->latest()->first();
        $time = $this->timeType;
        $date = date("Y-m-d");

        $cashIn = Accountsmodule::query()->create([
            'shop_id'      => $request->header('shop-id'),
            'bill_no'      => str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
            'ledger_id'    => $request->ledger_id, // ledger id
            'amount'       => $request->amount,
            'payor_id'     => $request->payor_id,
            'payment_type' => $request->payment_type,
            'payment_id'   => $request->payment_id,
            'description'  => $request->description,
            'balance'      => ($lastInsert->balance ?? 0) + $request->amount,
            'date'         => $date,
            'time'         => $time,
            'status'       => 'CashIn'
        ]);

        if (!$cashIn) {
            return $this->sendApiResponse('', 'Not found', 'NotFound');
        }

        return $this->sendApiResponse($cashIn, 'CashIn received successfully');
    }

    public function cashOutPayment(CashInRequest $request): JsonResponse
    {
        $lastInsert = Accountsmodule::query()->where('shop_id', $request->header('shop-id'))->latest()->first();
        $time = $this->timeType;
        $date = date("Y-m-d");

        $cashOut = Accountsmodule::query()->create([
            'shop_id'      => $request->header('shop-id'),
            'bill_no'      => str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
            'ledger_id'    => $request->ledger_id,
            'amount'       => $request->amount,
            'payor_id'     => $request->payor_id,
            'payment_type' => $request->payment_type,
            'payment_id'   => $request->payment_id,
            'description'  => $request->description,
            'balance'      => ($lastInsert->balance ?? 0) - $request->amount,
            'date'         => $date,
            'time'         => $time,
            'status'       => 'CashOut'
        ]);

        if (!$cashOut) {
            return $this->sendApiResponse('', 'CashOut not found', 'NotFound');
        }

        return $this->sendApiResponse($cashOut, 'CashOut received successfully');
    }

    public function PaymentListShow(Request $request): JsonResponse
    {
        $paymentList = Accountsmodule::with('ledger', 'payor', 'payment')
            ->where('shop_id', $request->header('shop-id'))
            ->orderByDesc('id')
            ->get();

        if (!$paymentList) {
            return $this->sendApiResponse('', 'No data available', 'NotAvailable');
        }

        return $this->sendApiResponse(AccountsModuleResources::collection($paymentList), 'Payment list');
    }

    public function PaymentEdit(int $id, Request $request): JsonResponse
    {
        $editPayment = Accountsmodule::with('ledger', 'payor')
            ->where('shop_id', $request->header('shop-id'))
            ->find($id);

        return $this->sendApiResponse($editPayment, 'Payment data view successfully');
    }

    public function PaymentUpdate(CashInRequest $request, int $id): JsonResponse
    {
        $time = $this->timeType;
        $date = date("Y-m-d");
        $lastInsert = Accountsmodule::query()->where('shop_id', $request->header('shop-id'))
            ->latest()
            ->first();

        $updatePayment = Accountsmodule::query()->where('shop_id', $request->header('shop-id'))->find($id);
        $updatePayment->ledger_id = $request->ledger_id;
        $updatePayment->amount = $request->amount;
        $updatePayment->payor_id = $request->payor_id;
        $updatePayment->payment_type = $request->payment_type;
        $updatePayment->payment_id = $request->payment_id;
        $updatePayment->description = $request->description;
        $updatePayment->date = $date;
        $updatePayment->time = $time;

        if ($updatePayment->status === 'CashIn') {
            $updatePayment->status = 'CashIn';
            $updatePayment->balance = ($lastInsert->balance ?? 0) + $request->amount;
        } else {
            $updatePayment->status = 'CashOut';
            $updatePayment->balance = ($lastInsert->balance ?? 0) + $request->amount;
        }
        $updatePayment->update();

        return $this->sendApiResponse($updatePayment, 'Payment update successfully');
    }

    public function PaymentDelete(int $id): JsonResponse
    {
        Accountsmodule::query()->find($id)->delete();

        return $this->sendApiResponse('', 'Payment deleted successfully');
    }

    public function PaymentCalculation(Request $request): JsonResponse
    {
        $payment = Accountsmodule::query()->where('shop_id', $request->header('shop-id'))->get();
        $cashIn = $payment->where('status', 'CashIn')->sum('amount');
        $cashOut = $payment->where('status', 'CashOut')->sum('amount');

        $balance = $cashIn - $cashOut;
        $balanchSheet = [
            'cashIn'  => $cashIn,
            'cashOut' => $cashOut,
            'balance' => $balance
        ];

        return $this->sendApiResponse($balanchSheet, 'Payment balance calculation');
    }

    public function PaymentSearch(string $search, Request $request): JsonResponse
    {
        $accountModule = Accountsmodule::query();

        if ($search !== null) {
            $accountModule = $accountModule->orWhere('accountsmodules.description', 'LIKE', '%' . $search . '%');
            $accountModule = $accountModule->orWhere('accountsmodules.payment_type', 'LIKE', '%' . $search . '%');
            $accountModule = $accountModule->orWhere('accountsmodules.bill_no', 'LIKE', '%' . $search . '%');
            $accountModule = $accountModule->orWhere('account_ledgers.name', 'LIKE', '%' . $search . '%');
            $accountModule = $accountModule->orWhere('account_payors.name', 'LIKE', '%' . $search . '%');
        }

        $accountModule = $accountModule
            ->select('accountsmodules.*', 'account_ledgers.name as ledgerName', 'account_payors.name as payorName')
            ->leftJoin('account_ledgers', 'account_ledgers.id', 'accountsmodules.ledger_id')
            ->leftJoin('account_payors', 'account_payors.id', 'accountsmodules.payor_id')
            ->get();

        $filterArr = [];

        foreach ($accountModule as $filter) {
            if ($request->header('shop-id') == $filter->shop_id) {
                $filterArr[] = [
                    "id"           => $filter->id,
                    "shop_id"      => $filter->shop_id,
                    "ledger_id"    => $filter->ledger_id,
                    "amount"       => $filter->amount,
                    "payor_id"     => $filter->payor_id,
                    "payment_type" => $filter->payment_type,
                    "description"  => $filter->description,
                    "date"         => $filter->date,
                    "time"         => $filter->time,
                    "status"       => $filter->status,
                    "created_at"   => $filter->created_at,
                    "updated_at"   => $filter->updated_at,
                    "bill_no"      => $filter->bill_no,
                    "balance"      => $filter->balance,
                    "ledgerName"   => $filter->ledgerName,
                    "payorName"    => $filter->payorName,
                ];
            }
        }

        return $this->sendApiResponse($filterArr, 'Payment search result');
    }

    public function paymentMethodAdd(PaymentMethodRequest $request): JsonResponse
    {
        // payment method create
        $payment = PaymentMethod::query()->create([
            'name'    => $request->input('name'),
            'shop_id' => $request->header('shop-id'),
            'type'    => $request->input('type')
        ]);

        return $this->sendApiResponse($payment, 'Add payment method successfully');
    }

    public function paymentMethodShow(Request $request): JsonResponse
    {
        $paymentMethods = PaymentMethod::query()
            ->where('type', $request->type)
            ->where('shop_id', $request->header('shop-id'))
            ->orderByDesc('id')
            ->get();

        return $this->sendApiResponse($paymentMethods, 'Show payment methods');
    }

    public function paymentMethodDelete(int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::query()
            ->where('id', $id)
            ->where('shop_id', request()->header('shop-id'))
            ->delete();

        if (!$paymentMethod) {
            return $this->sendApiResponse('', 'Data not found !');
        }

        return $this->sendApiResponse('', 'Delete payment method');
    }

    public function accountPayorAdd(AccountPayorRequest $request): JsonResponse
    {
        $payor = AccountPayor::query()->create([
            'name'    => $request->name,
            'shop_id' => $request->header('shop-id'),
            'type'    => $request->input('type')
        ]);

        return $this->sendApiResponse($payor, 'Account payor created successfully');
    }

    public function accountPayorList(Request $request): JsonResponse
    {
        $payorList = AccountPayor::query()
            ->where('type', $request->type)
            ->where('shop_id', $request->header('shop-id'))
            ->distinct('id', 'name')
            ->orderByDesc('id')
            ->pluck('id', 'name');

        $payorArr = [];

        foreach ($payorList as $index => $payor) {
            $payorArr[] = [
                'id'   => $payor,
                'name' => $index,
            ];
        }

        return $this->sendApiResponse($payorArr, 'Payor list show');
    }

    public function accountPayorDelete(int $id): JsonResponse
    {
        $accountPayor = AccountPayor::query()
            ->where('id', $id)
            ->where('shop_id', request()->header('shop-id'))
            ->delete();

        if (!$accountPayor) {
            return $this->sendApiResponse('', 'Data not found !');
        }

        return $this->sendApiResponse('', 'Delete account payor');
    }

    public function accountLedgerAdd(AccountLedgerRequest $request): JsonResponse
    {
        $ledger = AccountLedger::query()->create([
            'name'    => $request->name,
            'shop_id' => $request->header('shop-id'),
            'type'    => $request->input('type')
        ]);

        return $this->sendApiResponse($ledger, 'Account ledger created successfully');
    }

    public function accountLedgerList(Request $request): JsonResponse
    {
        $ledgerList = AccountLedger::query()
            ->where('type', $request->type)
            ->where('shop_id', $request->header('shop-id'))
            ->distinct('id', 'name')
            ->orderByDesc('id')
            ->pluck('id', 'name');

        $ledgerArr = [];

        foreach ($ledgerList as $index => $ledger) {
            $ledgerArr[] = [
                'id'   => $ledger,
                'name' => $index,
            ];
        }

        return $this->sendApiResponse($ledgerArr, 'Ledger list show');
    }

    public function accountLedgerDelete(int $id): JsonResponse
    {
        $accountLedger = AccountLedger::query()
            ->where('id', $id)
            ->where('shop_id', request()->header('shop-id'))
            ->delete();

        if (!$accountLedger) {
            return $this->sendApiResponse('', 'Data not found !');
        }

        return $this->sendApiResponse('', 'Delete account ledger');
    }

    public function accountModuleMultiSearch(Request $request): JsonResponse
    {
        $search = Accountsmodule::query()
            ->with('ledger', 'payor', 'payment')
            ->where('shop_id', $request->header('shop-id'))
            ->where(function ($query) use ($request) {
                $query->when($request->payor, function ($query) use ($request) {
                    $query->whereIn('payor_id', $request->payor);
                })
                    ->when($request->ledger, function ($query) use ($request) {
                        $query->whereIn('ledger_id', $request->ledger);
                    })
                    ->when($request->payment_id, function ($query) use ($request) {
                        $query->whereIn('payment_id', $request->payment_id);
                    })
                    ->when($request->date === 'today', function ($query) {
                        $query->whereDate('created_at', Carbon::today());
                    })
                    ->when($request->date === 'yesterday', function ($query) {
                        $query->whereDate('created_at', Carbon::yesterday());
                    })
                    ->when($request->date === 'weekly', function ($query) {
                        $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    })
                    ->when($request->date === 'monthly', function ($query) {
                        $query->whereMonth('created_at', date('m'));
                    })
                    ->when($request->date === 'yearly', function ($query) {
                        $query->whereYear('created_at', Carbon::today()->year);
                    })
                    ->when($request->date === 'custom', function ($query) use ($request) {
                        $query->whereDate('created_at', '>=', Carbon::parse($request->start_date)->toDateTimeString())
                            ->whereDate('created_at', '<=', Carbon::parse($request->end_date)->toDateTimeString());
                    });
            })
            ->orderByDesc('id')
            ->get();

        $totalCashout = 0;
        $totalCashin = 0;

        foreach ($search as $payment) {
            if ($payment->status === 'CashOut') {
                $totalCashout += $payment->amount;
            } elseif ($payment->status === 'CashIn') {
                $totalCashin += $payment->amount;
            }
        }
        $totalBalance = $totalCashin - $totalCashout;

        return $this->sendApiResponse([
            'payments' => AccountsMultiSearchResources::collection($search),
            'cashIn'   => $totalCashin,
            'cashOut'  => $totalCashout,
            'balance'  => $totalBalance
        ], 'Payment searching data');
    }
}
