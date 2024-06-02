<?php

namespace App\Http\Controllers\Api\User\Order\Transaction;

use DB;
use Validator;
use App\Models\Order;
use App\Models\TechNot;
use App\Models\TechToken;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\External\Myfatoorah\Myfatoorah;

class ConfirmOnlineTransaction extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'             => 'required|exists:users,id',
            'jwt'                 => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id'            => 'required|exists:orders,id,user_id,'.$request->user_id,
            'online_payment_type' => 'required',
            'key'                 => 'required',
            'key_type'            => 'required|in:InvoiceId,PaymentId',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $order = Order::where('id', $request->order_id)->first();

        $payload = [
            'Key'     => $request->key,
            'KeyType' => $request->key_type
        ];

        // $result = Myfatoorah::call('GetPaymentStatus', $payload);
        // $invoiceStatus = $result['Data']['InvoiceStatus'];
        // $customerReference = $result['Data']['CustomerReference'];
        //
        // if($invoiceStatus != 'Paid' || $customerReference != $request->order_id) {
        //     return [
        //         'status'      => 'failed',
        //         'msg'         => 'Bad Transaction',
        //         'transaction' => $result
        //     ];
        // }

        DB::table('payments')
        ->where('order_id', $request->order_id)
        ->insert([
            'user_id'             => $request->user_id,
            'order_id'            => $request->order_id,
            'transaction_id'      => $request->key,
            'payment_type'        => 'online',
            'online_payment_type' => $request->online_payment_type,
            'paid_at'             => now(),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);


        // Notify User : rate
        $token   = TechToken::where('tech_id', $order->tech_id)->pluck('token');
        $en_text = 'Order has been paid online';
        $ar_text = 'تم دفع تكلفة الخدمة بالنظام الالكتروني';
        TechNot::create([
            'type'     => 'order_paid_online',
            'tech_id'  => $order->tech_id,
            'order_id' => $request->order_id,
            'ar_text'  => $ar_text,
            'en_text'  => $en_text
        ]);
        $tech = $order->get_tech_all($order->tech_id, $order->cat_id);
        PushNotify::tech_send($token, $ar_text, $en_text, 'order_paid_online', $order->id, $tech);

        $text['en'] = 'Order has been paid online';
        $text['ar'] = 'تم دفع تكلفة الخدمة بالنظام الالكتروني';

        return [
            'status'      => 'success',
            'msg'         => $request->header('lang') == 'ar' ? $text['ar'] : $text['en'],
            // 'transaction' => $result
        ];
    }
}
