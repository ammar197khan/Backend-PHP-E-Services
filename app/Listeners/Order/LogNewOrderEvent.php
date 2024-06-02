<?php

namespace App\Listeners\Order;

use App\Events\Order\NewOrderEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class LogNewOrderEvent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewOrderEvent  $event
     * @return void
     */
    public function handle(NewOrderEvent $event)
    {
        $guard = active_guard();
        $admin = auth()->guard($guard)->user();
        $model = auth()->guard($guard)->getProvider()->getModel();

        DB::table('activity_logs')->insert([
            'name'         => 'Order Created',
            'description'  => 'Order Created',
            'subject_id'   => $event->order->id,
            'subject_type' => 'App\Models\Order',
            'causer_id'    => $admin->id,
            'causer_type'  => $model,
            'properties'   => null,
            'created_at'   => now(),
        ]);
    }
}
