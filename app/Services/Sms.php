<?php

namespace App\Services;

use App\Models\SmsTemplate;
use App\Models\SupportTicket;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Sms
{
    protected string $smsBaseURL;
    protected $revesms;
    protected $elitbuzzsms;

    public function __construct()
    {
        if(config('sms.elitbuzzsms')){
            $this->smsBaseURL = config('sms.elitbuzz.server');
            $this->elitbuzzsms = config('sms.elitbuzzsms');
        }elseif(config('sms.revesms')) {
            $this->smsBaseURL = config('sms.revesms.server');
            $this->revesms = config('sms.revesms');
        }else {
            $this->smsBaseURL = null;
            $this->revesms = config('sms.revesms');
            $this->elitbuzzsms = config('sms.elitbuzzsms');
        }
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl($this->smsBaseURL)
            ->asJson();
    }

    public function send(string $phone, string $message): PromiseInterface|Response
    {
        if($this->elitbuzzsms){
            return $this->request()
            ->send('get', '?api_key=R600191764bb7124559542.64067010&type=text&contacts=' . $phone . '&senderid=8809612472872&msg=' . $message);
        }else{
            return $this->request()
            ->send('get', '?apikey=72baa8c544661129&secretkey=73e31089&callerID=1234&toUser=' . $phone . '&messageContent=' . $message);
        }
    }

    public function sendOtp(Model $user): PromiseInterface|Response
    {
        $otp = random_int(111111, 999999);
        $user->otp = $otp;
        $user->save();

        return $this->send($user->phone, $this->otpTemplate($otp));
    }

    public function identifyOtp(Model $order, Model $shop): PromiseInterface|Response
    {
        $otp = random_int(111111, 999999);
        $order->identify_otp = $otp;
        $order->save();

        return $this->send($order->phone, $this->checkoutTemplate($otp, $shop));
    }

    public function sendSmsForRegistration(Model $user, $shop): PromiseInterface|Response
    {
        return $this->send($user->phone, $this->registrationTemplate($shop));
    }

    public function sendSmsForSubscriptionReminder(Model $user, string $diff_days, string $expire_date): PromiseInterface|Response
    {
        return $this->send($user->phone, $this->subscriptionReminderTemplate($diff_days, $expire_date));
    }

    public function orderConfirmSms(string $phone, Model $order, Model $shop): PromiseInterface|Response
    {
        $customer = ucwords($order->customer_name);

        return $this->send($phone, $this->orderPlacedTemplate($customer, $order, $shop));
    }

    public function orderStatusUpdate(string $phone, Model $order, Model $shop): PromiseInterface|Response
    {
        $customer = ucwords($order->customer_name);

        return $this->send($phone, $this->orderStatusTemplate($customer, $order, $shop));
    }

    public function sendSmsForSupportTicket($phone, $ticket)
    {
        if($ticket->status == SupportTicket::OPENED) {
            return $this->send($phone, $this->supportTicketOpenTemplate($ticket->ticket_id));
        }

        if($ticket->status == SupportTicket::SOLVED) {
            return $this->send($phone, $this->supportTicketSolvedTemplate($ticket->ticket_id));
        }

        if($ticket->status == SupportTicket::PROCESSING) {
            return $this->send($phone, $this->supportTicketProcessTemplate($ticket->ticket_id));
        }
    }

    public function supportTicketOpenTemplate(string $ticketNo): string
    {
        return 'Thank you for submitting your ticket no : ' . $ticketNo . '. Our technical team is currently reviewing it, and you will receive an update soon.';
    }

    public function supportTicketSolvedTemplate(string $ticketNo): string
    {
        return 'Dear valued client, we are happy to inform you that (your ticket : ' . $ticketNo . '.) has been successfully resolved. If there is anything else you need help, please let us know. Thank you for your patience';
    }

    public function supportTicketProcessTemplate(string $ticketNo): string
    {
        return 'Dear valued client, we are happy to inform you that (your ticket : ' . $ticketNo . '.) has been processed. If there is anything else you need help, please let us know. Thank you for your patience';
    }

    public function otpTemplate(string $otp): string
    {
        return 'Your Funnel Liner OTP is ' . $otp . '%0a Do not share this with anyone.';
    }

    public function checkoutTemplate(string $otp, string $shop): string
    {
        return 'Use OTP: ' . $otp . ' to proceed with Checkout. %0a This OTP will be valid for 2 minutes. %0a Please do not share it with anyone.';
    }

    public function registrationTemplate(string $shop): string
    {
        return 'Welcome to Funnel Liner! Your registration is completed. Your Shop Id is: ' . $shop .
            'Please complete this step and run your automation journey with Funnel Liner: https://app.funnelliner.com/congratulation
        %0a For any queries: 09638888881';
    }

    public function subscriptionTemplate()
    {

    }

    public function subscriptionReminderTemplate(string $diff_days, string $expire_date): string
    {
        $msg = match ($diff_days) {
            '0' => 'left',
            '7', '4', '3', '2', '1' => 'overdue',
            default => 'days'
        };

        return "You have " . $diff_days . " days " . $msg . " to renew your Funnel Liner subscription.%0aDate of Renewal: " . $expire_date . ", Please renew your subscription to maintain continuous access.%0aThank You.";
    }

    public function orderPlacedTemplate(string $customer, Model $order, Model $shop): string
    {
        $smsTemplate = SmsTemplate::where('shop_id', $shop->shop_id)
        ->where('module', 'order')
        ->where('type', 'place')
        ->first();

        if($smsTemplate) {
            $dynamicMessage = $this->dynamicSmsTemplateMessages($shop, $order, $smsTemplate);

            return $dynamicMessage;
        } else {
            return 'Dear ' . ucwords($customer) . ',%0aYour Order No. ' . $order->order_no . ' is placed successfully.%0aThank you.%0a %0a'
            . ucwords($shop->name);
        }

    }

    public function orderStatusTemplate(string $customer, Model $order, Model $shop): string
    {
        $smsTemplate = SmsTemplate::where('shop_id', $shop->shop_id)
        ->where('module', 'order')
        ->where('type', $order->order_status)
        ->first();

        if($smsTemplate) {
            $dynamicMessage = $this->dynamicSmsTemplateMessages($shop, $order, $smsTemplate);

            return $dynamicMessage;
        } else {
            return 'Dear ' . ucwords($customer) . ',%0aYour Order No. ' . $order->order_no . ' is ' . $order->order_status . '.%0aThank you.%0a%0a'
            . ucwords($shop->name);
        }

    }

    public function dynamicSmsTemplateMessages(Model $shop, Model $order, Model $smsTemplate): string
    {
        $staticMessage = $smsTemplate->message;
        $customerName = $order->customer_name;
        $orderNo = $order->order_no;
        $orderStatus = $order->order_status;
        $shopName = ucwords(str_replace("-", " ", $shop->name));
        $breakSign = '%0a';

        $replaceDynamicData = str_replace(['{customerName}', '{orderNo}', '{break}', '{orderStatus}'], [$customerName, $orderNo, $breakSign,  $orderStatus], $staticMessage);
        $dynamicMessage = $replaceDynamicData . ' %0a %0a ' . $shopName;

        return $dynamicMessage;
    }

    public function domainRequestStatusUpdateMessage($shop)
    {
        $shopName = $shop->name;
        if($shop->reject_reason == null){
            $message = "Dear ".$shop->merchant->name .", Your domain ". $shop->domain_request ." is ".$shop->domain_status.'. %0a %0a'.$shopName;
        }else {
            $message = "Dear ".$shop->merchant->name .", Your domain ". $shop->domain_request ." is ".$shop->domain_status. ". The reason is ".$shop->reject_reason.'. %0a %0a'.$shopName;
        }
        
        return $this->send($shop->merchant->phone, $message);
    }
}