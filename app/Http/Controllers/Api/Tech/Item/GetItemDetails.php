<?php

namespace App\Http\Controllers\Api\Tech\Item;

use DB;
use Validator;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetItemDetails extends Controller
{
    public function __invoke(Request $request)
    {
        $provider_id = Technician::where('id', $request->tech_id)->where('jwt', $request->jwt)->select('provider_id')->first()->provider_id;
        $table = $provider_id . '_warehouse_parts';

        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,' . $request->tech_id,
            'item_id' => 'required|exists:' . $table . ',id,active,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');
        $item = DB::table($provider_id.'_warehouse_parts')->where('id', $request->item_id)->select('id', $lang.'_name as name', $lang.'_desc as desc', 'image', 'code', 'count', 'price')->first();
        $item->image = 'http://'.$_SERVER['SERVER_NAME'].'/warehouses/'.$item->image;

        return response()->json($item);
    }
}
