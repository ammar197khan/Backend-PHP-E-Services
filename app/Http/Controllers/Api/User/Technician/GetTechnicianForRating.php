<?php

namespace App\Http\Controllers\Api\User\Technician;

use Validator;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetTechnicianForRating extends Controller
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

        $order = Order::where('id', $request->order_id)->select('tech_id', 'cat_id')->first();
        $tech = $order->get_tech_lang($lang, $order->tech_id, $order->cat_id);

        return response()->json($tech);
    }
}
