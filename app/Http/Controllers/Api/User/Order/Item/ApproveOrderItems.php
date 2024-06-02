<?php

namespace App\Http\Controllers\Api\User\Order\Item;

use DB;
use Validator;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Models\OrderItemUser;
use App\Models\OrderTracking;
use App\Http\Controllers\Controller;

class ApproveOrderItems extends Controller
{
    public function __invoke(Request $request)
    {
        $initialItemRequests = OrderItemUser::whereIn('id', $request->ids)->get();
        $lang = $request->header('lang');
        foreach ($initialItemRequests as $initialItemRequest) {
            $order = Order::findOrFail($initialItemRequest->order_id);
            $item = DB::table($initialItemRequest->provider_id . '_warehouse_parts')->find($initialItemRequest->item_id);

            DB::table('order_tech_requests')->insert([
                'order_id'    => $initialItemRequest->order_id,
                'provider_id' => $initialItemRequest->provider_id,
                'item_id'     => $initialItemRequest->item_id,
                'taken'       => $initialItemRequest->taken,
                'status'      => 'confirmed',
                'desc'        => null,
            ]);

            if ($request->status == 'confirmed') {
                $order->item_total = $order->item_total + ($initialItemRequest->taken * $item->price);
                $order->type       = 're_scheduled';
                $order->save();
                if ($initialItemRequest->taken > $item->count) {
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
        }

        $ar_text = 'تم الموافقة علي القطع المطلوبة للطلب خاصتك,الرجاء تحديد موعد لإعادة الزيارة';
        $en_text = 'The request for your order items is approved,please select the reschedule time';

        UserNot::create([
            'type'     => 'time',
            'user_id'  => $request->user_id,
            'order_id' => $request->order_id,
            'ar_text'  => $ar_text,
            'en_text'  => $en_text
        ]);

        $token = UserToken::where('user_id', $request->user_id)->pluck('token');
        PushNotify::user_send($token, $ar_text, $en_text, 'time', $request->order_id, null, 0, $lang);

        return response()->json(msg($request, success(), 'please_wait'));
    }
}
