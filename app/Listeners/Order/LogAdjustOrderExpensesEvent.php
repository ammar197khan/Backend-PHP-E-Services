<?php

namespace App\Listeners\Order;

use App\Events\Order\AdjustOrderExpensesEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class LogAdjustOrderExpensesEvent
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
     * @param  AdjustOrderExpensesEvent  $event
     * @return void
     */
    public function handle(AdjustOrderExpensesEvent $event)
    {
        $guard = active_guard();
        $admin = auth()->guard($guard)->user();
        $model = auth()->guard($guard)->getProvider()->getModel();

        DB::table('activity_logs')->insert([
            'name'         => 'Order Expenses Adjusted',
            'description'  => 'Order Expenses Adjusted',
            'subject_id'   => $event->order_id,
            'subject_type' => 'App\Models\Order',
            'causer_id'    => $admin->id,
            'causer_type'  => $model,
            'properties'   => null,
            'created_at'   => now(),
        ]);
    }
}
