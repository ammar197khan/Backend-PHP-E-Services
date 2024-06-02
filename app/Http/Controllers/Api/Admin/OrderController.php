<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItemUser;
use App\Models\OrderTechRequest;
use App\Models\ItemRequestState;
use App\Models\OrderTracking;
use Carbon\Carbon;
use DB;

class OrderController extends Controller
{
    public function approveItem(Request $request, $id)
    {
        $initialItemRequest = OrderItemUser::findOrFail($id);
        $order = Order::findOrFail($initialItemRequest->order_id);
        $item = DB::table($initialItemRequest->provider_id . '_warehouse_parts')->find($initialItemRequest->item_id);

        $order_tech_request_id =
        DB::table('order_tech_requests')->insertGetId([
            'order_id'    => $initialItemRequest->order_id,
            'provider_id' => $initialItemRequest->provider_id,
            'item_id'     => $initialItemRequest->item_id,
            'taken'       => $initialItemRequest->taken,
            'status'      => $request->status,
            'desc'        => null,
        ]);

        event(new \App\Events\Order\StateOrderItemEvent($order_tech_request_id));

        if($request->status == 'confirmed'){
            $order->item_total = $order->item_total + ($initialItemRequest->taken * $item->price);
            $order->type       = 're_scheduled';
            $order->save();
            if($initialItemRequest->taken > $item->count){
                DB::table($initialItemRequest->provider_id . '_warehouse_parts')
                ->where('id', $initialItemRequest->item_id)
                ->update(['count' => ($item->count - $initialItemRequest->taken)]);
            } else {
                DB::table($initialItemRequest->provider_id . '_warehouse_parts')
                ->where('id', $initialItemRequest->item_id)
                ->update(['requested_count' => ($item->requested_count + $initialItemRequest->taken)]);
            }

            OrderTracking::create([
                'order_id' => $order->id,
                'status' => 'Spare parts approved',
                'date' => Carbon::now()
            ]);
        }

        $initialItemRequest->delete();

        return ['message' => 'ok'];
    }
}
