<?php

namespace App\Http\Controllers\Api\Tech\Profile;

use Validator;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetTechnicianProfile extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $tech =
        Technician::where('id', $request->tech_id)
        ->select('ar_name', 'en_name', 'email', 'phone', 'busy', 'rotation_id')
        ->with('rotation')
        ->first();

        $tech['phone'] = isset($tech->phone) ? $tech->phone : '';

        return response()->json($tech);
    }
}
