<?php

namespace App\Http\Controllers\Api\User\Auth;

use Carbon\Carbon;
use App\Models\Code;
use App\Models\User;
use App\Models\Company;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Services\External\SMS;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterUser extends Controller
{

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'en_name'  => 'required',
            'ar_name'  => 'required',
            'email'    => 'required|email',
            'phone'    => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }


        $email = User::where('email', $request->email)->select('id')->first();
        if ($email) {
            return response()->json(msg($request, failed(), 'email_exist'));
        }

        $phone = User::where('phone', $request->phone)->select('id')->first();
        if ($phone) {
            return response()->json(msg($request, failed(), 'phone_exist'));
        }

        $company = Company::where('en_name', 'individuals')->select('id')->first();

        $user = User::create([
              'type'        => 'individual',
              'jwt'         => str_random(20),
              'active'      => 0,
              'en_name'     => $request->en_name,
              'ar_name'     => $request->ar_name,
              'email'       => $request->email,
              'phone'       => $request->phone,
              'password'    => Hash::make($request->password),
              'company_id'  => $company->id,
        ]);

        $code =Code::updateOrcreate(
            [
                'type'  => 'activation',
                'role'  => 'user',
                'email' => $request->email,
                'phone' => $request->phone
            ],
            [
                'code' => rand(1000,9999),
                'expire_at' => Carbon::now()->addHour()->toDateTimeString()
            ]
        );

        $body = "Confirmation Code: " . $code->code;
        SMS::send($request->phone, $body);

        return response()->json(msg($request, success(), 'registered'));
    }
}
