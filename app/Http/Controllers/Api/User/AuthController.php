<?php

namespace App\Http\Controllers\Api\User;

use Mail;
use Carbon\Carbon;
use App\Models\Code;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required',
                'password' => 'required',
                'token' => 'required'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $user = User::where('email', $request->email)->select('id', 'active', 'jwt', 'company_id', 'email', 'password', 'lat', 'lng')->first();

        if ($user) {
            if ($user->active == 0) {
                return response()->json(msg($request, not_active(), 'not_active'));
            }

            $check = Hash::check($request->password, $user->password);

            if ($check) {
                UserToken::updateOrcreate(
                    [
                        'user_id' => $user->id
                    ],
                    [
                        'token' => $request->token
                    ]
                );

                $user['lat'] = isset($user->lat) ? $user->lat : '';
                $user['lng'] = isset($user->lng) ? $user->lng : '';

                unset($user->email,$user->password);
                return response()->json(['status' => 'success', 'msg' => 'logged_in', 'data' => $user]);
            } else {
                return response()->json(msg($request, failed(), 'invalid_password'));
            }
        } else {
            return response()->json(msg($request, failed(), 'invalid_email'));
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'en_name' => 'required',
                'ar_name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'password' => 'required',
                'city' => 'required',
                'district' => 'required',
                'home_no' => 'required'
            ]
        );

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

        $user = User::create([
            'type' => 'individual',
            'jwt' => str_random(20),
            'active' => 0,
            'en_name' => $request->en_name,
            'ar_name' => $request->ar_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'city' => $request->city,
            'street' => $request->district,
            'building_no' => $request->home_no
        ]);

        $code = Code::updateOrcreate(
            [
                'email' => $user->email
            ],
            [
                'type' => 'activation',
                'role' => 'user',
                'code' => rand(1000, 9999),
                'expire_at' => Carbon::now()->addHour()->toDateTimeString()
            ]
        );

        $data = [
            'name' => $user->name,
            'subject' => 'Activation code in qareeb app ',
            'content' => $code->code
        ];


        Mail::send('email', $data, function ($message) use ($data,$user) {
            $message->from('support@8reeb.my-staff.net', 'Activation@Qareeb')
                ->to($user->email)
                ->subject('Activation account | Qareeb');
        });

        return response()->json(msg($request, success(), 'registered'));
    }


    public function splash(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
                'jwt' => 'required|exists:users,jwt,id,'.$request->user_id,
                'token' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }


        UserToken::updateOrcreate(
            [
                'user_id' => $request->user_id,
            ],
            [
                'token' => $request->token
            ]
        );

        return response()->json(['status' => 'success', 'msg' => 'updated']);
    }


    public function code_send(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'type' => 'required|in:activation,reset',
                'email' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $check_email = User::where('email', $request->email)->first();
        if (!$check_email) {
            return response()->json(msg($request, failed(), 'invalid_email'));
        }

        $code =
        Code::updateOrcreate(
            [
                'type' => $request->type,
                'role' => 'user',
                'email' => $request->email
            ],
            [
                'code' => rand(1000, 9000),
                'expire_at' => Carbon::now()->addHour()->toDateTimeString()
            ]
        );

        $body = "Reset Password Code : " . $code->code;
        Mail::raw($body, function ($message) use ($request) {
            $message->from('qbs@qreebs.com')
            ->subject('Qreeb - Reset Password Code')
            ->to($request->email);
        });

        return response()->json(msg($request, success(), 'code_sent'));
    }


    public function code_check(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'type' => 'required|in:activation,reset',
                'code' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $check = Code::where('code', $request->code)->where('type', $request->type)->where('role', 'user')->select('email', 'code')->first();

        if ($check) {
            if ($request->type == 'reset') {
                $data = User::where('email', $check->email)->select('id', 'jwt')->first();
            } else {
                return 'not yet';
            }

            return response()->json(['status' => 'success', 'msg' => 'code matched', 'data' => $data]);
        } else {
            return response()->json(msg($request, failed(), 'invalid_code'));
        }
    }


    public function password_change(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
                'jwt' => 'required|exists:users,jwt,id,'.$request->user_id,
                'password' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $user = User::where('id', $request->user_id)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(msg($request, success(), 'password_changed'));
    }


    public function profile(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
                'jwt' => 'required|exists:users,jwt,id,'.$request->user_id,
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }


        $user = User::where('id', $request->user_id)->select('ar_name', 'en_name', 'email', 'phone')->first();
        $user['phone'] = isset($user->phone) ? $user->phone : '';

        return response()->json($user);
    }


    public function profile_update(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
                'jwt' => 'required|exists:users,jwt,id,'.$request->user_id,
                'ar_name' => 'required',
                'en_name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'password' => 'sometimes'
            ]
        );

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


    public function set_location(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => 'required|exists:users,id',
                'jwt' => 'required|exists:users,jwt,id,'.$request->user_id,
                'lat' => 'required',
                'lng' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $user = User::where('id', $request->user_id)->first();
        $user->lat = $request->lat;
        $user->lng = $request->lng;
        $user->save();

        return response()->json(msg($request, success(), 'updated'));
    }
}
