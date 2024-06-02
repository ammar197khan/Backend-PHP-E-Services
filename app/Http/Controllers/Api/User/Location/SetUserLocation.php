<?php

namespace App\Http\Controllers\Api\User\Location;

use App\Models\UserLocation;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetUserLocation extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
            'jwt'           => 'required|exists:users,jwt,id,' . $request->user_id,
            'lat'           => 'required',
            'lng'           => 'required',
            'address'       => 'required',
            'name'          => 'required',
            'city'          => 'required',
            'camp'          => 'required',
            'street'        => 'required',
            'plot_no'       => 'required',
            'block_no'      => 'required',
            'building_no'   => 'required',
            'apartment_no'  => 'required',
            'house_type'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        UserLocation::create([
            'user_id'       => $request->user_id,
            'lat'           => $request->lat,
            'lng'           => $request->lng,
            'address'       => $request->address,
            'name'          => $request->name,
            'is_default '   => $request->is_default ,
            'city'          => $request->city,
            'camp'          => $request->camp,
            'street'        => $request->street,
            'plot_no'       => $request->plot_no,
            'block_no'      => $request->block_no,
            'building_no'   => $request->building_no,
            'apartment_no'  => $request->apartment_no,
            'house_type'    => $request->house_type,
        ]);

        return response()->json(msg($request, success(), 'success'));
    }
}
