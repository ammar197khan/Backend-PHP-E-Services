<?php

namespace App\Http\Controllers\Api\Tech\Order;

use Validator;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class SetTechnicianOrderStatus extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'order_id' => 'required|exists:orders,id',
            'status'   => 'required',
            'tech_id'  => 'required'
        ]);

        // TODO: MUST VALIDATE AUTHENTICATION

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $status = Order_Tracking_Statuses(strval($request->status) , 'value');
        $status_arabic = '';
        $status_arabic = Order_Tracking_Statuses_Arabic(strval(str_replace(' ', '_', $request->status)) , 'key');
        if($status == null){
            return response()->json(['status' => 'failed', 'Status not exsist!']);
        }
        $lang = $request->header('lang');

        if($request->status == 'الفني في الطريق'){
            $request->status = 'Technician on the way';
        }elseif($request->status == 'الصيانة جارية'){
            $request->status = 'Maintenance on progress';
        }
        // FIXME: SHOULD BE firstOrCreate
        OrderTracking::create([
            'order_id' => $request->order_id,
            'status'   => $status,
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

        if ($request->status == 'Maintenance on progress') {
            $track['track'] = $lang == 'en' ? 'Finish order' : 'أنجزت المهمة';
            $track['is_finished_order'] = true;
        }

        return response()->json(msg_data($request, success(), 'updated', $track));
    }
}
