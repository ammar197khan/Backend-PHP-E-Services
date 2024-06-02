<?php

namespace App\Http\Controllers\Api\User\Order;

use Validator;
use App\Models\UserNot;
use App\Models\OrderRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RateOrder extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'     => 'required|exists:users,id',
            'jwt'         => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id'    => 'required|exists:orders,id,user_id,'.$request->user_id,
            'appearance'  => 'required',
            'cleanliness' => 'required',
            'performance' => 'required',
            'commitment'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        OrderRate::create([
            'order_id'    => $request->order_id,
            'appearance'  => $request->appearance,
            'cleanliness' => $request->cleanliness,
            'performance' => $request->performance,
            'commitment'  => $request->commitment
        ]);

        UserNot::where('user_id', $request->user_id)->where('order_id', $request->order_id)->where('type', 'rate')->delete();

        return response()->json(msg($request, success(), 'success'));
    }
}
