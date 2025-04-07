<?php

namespace App\Http\Controllers\API\V1\Client\SmsTemplate;

use App\Http\Controllers\Controller;
use App\Http\Requests\SmsTemplateRequest;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;

class SmsTemplateController extends Controller
{
    public function index(Request $request)
    {
        $smsTemplate = SmsTemplate::where('shop_id', $request->header('shop-id'))->get();

        return $this->sendApiResponse($smsTemplate, 'Smsm template messages');
    }

    public function updateOrstore(SmsTemplateRequest $request)
    {
        SmsTemplate::updateOrCreate([
            "shop_id" => $request->header('shop-id'),
            "module"  => $request->module,
            "type"    => $request->type,
        ], [
            "message" => $request->message,
        ]);

        return $this->sendApiResponse('', 'Sms template stored successfully');
    }

    public function delete(SmsTemplate $smsTemplate)
    {
        if(!$smsTemplate) {
            $this->sendApiResponse('', 'Sms template not found !');
        }
        $smsTemplate->delete();

        return $this->sendApiResponse('', 'Sms template deleted successfully');
    }
}
