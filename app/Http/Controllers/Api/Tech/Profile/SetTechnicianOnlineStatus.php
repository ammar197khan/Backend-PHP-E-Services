<?php

namespace App\Http\Controllers\Api\Tech\Profile;

use Validator;
use App\Models\Order;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetTechnicianOnlineStatus extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'status'  => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $orders = Order::where('tech_id', $request->tech_id)->where('completed', 0)->count();

        if ($request->status == 0 && $orders > 0) {
            return response()->json(msg($request, error(), 'orders_not_completed'));
        }

        $tech = Technician::where('id', $request->tech_id)->first();
        $tech->online = $request->status;
        $tech->save();

        return response()->json(msg($request, success(), 'updated'));
    }
}
