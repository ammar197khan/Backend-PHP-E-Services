<?php

namespace App\Http\Controllers\Api\User\Order;

use Validator;
use App\Models\Order;
use App\Models\TechNot;
use App\Models\TechToken;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Models\ProviderCategoryFee;
use App\Http\Controllers\Controller;

class CancelUserOrder extends Controller
{

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|exists:users,id',
            'jwt'      => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id' => 'required|exists:orders,id,user_id,'.$request->user_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $lang = $request->header('lang');

        // TODO: MUST VALIDATE IF USER CAN CANCEL ORDER

        $order = Order::findOrFail($request->order_id);

        $order->update([
            'canceled'    => 1,
            'canceled_by' => 'user',
            'type'        => 'canceled',
        ]);

        $ar_text = 'عذراً,لقد تم إلغاء الطلب من قبل المستخدم';
        $en_text = 'Sorry,the current order has been canceled by the user';

        TechNot::create([
            'type'     => 'order',
            'tech_id'  => $order->tech_id,
            'order_id' => $request->order_id,
            'ar_text'  => $ar_text,
            'en_text'  => $en_text
        ]);

        $token = TechToken::where('tech_id', $order->tech_id)->pluck('token');
        PushNotify::tech_send($token, $ar_text, $en_text, 'push', $request->order_id,  null,  0, $lang);

        return response()->json(msg($request, success(), 'success'));
    }

}
