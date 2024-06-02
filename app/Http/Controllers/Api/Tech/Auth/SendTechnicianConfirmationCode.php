<?php

namespace App\Http\Controllers\Api\Tech\Auth;

use Mail;
use Validator;
use Carbon\Carbon;
use App\Models\Code;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Services\External\SMS;
use App\Http\Controllers\Controller;

class SendTechnicianConfirmationCode extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'  => 'required|in:activation,reset',
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $check_email = Technician::where('email',$request->email)->orWhere('phone', $request->phone)->first();

        if(!$check_email) {
            return response()->json(msg($request, failed(), 'invalid_contact'));
        }

        $code = Code::updateOrcreate(
            [
                'type' => $request->type,
                'role' => 'tech',
                'email' => $request->email,
                'phone' => $request->phone
            ],
            [
                'code' => rand(1000, 9000),
                'expire_at' => Carbon::now()->addHour()->toDateTimeString()
            ]
        );

        $body = "Confirmation Code: " . $code->code;

        if($request->has('email')) {
            Mail::raw($body, function ($message) use ($request){
              $message->to($request->email)
              ->subject('Qreeb - Confirmation Code');
            });
        }

        if($request->has('phone')) {
            SMS::send($request->phone, $body);
        }

        return response()->json(msg($request, success(), 'code_sent'));
    }
}
