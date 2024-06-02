<?php

namespace App\Http\Controllers\Api\User\Technician;

use Validator;
use App\Models\User;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetTechnicianDetails extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'jwt'     => 'required|exists:users,jwt,id,' . $request->user_id,
            'cat_id'  => 'required|exists:categories,id,type,2',
            'tech_id' => 'required|exists:technicians,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $user = User::where('id', $request->user_id)->select('lat', 'lng')->first();

        $tech = Technician::where('id', $request->tech_id)->select('id', 'provider_id', $lang . '_name as name', 'cat_ids', 'image', 'lat', 'lng', 'rotation_id')->first();

        $tech['categories'] = $tech->get_categories($lang, $tech->cat_ids);
        $tech['rate'] = $tech->get_all_rate($tech->id);
        $tech['image'] = 'http://' . $_SERVER['SERVER_NAME'] . '/public/providers/technicians/' . $tech->image;
        $tech['distance'] = $tech->get_distance($tech, $user);
        $tech['fee'] = $tech->get_service_fee($tech->provider_id, $request->cat_id);

        return response()->json($tech);
    }
}
