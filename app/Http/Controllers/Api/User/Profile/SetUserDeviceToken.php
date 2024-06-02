<?php

namespace App\Http\Controllers\Api\User\Profile;

use Validator;
use App\Models\UserToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetUserDeviceToken extends Controller
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

        // NOTE: THIS ENABLE NOTIFICATION FOR ONLY ONE DEVICE
        UserToken::updateOrcreate(
            ['user_id' => $request->user_id],
            ['token' => $request->token]
        );

        return response()->json(['status' => 'success', 'msg' => 'updated']);
    }
}
