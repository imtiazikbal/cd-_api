<?php

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Request;

/**
 * To determine the route active on url match
 *
 * @param string $uri
 * @return string
 *
 */
function activeMenu($uri = ''): string
{
    $active = '';

    if (Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri)) {
        $active = 'active';
    }

    return $active;
}

/**
 * @param $date
 * @return bool
 */
function diffrenceDate($date): bool
{
    $addedDayTime = \Carbon\Carbon::parse($date)->timezone(User::TIMEZONE)->addDay();
    $currentDate = \Carbon\Carbon::now()->timezone(User::TIMEZONE);

    if ($currentDate->gte($addedDayTime)) {
        return true;
    }

    return false;
}

function checkSubscription($id): bool
{
    $subscriptions = Subscription::query()->where('user_id', $id)->latest()->first();
    $currentDate = \Carbon\Carbon::now()->timezone(User::TIMEZONE)->toDateString();

    if ($subscriptions) {
        if ($subscriptions->next_due_date >= $currentDate) {
            return true;
        }

        return false;
    }

    return false;
}
