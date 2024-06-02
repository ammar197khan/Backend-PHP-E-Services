<?php

namespace App\Listeners\Order;

use App\Events\Order\CancelOrderEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class LogCancelOrderEvent implements ShouldQueue
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
     * @param  CancelOrderEvent  $event
     * @return void
     */
    public function handle(CancelOrderEvent $event)
    {
        $guard = active_guard();
        $admin = auth()->guard($guard)->user();
        $model = auth()->guard($guard)->getProvider()->getModel();

        DB::table('activity_logs')->insert([
            'name'         => 'Order Canceled',
            'description'  => 'Order Canceled',
            'subject_id'   => $event->order->id,
            'subject_type' => 'App\Models\Order',
            'causer_id'    => $admin->id,
            'causer_type'  => $model,
            'properties'   => null,
            'created_at'   => now(),
        ]);
    }

}
