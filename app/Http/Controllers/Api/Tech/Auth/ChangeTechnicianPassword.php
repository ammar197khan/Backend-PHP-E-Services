<?php

namespace App\Http\Controllers\Api\Tech\Auth;

use Hash;
use Validator;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChangeTechnicianPassword extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $tech = Technician::findOrFail($request->tech_id);
        $tech->password = Hash::make($request->password);
        $tech->save();

        return response()->json(msg($request, success(), 'password_changed'));
    }
}
