<?php

namespace App\Http\Controllers\Api\User\Notification;

use Validator;
use App\Models\UserNot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetUserNotificationAsSeen extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'jwt'     => 'required|exists:users,jwt,id,'.$request->user_id,
            'not_id'  => 'required|exists:user_nots,id,user_id,'.$request->user_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        UserNot::where('id', $request->not_id)->update(['seen' => 1]);

        return response()->json(['status' => 'success', 'msg' => 'notification is seen successfully']);
    }
}
