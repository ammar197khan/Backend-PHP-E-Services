<?php

namespace App\Http\Controllers\Api\User\Order;

use Validator;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\TechNot;
use App\Models\TechToken;
use App\Models\PushNotify;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\User;
use App\Http\Controllers\Controller;

class RescheduleUserOrder extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
            'jwt'       => 'required|exists:users,jwt,id,'.$request->user_id,
            'order_id'  => 'required|exists:orders,id,user_id,'.$request->user_id,
            'timestamp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $lang = $request->header('lang');
        $user = User::where('id', $request->user_id)->with(['company'])->first();

        Order::where('id', $request->order_id)->update([
            'scheduled_at' => $request->timestamp,
        ]);

        OrderTracking::create([
            'order_id' => $request->order_id,
            'status'   => 'Reschedule the visit',
            'date'     => Carbon::now()
        ]);
        $orders = Order::where('id', $request->order_id)->with(['track'])->get()->toArray();
        if(!empty($orders) && !empty($orders['0']) && !empty($orders['0']['track']) ){

            $track = collect($orders['0']['track'])->unique('technicain_id');
            $status =   !empty($user->company) && !empty($user->company->order_process_id) && $user->company->order_process_id == 1 ? 'Technician selected' : 'Service request';
            $filtered = $track->filter(function($item) use($status){
                return $item['status'] == $status;
            });

            $track = array_values(collect($filtered)->toArray());
             foreach($track as $dataTeach){
                 if(!empty($dataTeach['technicain_id'])){
                     $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
                     $en_text = 'User want to Reschedule the visit';

                     TechNot::create(
                         [
                             'type' => 'report_reject',
                             'tech_id' => $dataTeach['technicain_id'],
                             'order_id' => $request->order_id,
                             'ar_text' => $ar_text,
                             'en_text' => $en_text
                         ]
                     );
                     $token = TechToken::where('tech_id', $dataTeach['technicain_id'])->pluck('token');
                     PushNotify::tech_send($token, $ar_text, $en_text, 'report_reject', $request->order_id,  null,  0, $lang);
                     Technician::where('id', $dataTeach['technicain_id'])->update(['busy' => 0]);
                 }
             }
          }

        UserNot::where('order_id', $request->order_id)->where('type', 'time')->delete();

        return response()->json(msg($request, success(), 'success'));
    }
}
