<?php

namespace App\Http\Controllers\Api\User\Order;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\Order;
use App\Models\PushNotify;
use Validator;
use DB;

class SetPaymentType extends Controller
{
    public function __invoke(Request $request)
    {

        $rules = [
            'user_id'      => 'required|exists:users,id',
            'jwt'          => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id'     => 'required|exists:orders,id,user_id,'.$request->user_id,
            'payment_type' => 'required|in:cash,online',
        ];

        if($request->payment_type == 'online') {
            $rules['online_payment_type'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $lang = $request->header('lang');
        DB::table('payments')
        ->where('order_id', $request->order_id)
        ->insert([
            'user_id'             => $request->user_id,
            'order_id'            => $request->order_id,
            'transaction_id'      => null,
            'payment_type'        => $request->payment_type,
            'online_payment_type' => $request->online_payment_type,
            'paid_at'             => null,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        if($request->payment_type == 'online') {
            return response()->json(msg($request, success(), 'success'));
        }

        $order =
        DB::table('orders')
        ->where('id', $request->order_id)
        ->first();

        $ar_text  = 'Have you recieved cash from the customer?';
        $en_text  = 'هل تم استلام النقدية من العميل؟';
        $notification_type = "confirm_payment";

        UserNot::create([
            'type'     => 'confirm_payment',
            'user_id'  => $order->user_id,
            'order_id' => $request->order_id,
            'ar_text'  => 'Have you recieved cash from the customer?',
            'en_text'  => 'هل تم استلام النقدية من العميل؟',
        ]);

        $token =
        DB::table('tech_tokens')
        ->where('tech_id', $order->tech_id)
        ->pluck('token');

        $token = UserToken::where('user_id', $order->user_id)->pluck('token');

        PushNotify::tech_send($token, $ar_text, $en_text, $notification_type, $order->id, null, $order->total_amount, $lang);

        return response()->json(msg($request, success(), 'success'));
    }
}
