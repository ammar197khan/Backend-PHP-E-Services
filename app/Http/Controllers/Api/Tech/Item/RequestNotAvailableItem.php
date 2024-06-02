<?php

namespace App\Http\Controllers\Api\Tech\Item;

use DB;
use Validator;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\PushNotify;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestNotAvailableItem extends Controller
{
    public function __invoke(Request $request)
    {
        $provider_id = Technician::where('id', $request->tech_id)->where('jwt', $request->jwt)->select('provider_id')->first()->provider_id;
        $table = $provider_id . '_warehouse_requests';

        $validator = Validator::make($request->all(), [
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,' . $request->tech_id,
            'order_id' => 'required|exists:orders,id,completed,0|exists:orders,id,tech_id,'.$request->tech_id,
            'desc'     => 'required',
            'title'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $lang = $request->header('lang');
        DB::table($table)->insert([
            'order_id' => $request->order_id,
            'tech_id'  => $request->tech_id,
            'title'    => $request->title,
            'details'  => $request->desc,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // QUESTION: WHY FREE TECH ?
        // Technician::where('id', $request->tech_id)->update(['busy' => 0]);

        // QUESTION: WHY DISSOCIATE TECH FROM ORDER ?
        $order = Order::where('id', $request->order_id)->select('id', 'user_id', 'tech_id', 'type')->first();
        // $order->tech_id = null;
        $order->type = 're_scheduled';
        $order->save();

        $ar_text = 'في إنتظار الموافقة علي القطعة المطلوبة للطلب خاصتك,الرجاءالإنتظار';
        $en_text = 'The request for your order item is under approval,please wait';

        UserNot::create([
            'type'     => 'order',
            'user_id'  => $order->user_id,
            'order_id' => $request->order_id,
            'ar_text'  => $ar_text,
            'en_text'  => $en_text
        ]);

        $token = UserToken::where('user_id', $order->user_id)->pluck('token');
        PushNotify::user_send($token, $ar_text, $en_text, 'order', $request->order_id , null,  0, $lang);

        return response()->json(msg($request, success(), 'success'));
    }
}
