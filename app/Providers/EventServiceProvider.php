<?php

namespace App\Providers;

use App\Events\ProductStockEvent;
use App\Events\SendSmsEvent;
use App\Events\SmsBalanceEvent;
use App\Events\TransactionEvent;
use App\Listeners\ProductStockEventListener;
use App\Listeners\SendSmsListener;
use App\Listeners\SmsBalanceEventListener;
use App\Listeners\TransactionListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ProductStockEvent::class => [
            ProductStockEventListener::class
        ],
        SmsBalanceEvent::class => [
            SmsBalanceEventListener::class
        ],
        SendSmsEvent::class => [
            SendSmsListener::class
        ],
        TransactionEvent::class => [
            TransactionListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
