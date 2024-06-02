<?php

namespace App\Http\Controllers\Api\Tech\Profile;

use Hash;
use Validator;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateTechnicianProfile extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'ar_name'  => 'required',
            'en_name'  => 'required',
            'email'    => 'required',
            'phone'    => 'required',
            'password' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $email_check = Technician::where('id', '!=', $request->tech_id)->where('email', $request->email)->first();
        if ($email_check) {
            return response()->json(msg($request, failed(), 'email_exist'));
        }

        $phone_check = Technician::where('id', '!=', $request->tech_id)->where('phone', $request->phone)->first();
        if ($phone_check) {
            return response()->json(msg($request, failed(), 'phone_exist'));
        }

        $tech = Technician::where('id', $request->tech_id)->first();
        $tech->ar_name = $request->ar_name;
        $tech->en_name = $request->en_name;
        $tech->email   = $request->email;
        $tech->phone   = $request->phone;
        if ($request->password) {
            $tech->password = Hash::make($request->password);
        }
        $tech->save();

        return response()->json(msg($request, success(), 'updated'));
    }
}
