<?php

namespace App\Http\Controllers\Api\Tech\Order;

use Validator;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Technician;
use App\Models\Collaboration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetTechnicianCompletedOrders extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $technician = Technician::find($request->tech_id);

        // GET PROVIDER PARTNERS
        $providers = Collaboration::where('company_id', $technician->company_id)->pluck('provider_id');
        $technician = Technician::where('id', $request->tech_id)->whereIn('provider_id', $providers)->first();
        $orders = Order::whereHas('track' , function($q) use($request){
            $q->where('technicain_id', '=', $request->tech_id);
        })
        ->where('completed', 1)
        ->orWhere('canceled', 1)
        ->select('id', 'type', 'user_id', 'completed', 'canceled', 'scheduled_at', 'created_at')
        ->latest()
        ->get();
        foreach ($orders as $order) {
            $user = $order->get_user($lang, $order->user_id);

            if($order->completed == 1){
                $order['completed'] = 1;
                $order['order_status'] = 'completed';
            }elseif($order->canceled == 1){
                $order['completed'] = 0;
                $order['order_status'] = 'canceled';
            }
            $order['type_text'] = $order->get_type($lang, $order->type);
            $order['user_name'] = $user->name;
            $order['user_phone'] = $user->phone;

            if ($order->type == 'urgent') {
                $date = $order->created_at->toDateTimeString();
            } elseif ($order->type == 'urgent') {
                $date = Carbon::parse($order->created_at)->toDateTimeString();
            } else {
                $date = Carbon::parse($order->scheduled_at)->toDateTimeString();
            }

            $order['date'] = Carbon::parse($date)->format('d-m-Y h:i:s');

            unset($order->scheduled_at,$order->created_at);
        }
        $orders = collect($orders);
        $orders->sortBy([
            'completed', 'asc',
         ]);

        $orders = $orders->toArray();
        //$busy = Technician::where('id',$request->tech_id)->select('busy')->first()->busy;
        $online = Technician::where('id', $request->tech_id)->select('online')->first()->online;

        return response()->json(['orders' => $orders, 'online' => $online]);
    }
}
