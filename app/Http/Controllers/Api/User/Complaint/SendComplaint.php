<?php

namespace App\Http\Controllers\Api\User\Complaint;

use Validator;
use App\Models\Complain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SendComplaint extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_id' => 'required|exists:complain_titles,id',
            'user_id'  => 'required|exists:users,id',
            'desc'     => 'required',
        ]);

        if($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        // TODO:  SHOULD AUTHed IF COMPLAINT WILL ASSIGNED TO THE USER

        Complain::create ([
            'title_id' => $request->title_id,
            'user_id'  => $request->user_id,
            'desc'     => $request->desc
        ]);

        return response()->json(msg($request,success(),'success'));
    }
}
