<?php

namespace App\Http\Controllers\Api\User\Profile;

use Hash;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateUserProfile extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|exists:users,id',
            'jwt'      => 'required|exists:users,jwt,id,'.$request->user_id,
            'ar_name'  => 'required',
            'en_name'  => 'required',
            'email'    => 'required',
            'phone'    => 'required',
            'password' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $email_check = User::where('id', '!=', $request->user_id)->where('email', $request->email)->first();
        if ($email_check) {
            return response()->json(msg($request, failed(), 'email_exist'));
        }

        $phone_check = User::where('id', '!=', $request->user_id)->where('phone', $request->phone)->first();
        if ($phone_check) {
            return response()->json(msg($request, failed(), 'phone_exist'));
        }

        $user = User::where('id', $request->user_id)->first();
        $user->ar_name = $request->ar_name;
        $user->en_name = $request->en_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json(msg($request, success(), 'updated'));
    }
}
