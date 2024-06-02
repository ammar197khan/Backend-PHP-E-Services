<?php

namespace App\Http\Controllers\Api\User\Technician;

use DB;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Technician;
use App\Models\Rotation;
use App\Models\Collaboration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetSupervisors extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|exists:users,id',
            'jwt'        => 'required|exists:users,jwt,id,' . $request->user_id,
            'company_id' => 'required|exists:companies,id|exists:users,company_id,id,' . $request->user_id,
            'cat_id'     => 'required|exists:categories,id',
            'latitude'   => 'sometimes',
            'longitude'  => 'sometimes',
        ]);

        if($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $user = User::where('id', $request->user_id)->select('company_id', 'lat', 'lng')->first();
        $providers = Collaboration::where('company_id', $user->company_id)->pluck('provider_id');

        $latitude  = $request->latitude ?? $user->lat;
        $longitude = $request->longitude ?? $user->lng;
        $haversine = $this->getRawHaversine($latitude, $longitude);
        $radius = 50;

        $techs =
        Technician::select('id','en_name as name' ,'lat', 'lng', 'rotation_id')
        ->selectRaw("{$haversine} AS distance")
        ->with('rotation')
        ->whereRaw("{$haversine} < ?", [$radius])
        ->where('cat_ids','like','%'.$request->cat_id.'%')
        ->whereIn('provider_id',$providers)
        ->where('technician_role_id', 2)
        ->where('active', 1)
        ->where('busy', 0)
        ->get();

        $techs_result = [];

        foreach ($techs as $tech) {
            if (optional($tech->rotation)->isOnline()) {
                unset($tech->rotation);
                array_push($techs_result, $tech);
            }
        }

        $user['lat'] = $user->lat;
        $user['lng'] = $user->lng;
        unset($user->company_id);
        return response()->json(['supervisor' => $techs_result, 'user' => $user]);
    }

    public function getRawHaversine($lat1, $lng1)
    {
        return
        "(6371 * acos(cos(radians({$lat1}))
         * cos(radians(technicians.lat))
         * cos(radians(technicians.lng)
         - radians({$lng1}))
         + sin(radians({$lat1}))
         * sin(radians(technicians.lat))))";
    }
}
