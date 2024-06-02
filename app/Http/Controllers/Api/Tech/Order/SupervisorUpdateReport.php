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
use App\Models\OrderTeamLeadReport;
use App\Http\Controllers\Api\Tech\Order\CancelTechnicianOrder;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class SupervisorUpdateReport extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(),[
             'order_id' => 'required|exists:orders,id',
             'status'   => 'required',
             'is_approved_status' => 'required'
        ]);

        // TODO: MUST VALIDATE AUTHENTICATION
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $isExistOrderTeamLeadReport = OrderTeamLeadReport::where('order_id', $request->order_id)->whereIn('is_approved_status', ['approved', 'rejected'])->first();
        if ($isExistOrderTeamLeadReport) {
            return response()->json(['status' => 'failed', 'Order report already finished!']);
        }
        $status = Order_Tracking_Statuses(strval($request->status) , 'value');
        $status_arabic = '';
        $status_arabic = Order_Tracking_Statuses_Arabic(strval(str_replace(' ', '_', $request->status)) , 'key');
        if($status == null){
            return response()->json(['status' => 'failed', 'Status not exsist!']);
        }


        $lang = $request->header('lang');
        $order = Order::where('id', $request->order_id)->first();

       if($request->is_approved_status == 'approved'){
        $techId = Order::with(['track' => function($q) use($request){
            return $q->where('status', '=', 'Assigned to Team Lead')->latest()->limit(1);
         }])
         ->where('id', $request->order_id)
         ->first();

         $techId = !empty(collect($techId)) && !empty(collect($techId)['track']) && !empty(collect($techId)['track']['0']) && !empty(collect($techId)['track']['0']['technicain_id'])? collect($techId)['track']['0']['technicain_id'] : 0;
         if(empty($techId)){
             return response()->json(['status' => 'failed', 'Team Lead not found!']);
         }
         $request->request->add(['tech_id' => $techId ]);
 // GET AUTH USER
        $technician = Technician::find($techId);
 // GET PROVIDER PARTNERS
        $providers = Collaboration::where('company_id', $technician->company_id)->pluck('provider_id');
        $technician = Technician::where('id', $techId)->whereIn('provider_id', $providers)->first();
         // VALIDATE TECH IS BELONG TO PARTNER PROVIDER
         if($request->has('tech_id') && !$technician){
            return response()->json(msg($request, 'invalid_tech', 'invalid_tech'));
        }
        if(!empty($order)){
            $order->update([
                'tech_id' => $techId
             ]);
        }


        // FIXME: SHOULD BE firstOrCreate
        OrderTracking::create([
            'order_id' => $request->order_id,
            'status'   => $request->status,
            'technicain_id' => $techId,
            'date' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
        $technician->update(['busy' => 1]);

        if ($status) {
            $user_id = Order::whereId($request->order_id)->select('user_id')->first()->user_id;
            $ar_text = $status_arabic;
            $en_text = $status;

            UserNot::create([
                'type' => 'report_approved',
                'user_id' => $user_id,
                'order_id' => $request->order_id,
                'ar_text' => $ar_text,
                'en_text' => $en_text
            ]);

            $token = UserToken::where('user_id', $user_id)->pluck('token');
            PushNotify::user_send($token, $ar_text, $en_text, 'report_approved', $request->order_id, null,  0, $lang);




        }
        if ($techId!= null) {
            $technician->update(['busy' => 1]);
            $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
            $en_text = 'Assessment report approved by supervisor!';
            TechNot::create(
                [
                    'type' => 'report_approved',
                    'tech_id' => $technician->id,
                    'order_id' => $order->id,
                    'ar_text' => $ar_text,
                    'en_text' => $en_text
                ]
            );

            $token = TechToken::where('tech_id', $technician->id)->pluck('token');

            PushNotify::tech_send($token, $ar_text, $en_text, 'report_approved', $order->id, null,  0, $lang);

        }
        $track['track'] = '';
        $track['is_finished_order'] = false;

       }elseif($request->is_approved_status == 'rejected'){
                 $request->request->add(['deleted_by' => 'supervisors' ]);
                 $CancelTechnicianOrder =   new CancelTechnicianOrder();
                 $CancelTechnicianOrder = $CancelTechnicianOrder->__invoke($request);
        $orders = Order::where('id', $request->order_id)->with(['track'])->get()->toArray();

        if(!empty($orders) && !empty($orders['0']) && !empty($orders['0']['track']) ){

           $track = collect($orders['0']['track'])->unique('technicain_id');
            foreach($track as $dataTeach){
                if(!empty($dataTeach['technicain_id'])){
                    $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
                    $en_text = 'Assessment report rejected by supervisor!';

                    TechNot::create(
                        [
                            'type' => 'report_reject',
                            'tech_id' => $dataTeach['technicain_id'],
                            'order_id' => $order->id,
                            'ar_text' => $ar_text,
                            'en_text' => $en_text
                        ]
                    );

                    $token = TechToken::where('tech_id', $dataTeach['technicain_id'])->pluck('token');

                    PushNotify::tech_send($token, $ar_text, $en_text, 'report_reject', $order->id, null,  0, $lang);
                    Technician::where('id', $dataTeach['technicain_id'])->update(['busy' => 0]);
                }
            }
         }



            $track['track'] = 'Oder Canceled';
            $track['is_finished_order'] = True;

       }
         $OrderTeamLeadReport = OrderTeamLeadReport::where('order_id', $request->order_id);
            if(!empty($OrderTeamLeadReport->latest('created_at')->first())){
                $OrderTeamLeadReport->update([
                    'is_approved_status' => $request->is_approved_status
                ]);
            }
        if ($request->status == 'Maintenance on progress') {
            $track['track'] = $lang == 'en' ? 'Finish order' : 'أنجزت المهمة';
            $track['is_finished_order'] = true;
        }

        return response()->json(msg_data($request, success(), 'updated', $track));
    }
}
