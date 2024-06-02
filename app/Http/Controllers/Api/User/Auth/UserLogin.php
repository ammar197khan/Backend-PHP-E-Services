<?php

namespace App\Http\Controllers\Api\User\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserToken;
use Validator;
use Hash;
use Auth;

class UserLogin
{

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required',
            'token'    => 'required'
        ]);

        if ($validator->fails()) {
            return ['status' => 'failed', 'msg' => $validator->messages()];
        }

        $user =
        User::where('email', $request->email)
        ->select('id', 'active', 'jwt', 'company_id','type' ,  'email', 'password', 'lat', 'lng')->with(['company'=> function($q){
            $q->with('orderProcessType');
        }])
        ->first();

        if (!$user) {
          return response()->json(msg($request, failed(), 'invalid_email'));
        }

        if ($user->active == 0) {
            return response()->json(msg($request, not_active(), 'not_active'));
        }

        $check = Hash::check($request->password, $user->password);

        if (!$check) {
          return response()->json(msg($request, failed(), 'invalid_password'));
        }

        UserToken::updateOrcreate(
            ['user_id' => $user->id],
            ['token' => $request->token]
        );

        $user['lat'] = isset($user->lat) ? $user->lat : '';
        $user['lng'] = isset($user->lng) ? $user->lng : '';
        $orderProcessType = !empty($user->company) && !empty($user->company->orderProcessType) && !empty($user->company->orderProcessType->name)? $user->company->orderProcessType->name : "" ;
        $user->order_process = $orderProcessType;

        unset($user->email,$user->password);
        return ['status' => 'success', 'msg' => 'logged_in', 'data' => $user];
    }

}
