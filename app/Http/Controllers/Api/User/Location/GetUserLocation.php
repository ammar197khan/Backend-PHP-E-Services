<?php

namespace App\Http\Controllers\Api\User\Location;

use Validator;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserLocation AS UserLocationResource;

class GetUserLocation extends Controller
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

        $user_location = UserLocation::where('user_id',$request->user_id)->paginate(15);

        // $location = UserLocationResource::collection($user_location);
        return response()->json($user_location);
    }
}
