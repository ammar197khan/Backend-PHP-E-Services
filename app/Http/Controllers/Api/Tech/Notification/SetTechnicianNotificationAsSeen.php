<?php

namespace App\Http\Controllers\Api\Tech\Notification;

use Validator;
use App\Models\TechNot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetTechnicianNotificationAsSeen extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'not_id'  => 'required|exists:tech_nots,id,tech_id,'.$request->tech_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        TechNot::where('id', $request->not_id)->update(['seen' => 1]);

        return response()->json(['status' => 'success', 'msg' => 'notification is seen successfully']);
    }
}
