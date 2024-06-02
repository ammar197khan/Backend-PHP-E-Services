<?php

namespace App\Http\Controllers\Api\User\Order;

use App\Models\OrderAddress;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\TechNot;
use App\Models\TechToken;
use App\Models\Technician;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Collaboration;
use App\Models\OrderUserDetail;
use App\Models\ProviderCategoryFee;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;

class MakeOrder extends Controller
{

    public function __invoke(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'user_id'      => 'required|exists:users,id',
            'service_type' => 'required',
            'jwt'          => 'required|exists:users,jwt,id,' . $request->user_id,
            'type'         => 'required|in:urgent,scheduled,emergency',
            'smo'          => 'sometimes',
            'tech_id'      => 'sometimes|exists:technicians,id',
            'scheduled_at' => 'sometimes|date',
            'category_id'  => 'required|exists:categories,id,type,2',
            'images'       => 'sometimes',
            'images.*'     => 'image',
            'lat'           => 'required',
            'lng'           => 'required',
//            'address'       => 'required',
//            'name'          => 'required',
//            'city'          => 'required',
//            'camp'          => 'required',
//            'street'        => 'required',
//            'plot_no'       => 'required',
//            'block_no'      => 'required',
//            'building_no'   => 'required',
//            'apartment_no'  => 'required',
//            'house_type'    => 'required'
        ]);


        if($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $lang = $request->header('lang');
        $lastOrder = Order::where('user_id', $request->user_id)->get()->last();
        if($lastOrder && $lastOrder !="") {
        if($lastOrder->type == 'urgent' || $lastOrder->type == 'scheduled')
        {
            $orderID = $lastOrder->id;
            $checkPayment = Payment::where('order_id', $orderID)->first();
            if(!$checkPayment || $checkPayment == ''){
                /***********remove first order payment check */
                // return response()->json(['status' => 'failed', 'msg' => 'You can not create a new request. Your previous order payment status is pending']);
            }
        }
        }

        // VALIDATE UNIQUE SMO
        //if(Order::where('smo', $request->smo)->first()) {
           //return response()->json(['status' => 'error', 'msg' => 'MSO already exists']);
        //}

        // GET AUTH USER
        $user = User::find($request->user_id);

        // GET PROVIDER PARTNERS
        $providers = Collaboration::where('company_id', $user->company_id)->pluck('provider_id');
        $technician = Technician::where('id', $request->tech_id)->whereIn('provider_id', $providers)->first();
        $techStatus = '';
        if( !empty($technician->technician_role_id) && $technician->technician_role_id == 1 ){
            $techStatus = 'Technician selected';
         }elseif( !empty($technician->technician_role_id) && $technician->technician_role_id == 2 ){
            $techStatus = 'Assessor Supervisor selected';
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

        // GET ORDERS FEES SETTED TO THIS COMPANY BY THE PROVIDER PARTNER
        if($technician) {
            $order_fees =
            ProviderCategoryFee::where('provider_id', $technician->provider_id)
            ->where('company_id', $user->company_id)
            ->where('cat_id', $request->category_id)
            ->first();
            $order_fees = isset($order_fees[$request->type . '_fee']) ? $order_fees[$request->type . '_fee'] : 0;
        }

        // SAVE ORDER
        $order = new Order();
        $order->smo          = $request->smo;
        $order->type         = $request->type;
        $order->service_type = $request->service_type;
        $order->company_id   = $user->company_id;
        $order->tech_id      = $request->tech_id ?: null;
        $order->provider_id  = $technician ? $technician->provider_id : null;
        $order->user_id      = $request->user_id;
        $order->code         = rand(1000, 9999);
        $order->cat_id       = $request->category_id;
        $order->sub_cat_id   = $request->category_id;
        $order->scheduled_at = $request->type == 'scheduled' ? $request->scheduled_at : null;
        $order->order_total  = isset($order_fees) ? $order_fees : 0;
        $order->save();

        OrderAddress::create([
            'order_id'      => $order->id,
            'lat'           => $request->lat,
            'lng'           => $request->lng,
            'name'          => 'name',
            'is_default '   => $request->is_default ,
            'city'          => $request->city,
            'camp'          => $request->camp,
            'street'        => $request->street,
            'plot_no'       => $request->plot_no,
            'block_no'      => $request->block_no,
            'building_no'   => $request->building_no,
            'apartment_no'  => $request->apartment_no,
            'house_type'    => $request->house_type,
        ]);

        if ($request->type == 'urgent' && $technician->technician_role_id != 2) {
            $technician->update(['busy' => 1]);
        }

        OrderTracking::create([
            'order_id' => $order->id,
            'status'   => 'Service request',
            'date'     => Carbon::now(),
            'technicain_id' => $request->tech_id
        ]);

        if($technician) {
            $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
            $en_text = 'You have a new order request,please respond';
            TechNot::create([
                'type' => 'order',
                'tech_id' => $order->tech_id,
                'order_id' => $order->id,
                'ar_text' => $ar_text,
                'en_text' => $en_text
            ]);
            $token = TechToken::where('tech_id', $order->tech_id)->pluck('token');
            PushNotify::tech_send($token, $ar_text, $en_text, 'order', $order->id, null,  0, $lang);
            OrderTracking::create([
                'order_id' => $order->id,
                'status'   => $techStatus,
                'date'     => Carbon::now(),
                'technicain_id' => $request->tech_id
            ]);
        }

        if ($request->images) {
            $names = [];
            foreach ($request->images as $image) {
                $name = unique_file($image->getClientOriginalName());
                $image->move(base_path() . '/public/orders/', $name);
                $names[] = $name;
            }
        }
        $order_details = new OrderUserDetail();
        $order_details->order_id = $order->id;
        $order_details->place    = $request->place;
        $order_details->part     = $request->part;
        $order_details->desc     = $request->desc;
        $order_details->images   = isset($names) ? serialize($names) : null;
        $order_details->save();

        $email = User::whereId($user->id)->select('email')->first()->email;
        $dataEmail = [
            'subject' => 'Order Confirmation',
            'email' => $email,
            'content' => $order
        ];
        Mail::send('emails.order.order-confirmation',$dataEmail, function ($message) use ($dataEmail) {
            $message->from('support@qareeb.com','Support@Qareeb')
                ->to($dataEmail['email'])
                ->subject('Qareeb | Order Confirmation');
        });
        return response()->json(msg($request, success(), 'please_wait'));
    }

}
