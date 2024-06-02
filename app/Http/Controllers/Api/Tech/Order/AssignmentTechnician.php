<?php

namespace App\Http\Controllers\Api\Tech\Order;

use Validator;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\PushNotify;
use App\Models\TechNot;
use App\Models\TechToken;
use App\Models\Technician;
use App\Models\Collaboration;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class AssignmentTechnician extends Controller
{
    public function __invoke(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'order_id' => 'required|exists:orders,id',
            'status'   => 'required',
            'tech_id'  => 'required',
        ]);

        // $techId = Order::with(['track' => function($q) use($request){
        //     return $q->where('status', '=', 'Assigned to Team Lead')->latest()->limit(1);
        //  }])
        //  ->where('id', $request->order_id)
        //  ->first();
        // //  dd($techId);

        //  $techId = !empty(collect($techId)) && !empty(collect($techId)['track']) && !empty(collect($techId)['track']['0']) && !empty(collect($techId)['track']['0']['technicain_id'])? collect($techId)['track']['0']['technicain_id'] : 0;
         // GET AUTH USER
        $technician = Technician::find($request->tech_id);

        // GET PROVIDER PARTNERS
        $providers = Collaboration::where('company_id', $technician->company_id)->pluck('provider_id');
        $technician = Technician::where('id', $request->tech_id)->whereIn('provider_id', $providers)->first();

        // TODO: MUST VALIDATE AUTHENTICATION
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        // VALIDATE TECH IS BELONG TO PARTNER PROVIDER
        if($request->has('tech_id') && !$technician){
            return response()->json(msg($request, 'invalid_tech', 'invalid_tech'));
        }

        // VALAIDATE REQUEST HAS 'tech_id' IF ORDER IS URGENT TYPE
        if ($request->type == 'urgent' && !$request->has('tech_id')) {
            return response()->json(msg($request, 'invalid_tech', 'invalid_tech'));
        }


        // TODO: VALIDATE IF TECH SERVE USER SubCompany

        // TODO: VALIDATE IF TECH IS ACTIVE

        // TODO: VALIDATE IF TECH IS CAN DO THIS CATEGORY SERVICE

        // TODO: VALIDATE IF TECH IS ONLINE && ACTIVE ROTATION IF URGENT ORDER

        // VALAIDATE TECH IS NOT BUSY IF ORDER IS URGENT
        if ($request->type == 'urgent' && $technician->busy) {
            return response()->json(msg($request, 'invalid_tech', 'invalid_tech'));
        }

        // VALAIDATE REQUEST HAS 'scheduled_at' IF ORDER IS SCHEDULED TYPE
        if ($request->type == 'scheduled' && !$request->has('scheduled_at')) {
            return response()->json(['status' => 'failed', 'msg' => 'invalid scheduled_at']);
        }
        $status = Order_Tracking_Statuses(strval($request->status) , 'value');
        $status_arabic = '';
        $status_arabic = Order_Tracking_Statuses_Arabic(strval(str_replace(' ', '_', $request->status)) , 'key');

        if($status == null){
            return response()->json(['status' => 'failed', 'Status not exsist!']);
        }

        $lang = $request->header('lang');
        $order =  Order::where('id', $request->order_id)->first();
        if(!empty($order)){
            $order->update([
                'tech_id' => $request->tech_id
             ]);
        }
        // FIXME: SHOULD BE firstOrCreate
        OrderTracking::create([
            'order_id' => $request->order_id,
            'status'   => $request->status,
            'technicain_id' => $request->tech_id,
            'date' => Carbon::now()
        ]);
            if ($status) {
            $user_id = Order::whereId($request->order_id)->select('user_id')->first()->user_id;

            $ar_text = $status_arabic;
            $en_text = $status;

            UserNot::create([
                'type' => 'order',
                'user_id' => $user_id,
                'order_id' => $request->order_id,
                'ar_text' => $ar_text,
                'en_text' => $en_text
            ]);

            $token = UserToken::where('user_id', $user_id)->pluck('token');
            PushNotify::user_send($token, $ar_text, $en_text, 'order', $request->order_id, null,  0, $lang);

            $track['track'] = $lang == 'en' ? 'Maintenance on progress' : 'الصيانة جارية';
            $track['is_finished_order'] = false;


        }
        if ($request->tech_id != null) {
            $technician->update(['busy' => 1]);
            $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
            $en_text = 'You have a new order request,please respond';

            TechNot::create(
                [
                    'type' => 'order',
                    'tech_id' => $order->tech_id,
                    'order_id' => $order->id,
                    'ar_text' => $ar_text,
                    'en_text' => $en_text
                ]
            );

            $token = TechToken::where('tech_id', $order->tech_id)->pluck('token');

            PushNotify::tech_send($token, $ar_text, $en_text, 'order', $order->id, null,  0, $lang);

        }

        if ($request->status == 'Maintenance on progress') {
            $track['track'] = $lang == 'en' ? 'Finish order' : 'أنجزت المهمة';
            $track['is_finished_order'] = true;
        }

        return response()->json(msg_data($request, success(), 'updated', $track));
    }
}
