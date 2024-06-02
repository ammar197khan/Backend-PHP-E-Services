<?php

namespace App\Http\Controllers\Api\Tech\Item;

use Validator;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\Technician;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Models\OrderItemUser;
use App\Models\OrderTracking;
use App\Http\Controllers\Controller;

class RequestOrderItemApprovalForUser extends Controller
{
    public function __invoke(Request $request)
    {
        $provider_id = Technician::where('id', $request->tech_id)->where('jwt', $request->jwt)->select('provider_id')->first()->provider_id;
        $table = $provider_id.'_warehouse_parts';

        $validator = Validator::make($request->all(), [
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'order_id' => 'required|exists:orders,id,completed,0|exists:orders,id,tech_id,'.$request->tech_id,
            'item_id'  => 'required|exists:'.$table.',id',
            'taken'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $lang = $request->header('lang');
        $user_id = Order::where('id', $request->order_id)->select('user_id')->first()->user_id;

        OrderItemUser::create([
            'order_id'    => $request->order_id,
            'user_id'     => $user_id,
            'item_id'     => $request->item_id,
            'provider_id' => $provider_id,
            'taken'       => $request->taken
        ]);

        $ar_text = 'تم إضافة قطعة جديدة للطلب خاصتك من قبل الفني,الرجاء التحقق';
        $en_text = 'There is a new part added by the technician for your order,please check !';

        UserNot::create([
            'type'     => 'order',
            'user_id'  => $user_id,
            'order_id' => $request->order_id,
            'ar_text'  => $ar_text,
            'en_text'  => $en_text
        ]);

        $token = UserToken::where('user_id', $user_id)->pluck('token');
        PushNotify::user_send($token, $ar_text, $en_text, 'order', $request->order_id,  null,  0, $lang );

        OrderTracking::create([
            'order_id' => $request->order_id,
            'status'   => 'Spare parts ordered',
            'date'     => Carbon::now(),
            'technicain_id' => $request->tech_id
        ]);

        return response()->json(msg($request, success(), 'please_wait_user_approval'));
    }
}
