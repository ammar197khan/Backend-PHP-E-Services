<?php

namespace App\Http\Controllers\Api\Tech\Auth;

use Hash;
use Validator;
use App\Models\TechToken;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TechnicianLogin extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required',
            'token'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $tech =
        Technician::where('email', $request->email)
        ->select('id', 'jwt', 'active', 'email', 'password', 'busy', 'lat', 'lng', 'technician_role_id', 'company_id')
        ->with(['technicainRole', 'company' => function($q){
              $q->with('orderProcessType');
        }])
        ->first();

            $techArray = [];

        if (!$tech) {
          return response()->json(msg($request, failed(), 'invalid_email'));
        }

        if ($tech->active == 0) {
            return response()->json(msg($request, suspended(), 'suspended'));
        }

        $check = Hash::check($request->password, $tech->password);

        if (!$check) {
            return response()->json(msg($request, failed(), 'invalid_password'));
        }

        TechToken::updateOrcreate(
            ['tech_id' => $tech->id],
            ['token'   => $request->token]
        );

        $tech['lat'] = isset($tech->lat) ? $tech->lat : '';
        $tech['lng'] = isset($tech->lng) ? $tech->lng : '';
        $tech->online = 1;
        $tech->save();
        $tech =  collect($tech)->toArray();

        $techArray['id'] = $tech['id'];
        $techArray['jwt'] = $tech['jwt'];
        $techArray['active'] = $tech['active'];
        $techArray['email'] = $tech['email'];
        $techArray['password'] = $tech['password'];
        $techArray['busy'] = $tech['busy'];
        $techArray['lat'] = $tech['lat'];
        $techArray['lng'] = $tech['lng'];
        $techArray['technician_role_id'] = $tech['technician_role_id'];
        $techArray['company_id'] = $tech['company_id'];
        $orderProcessType = !empty($tech['company']) && !empty($tech['company']['order_process_type']) && !empty($tech['company']['order_process_type']['id'])?  $tech['company']['order_process_type']['name'] : NULL;
        $techArray['order_process_type'] = $orderProcessType ;
        $techArray['technicain_role']['name'] = !empty($tech['technicain_role']) && !empty($tech['technicain_role']['name'])?  $tech['technicain_role']['name'] : '';
        unset($tech->email,$tech->password);
        return response()->json(['status' => 'success', 'msg' => 'logged_in', 'data' => (object)$techArray]);
    }
}
