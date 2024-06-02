<?php

namespace App\Http\Controllers\Api\Tech\Order;

use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\PushNotify;
use App\Models\Technician;
use App\Models\OrderTracking;
use Illuminate\Http\Request;;
use App\Models\OrderTechDetail;
use App\Models\ProviderCategoryFee;
use App\Http\Controllers\Controller;;

class FinishTechnicianOrder extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            'order_id' => 'required|exists:orders,id,completed,0,tech_id,'.$request->tech_id,
            'status'   => 'required|in:completed,re_scheduled',
            'code'     => 'required',
            // 'type_id' => 'required|exists:categories,id',
            'desc'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $order = Order::where('id', $request->order_id)->where('code', $request->code)->first();

        if (!$order) {
            return response()->json(msg($request, failed(), 'invalid_code'));
        }

        $user = User::find($order->user_id);

        if ($order->service_type == 1) {
            $details = new OrderTechDetail();
            $details->order_id = $request->order_id;
            $details->type_id  = $order->cat_id;
            $details->desc     = $request->desc;
            $details->save();
        }

        if ($order->service_type == 2 || $order->service_type == 3) {
            $validator = Validator::make($request->all(),         [
                'tech_id'       => 'required|exists:technicians,id',
                'jwt'           => 'required|exists:technicians,jwt,id,'.$request->tech_id,
                'order_id'      => 'required|exists:orders,id,completed,0,tech_id,'.$request->tech_id,
                'status'        => 'required|in:completed,re_scheduled',
                'code'          => 'required',
                'desc'          => 'required',
                'before_images' => 'required|array',
                'after_images'  => 'required|array',
            ]);

            // TODO: VALDIATE IMAGES TYPE

            if ($validator->fails()) {
                return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
            }

            $explode_cat_id = explode(',', $request->cat_id);
            $working_hour_explode = explode(',', $request->working_hours);

            $get_fee = 0;
            for ($i=0; $i<count($explode_cat_id); $i++) {
                //for sure exp same size to working hour
                $cat_id = $explode_cat_id[$i];
                $working_hour = $working_hour_explode[$i];

                $details = new OrderTechDetail();
                $details->order_id      = $request->order_id;
                $details->type_id       = $cat_id;
                $details->desc          = $request->desc;
                $details->working_hours = $working_hour;
                $details->save();

                $fee = ProviderCategoryFee::where('provider_id', $order->provider_id)->where('company_id', $order->company_id)
                    ->where('cat_id', $cat_id)->first();

                if (isset($fee)) {
                    $get_all = $working_hour * $fee->third_fee;
                } else {
                    $get_all = $working_hour * 0;
                }
                $get_fee += $get_all;

                if ($get_fee > $order->order_total) {
                    $order->order_total = $get_fee;
                    $order->save();
                }
            }

            $update_details = OrderTechDetail::where('id', $details->id)->first();
            $before = [];
            foreach ($request->before_images as $image) {
                $name = unique_file($image->getClientOriginalName());
                $image->move(base_path().'/public/orders/', $name);
                array_push($before, $name);
            }

            $after = [];
            foreach ($request->after_images as $image) {
                $name = unique_file($image->getClientOriginalName());
                $image->move(base_path().'/public/orders/', $name);
                array_push($after, $name);
            }

            $update_details->before_images = serialize($before);
            $update_details->after_images = serialize($after);
            $update_details->save();
        }


        $order->completed = 1;
        $order->save();

        $order = Order::find($order->id);

        OrderTracking::create([
            'order_id' => $order->id,
            'status'   => 'Job completed',
            'date'     => Carbon::now(),
            'technicain_id' => $request->tech_id
        ]);

        Technician::where('id', $request->tech_id)->update(['busy' => 0]);

        $orders = Order::where('id', $request->order_id)->with(['track'])->get()->toArray();

        if(!empty($orders) && !empty($orders['0']) && !empty($orders['0']['track']) ){
            foreach($orders['0']['track'] as $dataTeach){
                if(!empty($dataTeach['technicain_id'])){
                    Technician::where('id', $dataTeach['technicain_id'])->update(['busy' => 0]);
                }
            }
         }


        $token = UserToken::where('user_id', $order->user_id)->pluck('token');
        if($user->company->en_name == 'individuals'){
            $ar_text = 'تكلفة الخدمة ' . $order->total_amount;
            $en_text = 'Order costs ' . $order->total_amount;
            $notification_type = 'pay';
        } else {
            $ar_text = 'تم إنهاء الطلب بنجاح,الرجاء التقييم';
            $en_text = 'Order has been completed successfully,please rate';
            $notification_type = 'rate';
        }

        UserNot::create([
            'type'     => $notification_type,
            'user_id'  => $order->user_id,
            'order_id' => $request->order_id,
            'ar_text'  => $ar_text,
            'en_text'  => $en_text
        ]);

        $tech = $order->get_tech_all($order->tech_id, $order->cat_id);

        PushNotify::user_send($token, $ar_text, $en_text, $notification_type, $order->id, $tech, $order->total_amount, $lang);

        return response()->json(msg($request, success(), 'success'));
    }
}
