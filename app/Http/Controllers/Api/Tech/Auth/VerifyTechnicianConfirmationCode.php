<?php

namespace App\Http\Controllers\Api\Tech\Auth;

use Validator;
use App\Models\Code;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VerifyTechnicianConfirmationCode extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:activation,reset',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        // TODO: VERFICATION REQUIRE EMAIL TOO @ reset, 4 Digits code has high propability to duplicated

        $check =
        Code::where('code', $request->code)
        ->where('type', $request->type)
        ->where('role', 'tech')
        ->select('email', 'code')
        ->first();

        if(!$check) {
            return response()->json(msg($request, failed(), 'invalid_code'));
        }

        if ($request->type == 'reset') {
            $data = Technician::where('email', $check->email)->select('id', 'jwt')->first();
        }

        if ($request->type == 'activation') {
            return 'not yet';
        }

        return response()->json(['status' => 'success', 'msg' => 'code matched', 'data' => $data]);


    }
}
