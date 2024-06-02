<?php

namespace App\Http\Controllers\Api\Tech\Order;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\PushNotify;
use App\Models\TechNot;
use App\Models\TechToken;
use App\Models\Technician;
use App\Models\OrderTeamLeadReport;
use App\Models\OrderTeamAttachment;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use DB;
use App\Models\OrderTechDetail;
use App\Models\ProviderCategoryFee;
use App\Http\Controllers\Controller;

class SendBackTeamLeadRemarkReport extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status'   => 'required',
            'supervisor_remarks'     => 'required',
        ]);

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

        $techId = Order::with(['track' => function($q) use($request){
           return $q->where('status', '=', 'Assigned to Team Lead')->latest()->limit(1);
        }])
        ->where('id', $request->order_id)
        ->first();

        $techId = !empty(collect($techId)) && !empty(collect($techId)['track']) && !empty(collect($techId)['track']['0']) && !empty(collect($techId)['track']['0']['technicain_id'])? collect($techId)['track']['0']['technicain_id'] : 0;
        if(empty($techId)){
            return response()->json(['status' => 'failed', 'Team Lead not found!']);
        }
        $order = Order::where('id', $request->order_id)->first();
        if(!empty($order)){
            $order->update([
                'tech_id' => $techId
             ]);
        }

         // FIXME: SHOULD BE firstOrCreate
         OrderTracking::create([
             'order_id'      => $request->order_id,
             'status'        => $request->status,
             'technicain_id' => $techId,
             'date' => Carbon::now(),
         ]);

        $OrderTeamLeadReport = OrderTeamLeadReport::where('order_id', $request->order_id)->latest()->first();
        $OrderTeamLeadReport->update([
            'supervisor_remarks'  => $request->supervisor_remarks,
            'is_approved_status'  => $request->is_approved_status,
            'supervisor_sendback_date' =>  Carbon::now(),
            ]);

             if ($status) {
             $user_id = Order::whereId($request->order_id)->select('user_id')->first()->user_id;

             $ar_text = $status_arabic;
             $en_text = $status;

             UserNot::create([
                 'type' => 'send_back_to_teamLead_remarks',
                 'user_id' => $user_id,
                 'order_id' => $request->order_id,
                 'ar_text' => $ar_text,
                 'en_text' => $en_text
             ]);

             $token = UserToken::where('user_id', $user_id)->pluck('token');
             PushNotify::user_send($token, $ar_text, $en_text, 'send_back_to_teamLead_remarks', $request->order_id, null,  0, $lang);

             $track['track'] = $lang == 'en' ? 'Maintenance on progress' : 'الصيانة جارية';
             $track['is_finished_order'] = false;


         }
         if ($techId != null) {
             $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
             $en_text = 'Report has been send back by the supervisor!';

             TechNot::create(
                 [
                     'type' => 'send_back_to_teamLead_remarks',
                     'tech_id' => $techId,
                     'order_id' => $order->id,
                     'ar_text' => $ar_text,
                     'en_text' => $en_text
                 ]
             );

             $token = TechToken::where('tech_id', $techId)->pluck('token');

             PushNotify::tech_send($token, $ar_text, $en_text, 'send_back_to_teamLead_remarks', $order->id, null,  0, $lang);

         }

         if ($request->status == 'Maintenance on progress') {
             $track['track'] = $lang == 'en' ? 'Finish order' : 'أنجزت المهمة';
             $track['is_finished_order'] = true;
         }

         return response()->json(msg_data($request, success(), 'updated', $track));

    }
}
