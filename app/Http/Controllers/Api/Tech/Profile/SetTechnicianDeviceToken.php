<?php

namespace App\Http\Controllers\Api\Tech\Profile;

use Validator;
use App\Models\TechToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetTechnicianDeviceToken extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'token'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        // NOTE: THIS ENABLE NOTIFICATION FOR ONLY ONE DEVICE
        TechToken::updateOrcreate(
            ['tech_id' => $request->tech_id],
            ['token' => $request->token]
        );

        return response()->json(['status' => 'success', 'msg' => 'updated']);
    }
}
