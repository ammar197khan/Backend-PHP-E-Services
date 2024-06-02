<?php

namespace App\Http\Controllers\Api\User\Order;
use Validator;
use App\Models\OrderAddress;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Http\Controllers\Controller;

class GetUserOrderDetails extends Controller
{

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|exists:users,id',
            'jwt'      => 'required|exists:users,jwt,id,' . $request->user_id,
            'order_id' => 'required|exists:orders,id,user_id,' . $request->user_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $user = getUserDetailById($request->user_id);
        $lang = $request->header('lang');

        \App::setLocale($lang);

        $order = Order::where('id', $request->order_id)->with('user')->first();
        $orderAddress = OrderAddress::where('order_id', $request->order_id)->first();
        $payments = Payment::where('order_id', $request->order_id)->first();
        $payment_status= "";
        $payment_method = "";
        if($payments != "" && $payments != null) {
            $payment_status = "Paid";
            $payment_method = $payments['payment_type'];
        }
        else {
            $payment_status = "Not Paid";
            $payment_method = "";
        }
        $order['payment_status'] = $payment_status;
        $order['payment_method'] = $payment_method;
	    $order['smo'] = isset($order->smo) ? (string)$order->smo : '';
        $order['details'] = $order->get_details($order->id);
        $order['items_requested'] = $order->get_items_awaiting($lang, $order->id);
        $order['items_submitted'] = $order->get_items($lang, $order->id);
        $order['location'] = $order->get_user_location($order->user_id);
        $order['category'] = $order->get_category($lang, $order->cat_id);
   if(!empty($orderAddress)){
    $orderAddress->lat = !empty($orderAddress->lat)? $orderAddress->lat : '';
    $orderAddress->lng = !empty($orderAddress->lng)? $orderAddress->lng : '';
    $orderAddress->name = !empty($orderAddress->name)? $orderAddress->name : '';
    $orderAddress->order_id = !empty($orderAddress->order_id)? $orderAddress->order_id : '';
    $orderAddress->city = !empty($orderAddress->city)? $orderAddress->city : '';
    $orderAddress->camp = !empty($orderAddress->camp)? $orderAddress->camp : '';
    $orderAddress->street = !empty($orderAddress->street)? $orderAddress->street : '';
    $orderAddress->plot_no = !empty($orderAddress->plot_no)? $orderAddress->plot_no : '';
    $orderAddress->block_no = !empty($orderAddress->block_no)? $orderAddress->block_no : '';
    $orderAddress->building_no = !empty($orderAddress->building_no)? $orderAddress->building_no : '';
    $orderAddress->apartment_no = !empty($orderAddress->apartment_no)? $orderAddress->apartment_no : '';
    $orderAddress->house_type = !empty($orderAddress->house_type)? $orderAddress->house_type : '';
    $orderAddress->created_at = !empty($orderAddress->created_at)? $orderAddress->created_at : '';
    $orderAddress->updated_at = !empty($orderAddress->updated_at)? $orderAddress->updated_at : '';
   }
        $order['order_address'] = $orderAddress;

        if ($order->type == 'urgent') {
            $date = $order->created_at->toDateTimeString();
        } elseif ($order->type == 're_scheduled' && $order->scheduled_at == null) {
            $date = '';
        } else {
            $date = $order->scheduled_at;
        }
        // if ($lang == 'ar'){

        //     $show_all = ['طلب خدمة','تم اختيار الفني','الفني في الطريق','الصيانة جارية',
        //         'تم طلب قطع الغيار','تم الموافقه علي قطع الغيار','أعد جدولة الزيارة','أنجزت المهمة'];
        // }else{
            $orderProcessType = $request->order_process_type;
            $show_all = getalltracks( $orderProcessType );
        // }
        $track = OrderTracking::where('order_id', $order->id)->pluck('status')->toArray();
        $order['date'] = $date;
        $order['all_track'] = $show_all;
        $order['all_track_detail'] = $order->track_order($lang);
        $order['track'] = $this->translate_track($track);
        //        $order['steps'] = $order->get_steps($lang,$order->id);
        $order['canceled_by'] = isset($order->canceled_by) ? $order->canceled_by : '';

        if ($order->type == 'scheduled') {
              $order['provider_id'] = isset($order->provider_id) ? $order->provider_id : 0;
              $order['cat_id'] = isset($order->cat_id) ? $order->cat_id : 0;
              $order['tech_name'] = isset($order->tech_id) ? $order->get_tech($lang, $order->tech_id)->name : '';
              $order['tech_image'] = isset($order->tech_id) ? 'http://' . $_SERVER['SERVER_NAME'] . '/public/providers/technicians/' . $order->get_tech($lang, $order->tech_id)->image : '';
              $order['tech_id'] = isset($order->tech_id) ? $order->tech_id : 0;
        } elseif ($order->type == 'urgent') {
              $order['tech_name'] = $order->get_tech($lang, $order->tech_id)->name;
              $order['tech_image'] = 'http://' . $_SERVER['SERVER_NAME'] . '/public/providers/technicians/' . $order->get_tech($lang, $order->tech_id)->image;
        } elseif ($order->type == 're_scheduled') {
            if (isset($order->tech_id)) {
                $order['tech_name'] = $order->get_tech($lang, $order->tech_id)->name;
                $order['tech_image'] = 'http://' . $_SERVER['SERVER_NAME'] . '/public/providers/technicians/' . $order->get_tech($lang, $order->tech_id)->image;
            } else {
                $order['tech_id'] = 0;
                $order['tech_name'] = '';
                $order['tech_image'] = '';
            }
        }

        unset($order->scheduled_at, $order->created_at, $order->updated_at);

        return response()->json($order);
    }

    public function translate_track($track)
    {
        for ($i = 0; $i < count($track); $i++){
            // $track[$i] = __('language.' . $track[$i]);
            $track[$i] = $track[$i];
        }

        return $track;
    }

}
