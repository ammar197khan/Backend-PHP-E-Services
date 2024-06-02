<?php

namespace App\Http\Controllers\Api\Tech\Profile;

use Validator;
use App\Models\Order;
use App\Models\OrderRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetTechnicianRate extends Controller
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

        $orders = Order::where('tech_id', $request->tech_id)->pluck('id');

        $rates = OrderRate::whereIn('order_id', $orders)->get();

        $arr['appearance']  = $rates->pluck('appearance')->avg();
        $arr['cleanliness'] = $rates->pluck('cleanliness')->avg();
        $arr['performance'] = $rates->pluck('performance')->avg();
        $arr['commitment']  = $rates->pluck('commitment')->avg();


        return response()->json($arr);
    }
}
