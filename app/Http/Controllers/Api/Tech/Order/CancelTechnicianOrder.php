<?php

namespace App\Http\Controllers\Api\Tech\Order;

use Validator;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\PushNotify;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use App\Models\ProviderCategoryFee;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class CancelTechnicianOrder extends Controller
{
    public function __invoke(Request $request)
    {
        if(!isset($request->deleted_by)){

        $validator = Validator::make($request->all(), [
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,' . $request->tech_id,
            'order_id' => 'required|exists:orders,id,tech_id,' . $request->tech_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
    }

        $lang = $request->header('lang');

        $order = Order::findOrFail($request->order_id);
        $order->update([
            'type'        => 'canceled',
            'canceled'    => 1,
            'canceled_by' => 'tech',
        ]);
        OrderTracking::create([
            'order_id' => $order->id,
            'status'   => 'Service Request Rejected',
            'date'     => Carbon::now(),
            'technicain_id' => $request->tech_id
        ]);

        if(!isset($request->deleted_by)){
        $ar_text = 'عذراً,لقد تم إلغاء الطلب من قبل الفني';
        $en_text = 'Sorry,the current order has been canceled by the Technician';
        }else{
            $ar_text = 'عذراً,لقد تم إلغاء الطلب من قبل الفني';
            $en_text = 'Sorry,the current order has been canceled by the Supervisor';
        }
        UserNot::create([
            'type'     => 'push',
            'user_id'  => $order->user_id,
            'order_id' => $request->order_id,
            'ar_text'  => $ar_text,
            'en_text'  => $en_text
        ]);

        $token = UserToken::where('user_id', $order->user_id)->pluck('token');
        if(isset($request->tech_id)){
            PushNotify::user_send($token, $ar_text, $en_text, 'push', $request->order_id, null,  0, $lang);
        }


        return response()->json(msg($request, success(), 'success'));
    }
}
