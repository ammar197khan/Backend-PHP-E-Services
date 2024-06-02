<?php

namespace App\Http\Controllers\Api\Tech\Auth;

use App\Models\Company;
use App\Models\Provider;
use Hash;
use Validator;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TechnicianRegister extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'en_name'  => 'required',
            'ar_name'  => 'required',
            'email'    => 'required',
            'phone'    => 'required',
            'password' => 'required',
            'id_card'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $email = Technician::where('email', $request->email)->select('id')->first();
        if ($email) {
            return response()->json(msg($request, failed(), 'email_exist'));
        }

        $phone = Technician::where('phone', $request->phone)->select('id')->first();
        if ($phone) {
            return response()->json(msg($request, failed(), 'phone_exist'));
        }

        $id_card = unique_file($request->id_card->getClientOriginalName());
        $request->id_card->move(base_path() .'/public/individuals/', $id_card);

        $provider = Provider::where('en_name', 'individuals')->select('id')->first();
        $company = Company::where('en_name', 'individuals')->select('id')->first();

        $tech = new Technician();
        $tech->type    = 'individual';
        $tech->jwt     = str_random(20);
        $tech->active  = 0;
        $tech->en_name = $request->en_name;
        $tech->ar_name = $request->ar_name;
        $tech->email   = $request->email;
        $tech->phone   = $request->phone;
        $tech->password = Hash::make($request->password);
        if ($request->image) {
            $photo = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path() .'/public/individuals/', $photo);
            $tech->image = $photo;
        }
        $tech->cat_ids = $request->cat_ids;
        $tech->id_card = $id_card;
        $tech->provider_id = $provider->id;
        $tech->company_id = $company->id;
        $tech->save();

        return response()->json(msg($request, success(), 'registered'));
    }
}
