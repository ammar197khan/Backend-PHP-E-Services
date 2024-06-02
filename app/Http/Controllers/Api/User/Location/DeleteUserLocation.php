<?php

namespace App\Http\Controllers\Api\User\Location;

use App\Models\UserLocation;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteUserLocation extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:user_locations,id',
            'user_id'     => 'required|exists:users,id',
            'jwt'         => 'required|exists:users,jwt,id,' . $request->user_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        UserLocation::where('id', $request->location_id)->where('user_id', $request->user_id)->delete();

        return response()->json(msg($request, success(), 'deleted'));
    }
}
