<?php

namespace App\Http\Controllers\Api\Tech\Notification;

use Validator;
use App\Models\TechNot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetTechnicianNotifications extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id' => 'required|exists:technicians,id',
            'jwt'     => 'required|exists:technicians,jwt,id,'.$request->tech_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $nots =
        TechNot::where('tech_id', $request->tech_id)
        ->select('id', 'seen', 'type', 'order_id', $lang.'_text as text', 'created_at')
        ->latest()
        ->get();

        return response()->json($nots);
    }
}
