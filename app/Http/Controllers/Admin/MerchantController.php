<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Http\Resources\AdminMerchantResource;
use App\Http\Resources\AdminShopResource;
use App\Models\Order;
use App\Models\Shop;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\CustomerFaq;
use App\Models\Page;
use App\Traits\sendApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use phpseclib3\Net\SFTP;

class MerchantController extends AdminBaseController
{
    use sendApiResponse;

    public function index()
    {
        $userData = session('user_data');

        return view('panel.merchants.index', ['userData' => $userData]);
    }

    public function show($id)
    {
        $merchant = User::query()
            ->with('support_ticket', 'merchantinfo', 'shop', 'transactions')
            ->withCount(['support_ticket as total_opened_tickets' => function ($query) {
                $query->where('status', SupportTicket::OPENED);
            }, 'support_ticket as total_closed_tickets' => function ($query) {
                $query->where('status', SupportTicket::CLOSED);
            }, 'support_ticket as total_processing_tickets' => function ($query) {
                $query->where('status', SupportTicket::PROCESSING);
            }, 'support_ticket as total_solved_tickets' => function ($query) {
                $query->where('status', SupportTicket::SOLVED);
            }
            ])
            ->withCount('support_ticket')
            ->find($id);

        $userData = session('user_data');
        $start_date = Carbon::parse($merchant->next_due_date)->subDays(30);
        $order = Order::shopWiseOrderCount($merchant->shop->shop_id, $start_date->toDateString(), $merchant->next_due_date);

        if (Carbon::today() >= Carbon::parse($merchant->next_due_date)) {
            $order = Order::query()
                ->where('shop_id', $merchant->shop->shop_id)
                ->whereBetween('created_at', [$merchant->next_due_date, Carbon::today()])
                ->withTrashed()
                ->count();
        }

        $customerfaq = CustomerFaq::query()
            ->where('user_id', $id)
            ->get();

        $pages = Page::query()->with('shop', 'user')
            ->select('id', 'title', 'slug', 'status', 'user_id', 'shop_id', 'created_at')
            ->where('user_id', $merchant->id)
            ->orderByDesc('id')
            ->get();

        return view('panel.merchants.details', compact('merchant', 'order', 'customerfaq', 'pages'), ['userData' => $userData]);
    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'nullable',
            'shop_name' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
        ]);
        $user = User::findOrFail($request->merchant_id);
        $merchant = $user->shop;

        $user->fill($request->only(['name', 'email', 'phone']))->save();

        $merchant->name = $request->shop_name;

        $originalSlug = Str::slug($request->input('shop_name'));

        $existingMerchant = Shop::where('domain', $originalSlug)->first();

        if ($existingMerchant) {
            $uniqueSlug = $this->generateUniqueSlug($originalSlug);
            $merchant->domain = $uniqueSlug;
        } else {
            $merchant->domain = $originalSlug;
        }

        $merchant->save();

        return redirect()->back()->with('success', 'Merchant information updated successfully.');
    }
    private function generateUniqueSlug($originalSlug)
    {
        $suffix = 1;
        $slug = $originalSlug;

        while (Shop::where('domain', $slug)->exists()) {
            $suffix++;
            $slug = $originalSlug . '-' . $suffix;
        }

        return $slug;
    }

    public function faqsupdate(User $merchant, Request $request)
    {

        $updateData = [];

        if ($request->has('answer1')) {
            $updateData['answer1'] = $request->input('answer1');
        }

        if ($request->has('answer2')) {
            $updateData['answer2'] = $request->input('answer2');
        }

        if ($request->has('answer3')) {
            $updateData['answer3'] = $request->input('answer3');
        }

        if ($request->has('answer4')) {
            $updateData['answer4'] = $request->input('answer4');
        }

        if ($request->has('answer5')) {
            $updateData['answer5'] = $request->input('answer5');
        }

        if ($request->has('answer6')) {
            $updateData['answer6'] = $request->input('answer6');
        }

        if ($request->has('answer7')) {
            $updateData['answer7'] = $request->input('answer7');
        }

        if ($request->has('answer8')) {
            $updateData['answer8'] = $request->input('answer8');
        }

        if ($request->has('answer9')) {
            $updateData['answer9'] = $request->input('answer9');
        }

        if ($request->has('answer10')) {
            $updateData['answer10'] = $request->input('answer10');
        }

        if ($request->has('answer11')) {
            $updateData['answer11'] = $request->input('answer11');
        }

        CustomerFaq::updateOrCreate(
            ['user_id' => $merchant->id],
            $updateData
        );

        return redirect()->back();
    }

    public function changeStatus(Request $request, User $merchant): JsonResponse
    {
        if ($request->input('status') === User::STATUS_ACTIVE) {
            $merchant->update(['status' => User::STATUS_ACTIVE]);
        }

        if ($request->input('status') === User::STATUS_INACTIVE) {
            $merchant->update(['status' => User::STATUS_INACTIVE]);
        }

        if ($request->input('status') === User::STATUS_BLOCKED) {
            $merchant->update(['status' => User::STATUS_BLOCKED]);
        }

        if ($request->input('status') === User::STATUS_EXPIRED) {
            $merchant->update(['status' => User::STATUS_EXPIRED]);
        }

        return response()->json(['success' => 'Status Updated Successfully']);
    }

    public function merchants(Request $request): JsonResponse
    {
        $search = $request->search;
        $joining_date = $request->joining_date;

        $query = User::query()
            ->with('shop', function ($q){
                $q->select('id', 'name', 'shop_id', 'user_id');
            })
            ->where('role', 'merchant')
            ->latest();

        if ($search !== null) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%')
                ->orWhere('phone', 'LIKE', '%' . $request->input('search') . '%')
                ->orWhere('email', 'LIKE', '%' . $request->input('search') . '%')
                ->orWhereHas('shop', function ($query) use ($request) {
                    return $query->where('shop_id', 'LIKE', '%' . $request->input('search') . '%');
                });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($joining_date !== null) {
            $query->whereDate('created_at', $request->input('joining_date'));
        }

        $merchants = $query->paginate($this->limit(25));

        return response()->json([
            'data' => AdminMerchantResource::collection($merchants),
            'meta' => [
                'current_page'  => $merchants->currentPage(),
                'per_page'      => $merchants->perPage(),
                'total'         => ceil($merchants->total() / $merchants->perPage()),
                'next_page_url' => $merchants->nextPageUrl(),
                'prev_page_url' => $merchants->previousPageUrl(),
            ],
        ]);
    }

    public function statuses(): JsonResponse
    {
        $statuses = User::listStatus();

        return response()->json($statuses);
    }

    public function paymentStatusUpdate(Request $request, User $merchant): JsonResponse
    {
        if ($request->input('payment_status') === User::PAID) {
            $merchant->update(['payment_status' => User::PAID]);
        }

        if ($request->input('payment_status') === User::UNPAID) {
            $merchant->update(['payment_status' => User::UNPAID]);
        }

        return response()->json(['success' => 'Payment Status Updated Successfully']);
    }

    public function paymentStatus(): JsonResponse
    {
        $paymentstatus = User::listPaymentStatus();

        return response()->json($paymentstatus);
    }

    public function updateDueDate(Request $request, User $merchant)
    {
        $request->validate([
            'next_due_date' => 'required|date_format:Y-m-d',
        ]);

        try {

            if ($request->filled('next_due_date')) {
                $merchant->next_due_date = $request->input('next_due_date');
                $merchant->save();
            }

            return response()->json(['message' => 'Due date updated successfully'], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Customer not found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    public function destroy(User $merchant): JsonResponse
    {
        DB::transaction(function () use ($merchant) {
            if ($merchant) {
                $shop = Shop::query()->where('user_id', $merchant->id)->first();

                if ($shop) {
                    $shop->delete();
                }
                $merchant->delete();
                Order::query()->where('shop_id', $shop->shop_id)->each(function ($order) {
                    $order->forceDelete();
                });
            }
        });

        return response()->json('Merchant Deleted');
    }

    public function searchOrderCount(Request $request)
    {
        $startDate = Carbon::parse($request->startDate);
        $endDate = Carbon::parse($request->endDate)->addDay(1);

        $merchants = User::query()
        ->withCount(['orders as order_count' => function($query) use ($startDate, $endDate){
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->with('shop', function ($q){
            $q->select('id', 'name', 'shop_id', 'user_id');
        })
        ->where('role', 'merchant')
        ->orderByDesc('order_count')
        ->paginate($this->limit(25));
        
        return response()->json([
            'data'  => $merchants,
            'meta' => [
                'current_page'  => $merchants->currentPage(),
                'per_page'      => $merchants->perPage(),
                'total'         => ceil($merchants->total() / $merchants->perPage()),
                'next_page_url' => $merchants->nextPageUrl(),
                'prev_page_url' => $merchants->previousPageUrl(),
            ],
        ]);
    }

    public function superAdminPageEdit($pageId)
    {
        $shopId = Page::find($pageId)->shop_id;
        $fileName = 'index.html';
        $destinationFolder = "/var/www/editor.funnelliner.com/templates/" . $shopId . "/" . $pageId . "/" . $fileName;

        $sftp = $this->sftpLoginRequest();

        if(!$sftp->get($destinationFolder)) {
            toastr()->error('Oops! Page not found!');

            return redirect()->back();
        }

        $pageEditCode = $sftp->get($destinationFolder);
        $userData = session('user_data');

        return view('panel.page.edit', compact('pageEditCode', 'pageId'), ['userData' => $userData]);
    }

    public function superAdminPageUpdate(Request $request, $pageId)
    {
        $shopId = Page::find($pageId)->shop_id;
        $fileName = 'index.html';
        $destinationFolder = "/var/www/editor.funnelliner.com/templates/" . $shopId . "/" . $pageId . "/" . $fileName;
        $sftp = $this->sftpLoginRequest();
        $sftp->put($destinationFolder, $request->pageUpdatedCode);

        return redirect()->back();
    }

    private function sftpLoginRequest()
    {
        $serverIp = env('EDITOR_SERVER_IP');
        $serverPort = env('EDITOR_SERVER_PORT');
        $serverUser = env('EDITOR_SERVER_USER');
        $serverPass = env('EDITOR_SERVER_PASS');

        $sftp = new SFTP($serverIp, $serverPort);

        if(!$sftp->login($serverUser, $serverPass)) {
            throw new \Exception('SFTP login failed');
        }

        return $sftp;
    }

    public function perDayOrderCount(Request $request)
    {
        $month = date('m');  
        $year = date('Y');    

        $ordersPerDay = Order::query()
            ->select(DB::raw('DAY(created_at) as day'), DB::raw('COUNT(*) as order_count'))
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('DAY(created_at)'))
            ->orderBy('day')
            ->withTrashed()
            ->get();

        $days = [];
        $orderCounts = [];

        foreach ($ordersPerDay as $order) {
            $days[] = $order->day;
            $orderCounts[] = $order->order_count;
        }

        return response()->json([
            'days' => $days,
            'order_counts' => $orderCounts
        ]);
    }

    public function perDayNewRegisterCount(Request $request)
    {
        $month = date('m');  
        $year = date('Y');  
        
        $users = User::query()
        ->select(DB::raw('DAY(created_at) as day'), DB::raw('COUNT(*) as user_count'))
        ->whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->groupBy(DB::raw('DAY(created_at)'))
        ->orderBy('day')
        ->get();

        $days = [];
        $userCounts = [];

        foreach ($users as $user) {
            $days[] = $user->day;
            $userCounts[] = $user->user_count;
        }

        return response()->json([
            'days' => $days,
            'user_counts' => $userCounts
        ]);
    }
}