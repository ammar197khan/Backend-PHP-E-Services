<?php

namespace App\Http\Controllers\Api\Tech\Profile;

use Validator;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetTechnicianLocation extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'lat'     => 'required',
            'lng'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $tech = Technician::where('id', $request->tech_id)->first();
        $tech->lat = $request->lat;
        $tech->lng = $request->lng;
        $tech->save();

        return response()->json(msg($request, success(), 'updated'));
    }
}
