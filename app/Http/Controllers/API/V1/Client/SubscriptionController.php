<?php

namespace App\Http\Controllers\API\V1\Client;

use App\Enums\DateFormat;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Traits\sendApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use sendApiResponse;

    public function index(Request $request): JsonResponse
    {
        $subscription = Subscription::query()->where('user_id', $request->header('id'))->latest()->get();

        return $this->sendApiResponse($subscription);
    }

    public function subscriptionPdfDownload($id)
    {
        $subscription = Subscription::with('user')->where('user_id', request()->header('id'))->where('id', $id)->first();

        if (!$subscription) {
            return $this->sendApiResponse('', 'Data not found !');
        }
        $create_date = Carbon::parse($subscription->created_at)->format(DateFormat::DATEFORMAT);
        $expire_date = Carbon::parse($subscription->next_due_date)->format(DateFormat::DATEFORMAT);
        $today_date = Carbon::parse(now())->format(DateFormat::DATEFORMAT);

        // invoice pdf create
        $html = view('panel.pdf.subscription_pdf', compact('subscription'));
        $pdf = Pdf::loadHTML($html);

        return $pdf->download('document.pdf');
    }
}
