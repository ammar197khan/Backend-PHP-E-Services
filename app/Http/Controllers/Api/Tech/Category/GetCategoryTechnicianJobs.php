<?php

namespace App\Http\Controllers\Api\Tech\Category;

use Validator;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetCategoryTechnicianJobs extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'order_id' => 'required|exists:orders,id,completed,0|exists:orders,id,tech_id,'.$request->tech_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $order = Order::where('id', $request->order_id)->select('cat_id')->first();
        $levels = Category::where('parent_id', $order->cat_id)->select('id', $lang.'_name as title')->get();

        return response()->json($levels);
    }
}
