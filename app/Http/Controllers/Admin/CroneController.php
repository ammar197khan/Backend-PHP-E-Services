<?php

namespace App\Http\Controllers\Admin;

use App\Models\Collaboration;
use App\Models\Order;
use App\Models\Provider;
use App\Models\ProviderCategoryFee;
use App\Models\PushNotify;
use App\Models\Technician;
use App\Models\TechNot;
use App\Models\TechnicainRole;
use App\Models\TechToken;
use App\Models\OrderTracking;
use App\Models\User;
use App\Models\UserNot;
use App\Models\UserToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderProcessType;

class CroneController extends Controller
{
    public function schedule()
    {
    $orders = Order::where('type','!=','urgent')->where('type','!=','emergency')->where('tech_id', NULL)->get();
        foreach($orders as $order)
       {

            if($order->scheduled_at >= Carbon::now() && Carbon::parse($order->scheduled_at)->diffInMinutes(Carbon::now()->subHour()->toTimeString()) <= 180)
            {


  	 if($order->provider_id == NULL)
                {
                    $coll_providers = Collaboration::where('company_id', $order->company_id)->pluck('provider_id');
                }
                else
                {
                    $coll_providers = Provider::where('id', $order->provider_id)->pluck('id');
                }
                 $user    =   User::where('id', $order->user_id)->with(['company' => function($q){
                    $q->with('orderProcessType');
                 } ])->first();
                 $user = collect( $user)->toArray();
                 $technician_role_id  = 0;
                 if(!empty($user['company']) && !empty($user['company']['order_process_id']) && !empty($user['company']['order_process_id']) && $user['company']['order_process_id'] == 1){
                    $technician_role  = TechnicainRole::where('id', 2)->first();
                    $technician_role_id = $technician_role->id;
                 }elseif(!empty($user['company']) && !empty($user['company']['order_process_type']) && !empty($user['company']['order_process_type']['id']) && $user['company']['order_process_type']['id'] == 2){
                    $technician_role  = TechnicainRole::where('id', 1)->first();
                    $technician_role_id = $technician_role->id;
                 }

                $tech = Technician::whereIn('provider_id', $coll_providers)->where('cat_ids','like','%'.$order->cat_id.'%')->where('busy', 0)->where('active', 1)->where('technician_role_id', $technician_role_id )->inRandomOrder()->first();
                if(!empty($user['company']) && !empty($user['company']['order_process_id']) && !empty($user['company']['order_process_id']) && $user['company']['order_process_id'] == 1){
                    OrderTracking::create([
                        'order_id' => $order->id,
                        'status'   => 'Assessor Supervisor selected',
                        'date'     => Carbon::now(),
                        'technicain_id' => $tech->id,
                    ]);

                 }elseif(!empty($user['company']) && !empty($user['company']['order_process_type']) && !empty($user['company']['order_process_type']['id']) && $user['company']['order_process_type']['id'] == 2){
                    OrderTracking::create([
                        'order_id' => $order->id,
                        'status'   => 'Assigned to Technician',
                        'date'     => Carbon::now(),
                        'technicain_id' => $tech->id,
                    ]);
                 }
                if($tech)
                {
//                    if($order->provider_id == NULL)
//                    {
//                        $order->provider_id = $tech->provider_id;
//                    }
                    $order->provider_id = $tech->provider_id;
                    $order->tech_id = $tech->id;
                    if($order->order_total == 0) $order->order_total = ProviderCategoryFee::where('provider_id', $order->provider_id)->where('cat_id', $order->cat_id)->select('urgent_fee')->first()->urgent_fee;
                    $order->save();

                    $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
                    $en_text = 'You have a new order request,please respond';

                    TechNot::create
                    (
                        [
                            'tech_id' => $tech->id,
                            'order_id' => $order->id,
                            'ar_text' => $ar_text,
                            'en_text' => $en_text
                        ]
                    );

                    $token = TechToken::where('tech_id', $order->tech_id)->pluck('token');
                    PushNotify::tech_send($token,$ar_text,$en_text,'order',$order->id);

                    $ar_text = 'تم تعيين عامل لطلبك السابق في الموعد المحدد,الرجاء المتابعة';
                    $en_text = 'Technician has been appointed to your order in the scheduled time,please check the details';

                    UserNot::create
                    (
                        [
                            'user_id' => $order->user_id,
                            'order_id' => $order->id,
                            'ar_text' => $ar_text,
                            'en_text' => $en_text
                        ]
                    );

                    $token = UserToken::where('user_id', $order->user_id)->pluck('token');
                    PushNotify::user_send($token,$ar_text,$en_text,'order',$order->id);
                }
            }

        }
       return 'successzzz !'.Carbon::parse($order->scheduled_at)->diffInMinutes(Carbon::now()->subHour()->toTimeString());
    }


    public function rotate()
    {
        $techs = Technician::where('rotation_id','!=',NULL)->select('id','rotation_id','busy')->get();

        foreach($techs as $tech)
        {
            if(Carbon::parse($tech->rotation->from)->diffInMinutes(Carbon::now()->toTimeString()) <= 10)
            {
                $open_orders = Order::where('tech_id', $tech->id)->where('completed', 0)->first();
                if($open_orders == NULL) $tech->busy = 0;
            }
            elseif(Carbon::parse($tech->rotation->to)->diffInMinutes(Carbon::now()->toTimeString()) <= 10)
            {
                $tech->busy = 1;
            }

            $tech->save();
        }

//        return 'successzzz !';
    }

}
