<?php

namespace App\Listeners\Order;

use App\Events\Order\StateOrderItemExceedLimitEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class LogStateOrderItemExceedLimitEvent
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
     * @param  StateOrderItemExceedLimitEvent  $event
     * @return void
     */
    public function handle(StateOrderItemExceedLimitEvent $event)
    {
        $item_request_state = $event->item_request_state;
        $item_request =
        DB::table('order_tech_requests')
        ->where('id', $event->item_request_state->request_id)
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
            'description'  => "'$item->en_name' part $item_request_state->status by company",
            'subject_id'   => $item_request->order_id,
            'subject_type' => 'App\Models\Order',
            'causer_id'    => $admin->id,
            'causer_type'  => $model,
            'properties'   => json_encode([
                'item_request_state_id'  => $item_request_state->id,
                'order_tech_requests_id' => $item_request->id,
            ]),
            'created_at'   => now(),
        ]);
    }
}
