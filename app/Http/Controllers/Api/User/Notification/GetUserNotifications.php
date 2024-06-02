<?php

namespace App\Http\Controllers\Api\User\Notification;

use Validator;
use App\Models\UserNot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetUserNotifications extends Controller
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

        $lang = $request->header('lang');

        $nots =
        UserNot::where('user_id', $request->user_id)
        ->select('id','seen','type','order_id',$lang.'_text as text','created_at')
        ->latest()
        ->get();

        return response()->json($nots);
    }
}
