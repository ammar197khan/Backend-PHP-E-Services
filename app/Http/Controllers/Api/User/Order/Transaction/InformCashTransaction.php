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

class InformCashTransaction extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id'  => 'required|exists:users,id',
            // 'jwt'      => 'required|exists:users,jwt,id,'.$request->user_id,
            // 'order_id' => 'required|exists:orders,id,user_id,'.$request->user_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $order = Order::where('id', $request->order_id)->first();
Order::where('id', $request->order_id)->update(['method_before_payment' => 'cash']);

        // Notify User : rate
        $token   = TechToken::where('tech_id', $order->tech_id)->pluck('token');
        $ar_text = 'عملية تأكيد استلام نقدية';
        $en_text = 'Confirm cash recieval';
        TechNot::create([
            'type'     => 'confirm_cash',
            'tech_id'  => $order->tech_id,
            'order_id' => $request->order_id,
            'ar_text'  => $ar_text,
            'en_text'  => $en_text
        ]);
        $tech = $order->get_tech_all($order->tech_id, $order->cat_id);
        PushNotify::tech_send($token, $ar_text, $en_text, 'confirm_cash', $order->id, null, $order->total_amount);

        return response()->json(msg($request, success(), 'confirm_cash')); 
    }
}
