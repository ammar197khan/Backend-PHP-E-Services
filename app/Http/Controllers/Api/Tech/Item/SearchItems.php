<?php

namespace App\Http\Controllers\Api\Tech\Item;

use DB;
use Validator;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchItems extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'search'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $provider_id =
        Technician::where('id', $request->tech_id)
        ->where('jwt', $request->jwt)
        ->select('provider_id')
        ->first()
        ->provider_id;

        $items = DB::table($provider_id.'_warehouse_parts')->where('active', 1)->where('count', '>', 0)->where(
            function ($q) use ($request) {
                $q->where('ar_name', 'like', '%'.$request->search.'%');
                $q->orWhere('en_name', 'like', '%'.$request->search.'%');
                $q->orWhere('code', 'like', '%'.$request->search.'%');
            }
        )->select('id', $lang.'_name as name', $lang.'_desc as desc', 'image', 'code', 'count', 'price')->paginate(30);

        foreach ($items as $item) {
            $item->image = 'http://'.$_SERVER['SERVER_NAME'].'/warehouses/'.$item->image;
        }

        return response()->json($items);
    }
}
