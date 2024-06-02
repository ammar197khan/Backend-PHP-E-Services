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

class TeamLeadSubmitReport extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'order_id' => 'required',
            'status'   => 'required',
            'tl_remarks'     => 'required',

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

        $order = Order::where('id', $request->order_id)->first();
        if(!empty($order)){
            $order->update([
                'tech_id' => $request->tech_id
             ]);
        }

        $orderTracking = OrderTracking::where('order_id', $request->order_id)->get();
        // dd( $orderTracking);
        $orderTrackStatus  =   array_filter(collect($orderTracking)->toArray(), function($v, $k) {
            return $v['status'] == 'Assessor Supervisor selected';
          }, ARRAY_FILTER_USE_BOTH);
          $orderTrackStatus = array_values($orderTrackStatus);
          $notiTechId = !empty($orderTrackStatus) && !empty($orderTrackStatus['0']) && !empty($orderTrackStatus['0']['technicain_id'])? $orderTrackStatus['0']['technicain_id'] : '';
          // FIXME: SHOULD BE firstOrCreate
          if($notiTechId == ''){
            return response()->json(['status' => 'failed', 'Assessor Supervisor did not found in this order!']);
        }
         OrderTracking::create([
             'order_id'      => $request->order_id,
             'status'        => $request->status,
             'technicain_id' => $request->tech_id,
             'date' => Carbon::now()
         ]);

         OrderTeamLeadReport::create([
            'order_id' => $request->order_id,
            'tl_remarks'  => $request->tl_remarks,
            'is_approved_status' => $request->is_approved_status,
            'supervisor_sendback_date' => NULL,
            'tl_submit_date' =>  Carbon::now()
         ]);
          $lastInsertedIdOrderTeamLeadReport = DB::getPdo()->lastInsertId();
          $images = [];
          if(!empty($request->image_path)){
            foreach ($request->image_path as $image) {
                $name = unique_file($image->getClientOriginalName());
                $image->move(base_path().'/public/orders/', $name);
                array_push($images, $name);
            }
           OrderTeamAttachment::create([
              'order_team_lead_report_id'    => $lastInsertedIdOrderTeamLeadReport,
              'image_path'  => serialize($images),
           ]);
          }
    //       $orderTeamAttachmentExceptLatest =  OrderTeamLeadReport::where('order_id', $request->order_id)->whereHas('orderTeamAttachment', function($q){
    //       })->with(['orderTeamAttachment'])->latest()->get();
    //       $orderTeamAttachmentExceptLatest = array_slice(collect($orderTeamAttachmentExceptLatest)->toArray(),1);
    //       $new_arr = [];
    //       if(!empty(collect($orderTeamAttachmentExceptLatest)->toArray()) ){

    //       foreach($orderTeamAttachmentExceptLatest as $data){
    //         if(!empty($orderTeamAttachmentExceptLatest)){
    //             $orderTeamAttachment['images'] = unserialize($data['order_team_attachment']['0']['image_path']);
    //             foreach ($orderTeamAttachment['images'] as $image) {
    //                 array_push($new_arr,  base_path().'/public/orders/' . $image);
    //             }
    //         }

    //     }
    // }
    //    FileDelete($new_arr);

             if ($status) {
             $user_id = Order::whereId($request->order_id)->select('user_id')->first()->user_id;

             $ar_text =  $status_arabic;
             $en_text =  $status;
             UserNot::create([
                 'type' => 'order',
                 'user_id' => $user_id,
                 'order_id' => $request->order_id,
                 'ar_text' => $ar_text,
                 'en_text' => $en_text
             ]);

             $token = UserToken::where('user_id', $user_id)->pluck('token');
             PushNotify::user_send($token, $ar_text, $en_text, 'order', $request->order_id,  $lang);

             $track['track'] = $lang == 'en' ? 'Maintenance on progress' : 'الصيانة جارية';
             $track['is_finished_order'] = false;


         }
         if ($request->tech_id != null &&  !empty( $notiTechId)) {
             $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
             $en_text = 'Team Lead assessment report submitted!';
             TechNot::create(
                 [
                     'type' => 'order',
                     'tech_id' => $notiTechId,
                     'order_id' => $order->id,
                     'ar_text' => $ar_text,
                     'en_text' => $en_text
                 ]
             );

             $token = TechToken::where('tech_id', $notiTechId)->pluck('token');
             PushNotify::tech_send($token, $ar_text, $en_text, 'order', $order->id, $lang);

         }

         if ($request->status == 'Maintenance on progress') {
             $track['track'] = $lang == 'en' ? 'Finish order' : 'أنجزت المهمة';
             $track['is_finished_order'] = true;
         }

         return response()->json(msg_data($request, success(), 'updated', $track));

    }
}
