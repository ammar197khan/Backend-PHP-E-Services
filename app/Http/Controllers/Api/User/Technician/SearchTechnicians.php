<?php

namespace App\Http\Controllers\Api\User\Technician;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Rotation;
use App\Models\Technician;
use App\Models\Collaboration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProviderCategoryFee;

class SearchTechnicians extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
            'jwt'       => 'required|exists:users,jwt,id,' . $request->user_id,
            'cat_id'    => 'required|exists:categories,id,type,2',
            'latitude'  => 'sometimes',
            'longitude' => 'sometimes',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $user = User::where('id', $request->user_id)->select('company_id', 'lat', 'lng')->first();

        $providers = Collaboration::where('company_id', $user->company_id)->pluck('provider_id');

        $show_techs =
        Technician::whereIn('provider_id', $providers)
        ->where('active', 1)
        ->where('online', 1)
        ->where('busy', 0)
        ->WhereNotNull('company_id')
        ->where('technician_role_id', 1)
        ->select('id', 'busy', $lang . '_name as name', 'rotation_id','provider_id','company_id', 'image', 'lat', 'lng', 'cat_ids')
        ->get();

        // FIXME: FILTERING BY TECH IN DUTY (ONLINE) NOT WORKING RIGHT
        $techs = [];
        $tech = [];
        foreach ($show_techs as $show_tech) {
         $tech_id = $show_tech->id;
            $provider_id = $show_tech->provider_id;
            $company_id = $show_tech->company_id;
           // $provider_id = Technician::where('id', $tech_id)->first()->provider_id;
            $urgent_fee = ProviderCategoryFee::where('provider_id', $provider_id)->where('company_id', $company_id)->where('cat_id', $request->cat_id)->select('urgent_fee')->first();
		    $scheduled_fee = ProviderCategoryFee::where('provider_id', $provider_id)->where('company_id', $company_id)->where('cat_id', $request->cat_id)->select('scheduled_fee')->first();




	 $rotation = Rotation::where('id', $show_tech->rotation_id)->first();
            if($rotation){
                if ($rotation->from < Carbon::now()->format('H:i:s') && $rotation->to > Carbon::now()->format('H:i:s')) {
                    $cat_ids = explode(',', $show_tech->cat_ids);
                    if(in_array($request->cat_id, $cat_ids)){
                        array_push($techs, $show_tech);
                    }
                }
            }
        }


	  foreach ($techs as $tech) {
            $tech['image'] = 'http://' . $_SERVER['SERVER_NAME'] . '/public/providers/technicians/' . $tech->image;
            if($urgent_fee)
            {
                $urgentfee = $urgent_fee->urgent_fee;
            }
            else {
                $urgentfee = "";
            }
            if($scheduled_fee)
            {
                $schedulefee = $scheduled_fee->scheduled_fee;
            }
            else {
                $schedulefee = "";
            }
            $tech['urgent_fee'] = $urgentfee;
            $tech['scheduled_fee'] = $schedulefee;
$tech['rate'] = $tech->get_all_rate($tech->id);
	}

        return response()->json($techs);
    }
}

