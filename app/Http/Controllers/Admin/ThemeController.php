<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminBaseController;
use App\Http\Requests\ThemeRequest;
use App\Models\Media;
use App\Models\Shop;
use App\Models\Theme;
use App\Services\Sms;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ThemeController extends AdminBaseController
{
    use sendApiResponse;

    public function index()
    {
        $userData = session('user_data');
        return view('panel.themes.index', ['userData' => $userData]);
    }

    public function store(ThemeRequest $request): JsonResponse
    {
        $theme = Theme::query()->create([
            'type'       => $request->input('type'),
            'name'       => $request->input('name'),
            'url'        => $request->input('url'),
            'theme_name' => $request->input('name'),
        ]);

        if ($request->hasFile('image')) {
            $filePath = 'media/theme' . $request->header('id');
            Media::upload($theme, $request->file('image'), $filePath, 'template');
        }

        return $this->sendApiResponse($theme, 'Template Added successfully');
    }

    public function getThemes(): JsonResponse
    {
        $themes = Theme::query()->orderByDesc('id')->get();
        $themes->load('media');

        return $this->sendApiResponse($themes);
    }

    public function domainRequest($type = 'pending')
    {
        $domains = Shop::query()
        ->with('merchant', function ($query) {
            $query->select('id', 'name', 'phone');
        })
        ->select('id', 'domain_status', 'user_id', 'name', 'domain_request', 'shop_id', 'domain_request_date')
        ->where('domain_status', $type)
        ->orderByDesc('domain_request_date')
        ->paginate($this->limit(25));
        $userData = session('user_data');
        
        return view('panel.domain.index', [
            'domains' => $domains,
            'type'    => $type
        ], ['userData' => $userData]);
    }

    public function domainRequestStatusUpdate($id, $type, $rejectReason = null)
    {
        $domains = Shop::query()
        ->select('id', 'domain_status', 'user_id', 'name', 'domain_request', 'reject_reason', 'sms_balance', 'sms_sent')
        ->where('id', $id)
        ->with('merchant', function ($query) {
            $query->select('id', 'name', 'phone');
        })
        ->first();

        match($type){
            'pending' => $domains->domain_status = 'connected',
            'connected' => $domains->domain_status = 'pending',
            'rejected'  => $domains->domain_status = 'rejected',
        };

        if($rejectReason){
            $domains->reject_reason = $rejectReason;
        }
        $domains->update();

        if($domains->sms_balance < .30){
            toastr()->warning('Insufficient sms balance!');
        }elseif(!$domains->merchant->phone){
            toastr()->error('Phone number is missing!');
        }else {
            $sms = new Sms();
            $sms->domainRequestStatusUpdateMessage($domains);

            $domains->sms_balance -= .30;
            $domains->sms_sent += 1;
            $domains->update();
        }

        return redirect()->route('admin.domain.request', $domains->domain_status);
    }

    public function domainRequestSearch(Request $request)
    {
        $search = '%' . $request->search . '%';
        $type = $request->type;

        $domains = Shop::query()
        ->select('id', 'domain_status', 'user_id', 'name', 'domain_request', 'domain_request_date', 'shop_id')
        ->where('domain_status', $type)
        ->whereHas('merchant', function ($q) use ($search) {
            $q->where('phone', 'LIKE', $search);
        })
        ->orWhere(function ($query) use ($search) {
            $query->where('domain_request', 'LIKE', $search)
            ->orWhere('shop_id', 'LIKE', $search);
        })
        ->with(['merchant' => function ($query) {
            $query->select('id', 'name', 'phone');
        }])
        ->orderByDesc('domain_request_date')
        ->paginate($this->limit(25));
        $userData = session('user_data');
        return view('panel.domain.index', compact('domains', 'type'), ['userData' => $userData]);
    }

    public function domainRequestUpdate(Request $request, $id)
    {
        $shop = Shop::query()
        ->select('id', 'domain_request', 'shop_id')
        ->find($id);
        $today = Carbon::today()->toDateString();

        $shop->domain_request = $request->domain_request;
        $shop->domain_request_date = $today;
        $shop->ssl_status = "pending";
        $shop->update();

        toastr()->success('Domain request updated');

        return redirect()->back();
    }

    public function refreshDomain($id)
    {
        $shop = Shop::query()
        ->select('id', 'domain_request', 'shop_id')
        ->find($id);

        $today = Carbon::today()->toDateString();

        $shop->domain_status = "pending";
        $shop->domain_request_date = $today;
        $shop->ssl_status = "pending";
        $shop->update();

        toastr()->success("{$shop->domain_request} refreshed successfully");

        return redirect()->back();
    }

    public function domainRequestStatusReject(Request $request)
    {
        $this->domainRequestStatusUpdate($request->id, $request->type, $request->rejected_reason);

        return redirect()->route('admin.domain.request', 'rejected');
    }
}
