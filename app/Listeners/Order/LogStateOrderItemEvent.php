<?php

namespace App\Listeners\Order;

use App\Events\Order\StateOrderItemEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class LogStateOrderItemEvent
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
     * @param  StateOrderItemEvent  $event
     * @return void
     */
    public function handle(StateOrderItemEvent $event)
    {
        $item_request =
         DB::table('order_tech_requests')
         ->where('id', $event->order_tech_request_id)
         ->first();

        $item =
        DB::table($item_request->provider_id . '_warehouse_parts')
        ->where('id', $item_request->item_id)
        ->first();

        $guard = 'company';
        $admin = auth()->guard($guard)->user();
        $model = auth()->guard($guard)->getProvider()->getModel();

        DB::table('activity_logs')->insert([
            'name'         => 'Item Request Status Changed',
            'description'  => "'$item->en_name' part $item_request->status",
            'subject_id'   => $item_request->order_id,
            'subject_type' => 'App\Models\Order',
            'causer_id'    => company()->id,
            'causer_type'  => $model,
            'properties'   => json_encode(['order_tech_request_id' => $item_request->id]),
            'created_at'   => now(),
        ]);
    }
}
