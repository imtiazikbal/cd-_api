<?php

namespace App\Jobs;

use App\Enums\DateFormat;
use App\Models\Subscription;
use App\Notifications\SubscriptionNotification;
use App\Services\Sms;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Traits\sendApiResponse;

class SubscriptionReminder implements ShouldQueue
{
    use Dispatchable;

    use InteractsWithQueue;

    use Queueable;

    use SerializesModels;

    use sendApiResponse;

    /**
     * Execute the job.
     *
     * @return JsonResponse
     */
    public function handle()
    {
        $sms = new Sms();
        $subscriptions = Subscription::with('user')
            ->where('status', Subscription::PAID)
            ->get();

        foreach ($subscriptions as $singleSubscription) {

            $todayDate = Carbon::today();
            $dueWithExtraDay = Carbon::parse($singleSubscription->next_due_date)->addDays(7);
            $expireDate = Carbon::parse($singleSubscription->next_due_date)->format(DateFormat::DATEFORMATWITHCOMMA);
            $daysDiff = $todayDate->diffInDays($dueWithExtraDay);

            if ($dueWithExtraDay->isFuture()) {
                if ($daysDiff === 7) {
                    $sms->sendSmsForSubscription($singleSubscription->user, $daysDiff, $expireDate);

                    $subscriptionCheck = $this->callUnpaidSubscriptionUserQuery($singleSubscription->user_id);

                    if (!$subscriptionCheck) {
                        $subscription = Subscription::query()->create([
                            'user_id'       => $singleSubscription->user_id,
                            'invoice_num'   => Str::random(8),
                            'amount'        => $singleSubscription->amount,
                            'gateway'       => '',
                            'sub_gateway'   => '',
                            'gateway_trxid' => '',
                            'api_response'  => json_encode(''),
                            'status'        => Subscription::UNPAID,
                            'next_due_date' => $singleSubscription->next_due_date,
                            'created_at'    => $singleSubscription->created_at,
                        ])->load('user');

                        $subscription['daysDiff'] = $daysDiff;
                        $singleSubscription->user->notify(new SubscriptionNotification($subscription));
                    }
                } elseif ($daysDiff === 4) {
                    $sms->sendSmsForSubscription($singleSubscription->user, $daysDiff, $expireDate);

                    $subscription = $this->callUnpaidSubscriptionUserQuery($singleSubscription->user_id);
                    $subscription['daysDiff'] = $daysDiff;

                    $singleSubscription->user->notify(new SubscriptionNotification($subscription));
                } elseif ($daysDiff === 2) {
                    $sms->sendSmsForSubscription($singleSubscription->user, $daysDiff, $expireDate);

                    $subscription = $this->callUnpaidSubscriptionUserQuery($singleSubscription->user_id);
                    $subscription['daysDiff'] = $daysDiff;

                    $singleSubscription->user->notify(new SubscriptionNotification($subscription));
                } elseif ($daysDiff === 1) {
                    $sms->sendSmsForSubscription($singleSubscription->user, $daysDiff, $expireDate);

                    $subscription = $this->callUnpaidSubscriptionUserQuery($singleSubscription->user_id);
                    $subscription['daysDiff'] = $daysDiff;

                    $singleSubscription->user->notify(new SubscriptionNotification($subscription));
                }
            }
        }

        return $this->sendApiResponse('', "Send sms & email successfully");
    }

    public function callUnpaidSubscriptionUserQuery($user_id)
    {
        return Subscription::with('user')->where('user_id', $user_id)
            ->where('status', Subscription::UNPAID)
            ->first();
    }
}
