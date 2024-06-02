<?php

namespace App\Http\Controllers\Api\Tech\Order;

use DB;
use Validator;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\External\Myfatoorah\Myfatoorah;

class ConfirmCashRecieval extends Controller
{
    public function __invoke(Request $request)
    {
        $rules = [
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'order_id' => 'required|exists:orders,id,tech_id,'.$request->tech_id,
            'is_paid'  => 'required|required|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $lang = $request->header('lang');

        $order = Order::where('id', $request->order_id)->first();

        DB::table('payments')
        ->where('order_id', $request->order_id)
        ->insert([
            'user_id'             => $order->user_id,
            'order_id'            => $request->order_id,
            'transaction_id'      => $request->key,
            'payment_type'        => 'cash',
            'online_payment_type' => null,
            'paid_at'             => $request->is_paid ? now() : null,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        if($request->is_paid) {
            $ar_text = 'تم إنهاء الطلب بنجاح,الرجاء التقييم';
            $en_text = 'Order has been completed successfully,please rate';
            $notification_type = 'rate';
            UserNot::create([
                'type'     => $notification_type,
                'user_id'  => $order->user_id,
                'order_id' => $request->order_id,
                'ar_text'  => $ar_text,
                'en_text'  => $en_text
            ]);
        } else {
            $ar_text = 'الفني لم يستلم تكلفة الخدمة';
            $en_text = 'Technician has not recieved cash';
            $notification_type = 'cash_not_recieved';
            UserNot::create([
                'type'     => $notification_type,
                'user_id'  => $order->user_id,
                'order_id' => $request->order_id,
                'ar_text'  => $ar_text,
                'en_text'  => $en_text
            ]);
        }
        $token =
        DB::table('user_tokens')
        ->where('user_id', $order->user_id)
        ->pluck('token');
        $tech = $order->get_tech_all($order->tech_id, $order->cat_id);
        PushNotify::user_send($token, $ar_text, $en_text, $notification_type, $order->id, $tech, $order->total_amount,$lang);

        $state_en = $request->is_paid ? '' : 'not';
        $state_ar = $request->is_paid ? 'استلم' : 'لم يستلم';
        $text['en'] = "Technician inform cash $state_en recieved";
        $text['ar'] = "الفني بلغ انه " . $state_ar . " تكلفة الخدمة ";

        return [
            'status'      => 'success',
            'msg'         => $request->header('lang') == 'ar' ? $text['ar'] : $text['en'],
        ];
    }

}
