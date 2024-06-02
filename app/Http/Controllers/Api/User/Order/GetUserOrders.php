<?php

namespace App\Http\Controllers\Api\User\Order;

use Validator;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetUserOrders extends Controller
{

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'jwt'     => 'required|exists:users,jwt,id,' . $request->user_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $orders =
        Order::where('user_id', $request->user_id)
        ->select('id', 'type', 'tech_id', 'completed', 'canceled', 'scheduled_at', 'created_at')
        ->latest()
        ->get();

        foreach ($orders as $order) {
            $tech = $order->get_tech($lang, $order->tech_id);
            $order['type_text']  = $order->get_type($lang, $order->type);
            $order['tech_name']  = isset($order->tech_id) ? $tech->name : '';
            $order['tech_phone'] = isset($order->tech_id) ? $tech->phone : '';
            $order['tech_id']    = isset($order->tech_id) ? $order->tech_id : 0;

            if ($order->type == 'urgent') {
                $date = $order->created_at->toDateTimeString();
            } else {
                $date = Carbon::parse($order->scheduled_at)->toDateTimeString();
            }

            $order['date'] = $date;

            unset($order->scheduled_at, $order->created_at);
        }

        return response()->json($orders);
    }
    
}
