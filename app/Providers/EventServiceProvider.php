<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Order\NewOrderEvent' => [
            'App\Listeners\Order\LogNewOrderEvent',
        ],
        'App\Events\Order\CancelOrderEvent' => [
            'App\Listeners\Order\LogCancelOrderEvent',
        ],
        'App\Events\Order\FinishOrderEvent' => [
            'App\Listeners\Order\LogFinishOrderEvent',
        ],
        'App\Events\Order\AdjustOrderExpensesEvent' => [
            'App\Listeners\Order\LogAdjustOrderExpensesEvent',
        ],
        'App\Events\Order\StateOrderItemEvent' => [
            'App\Listeners\Order\LogStateOrderItemEvent',
        ],
        'App\Events\Order\StateOrderItemExceedLimitEvent' => [
            'App\Listeners\Order\LogStateOrderItemExceedLimitEvent',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
