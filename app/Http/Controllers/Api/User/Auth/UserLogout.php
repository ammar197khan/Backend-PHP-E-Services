<?php

namespace App\Http\Controllers\Api\User\Auth;

use Validator;
use App\Models\UserToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserLogout extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'jwt'     => 'required|exists:users,jwt,id,'.$request->user_id,
            'token'   => 'required',
        ]);

        if($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        UserToken::where('user_id', $request->user_id)
        ->where('token', $request->token)
        ->delete();

        return response()->json(['status' => 'success', 'msg' => 'updated']);
    }
}
