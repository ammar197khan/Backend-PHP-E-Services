<?php

namespace App\Http\Controllers\Api\User\Profile;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GetUserProfile extends Controller
{

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'jwt'     => 'required|exists:users,jwt,id,'.$request->user_id,
        ]);

        if($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $user =
        User::select('id', 'ar_name', 'en_name', 'email', 'phone')
        ->where('id', $request->user_id)
        ->first();

        $user['phone'] = isset($user->phone) ? $user->phone : '';

        return response()->json($user);
    }
}
