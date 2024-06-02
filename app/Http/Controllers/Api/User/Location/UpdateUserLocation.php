<?php

namespace App\Http\Controllers\Api\User\Location;

use Validator;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateUserLocation extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:locations,id',
            'user_id'     => 'required|exists:users,id',
            'jwt'         => 'required|exists:users,jwt,id,' . $request->user_id,
            'name'        => 'required',
//            'lat'         => 'required',
//            'lng'         => 'required',
//            'address'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        UserLocation::where('id', $request->location_id)->where('user_id', $request->user_id)->update([
//            'lat'       => $request->lat,
//            'lng'       => $request->lng,
//            'address'   => $request->address,
            'name'      => $request->name,
        ]);

        return response()->json(msg($request, success(), 'updated'));
    }
}
