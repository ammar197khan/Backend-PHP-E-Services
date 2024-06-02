<?php

namespace App\Http\Controllers\Api\User\Auth;

use App\Models\Code;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VerifyUserConfirmationCode extends Controller
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
        Code::select('email', 'phone', 'code')
        ->where('code', $request->code)
        ->where('type', $request->type)
        ->where('role', 'user')
        ->where('expire_at', '>', now())
        ->first();

        if (!$check) {
            return response()->json(msg($request, failed(), 'invalid_code'));
        }

        if ($request->type == 'reset') {
            $data = User::where('email', $check->email)->orWhere('phone', $check->phone)->select('id', 'jwt')->first();
        } else {
            $data = User::where('email', $check->email)->orWhere('phone', $check->phone)->first();
      	    $data->update(['active' => 1]);
        }

        return response()->json(['status' => 'success', 'msg' => 'code matched', 'data' => $data]);

    }
}
