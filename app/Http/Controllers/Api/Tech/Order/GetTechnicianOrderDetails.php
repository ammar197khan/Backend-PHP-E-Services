<?php

namespace App\Http\Controllers\Api\Tech\Order;

use Validator;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Http\Controllers\Controller;

class GetTechnicianOrderDetails extends Controller
{
    public function __invoke(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,'.$request->tech_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $order = Order::where('id', $request->order_id)->with(['user', 'orderTeamLeadReport' => function($q){
            $q->latest('created_at')->limit(1);
            $q->with('orderTeamAttachment');
          }])->first();

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
	    $user = $order->get_user($lang, $order->user_id);
        $order['user_name'] = $user->name;
        $order['user_phone'] = $user->phone;
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

        $order['reportStatus'] = !empty(collect($order)->toArray()) && !empty(collect($order)->toArray()['order_team_lead_report'])  && !empty(collect($order)->toArray()['order_team_lead_report']['0']['is_approved_status'])? collect($order)->toArray()['order_team_lead_report']['0']['is_approved_status']: '';

        $order['orderTeamLeadReport'] = (object)  !empty(collect($order)->toArray()) && !empty(collect($order)->toArray()['order_team_lead_report'])? collect($order)->toArray()['order_team_lead_report']['0']: NULL;

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
            $show_all = getalltracks( $orderProcessType);
        // }
        $track = OrderTracking::where('order_id', $order->id)->pluck('status')->toArray();
        $track = array_filter( $track, function($value) { return $value !== 'Send Back to TeamLead'; });

        $order['date'] = $date;
        $order['all_track'] = $show_all;
        $order['all_track_detail'] = $order->track_order($lang);

        $order['track_completed'] = $this->translate_track($track);
        $order_track = OrderTracking::where('order_id', $request->order_id)->pluck('status')->toArray();

        $latest_order_track =OrderTracking::where('order_id', $request->order_id)->select('status')->latest('status')->first()->toArray();
        $order['canceled_by'] = isset($order->canceled_by) ? $order->canceled_by : '';
        $order['date'] = $date;

        //  $order['steps'] = $order->get_steps($lang,$order->id);

        $order['track'] = !empty($latest_order_track) ? $latest_order_track['status']: '';

        if (in_array('Technician on the way', $order_track)) {
            $order['track'] = $lang == 'en' ? 'Maintenance on progress' : 'الصيانة جارية';
        }
        if (in_array('Maintenance on progress', $order_track)) {
            $order['track'] = $lang == 'en' ? 'Finish order' : 'أنجزت المهمة';
            $order['is_finished_order'] = true;
        }

        unset($order->scheduled_at,$order->created_at,$order->updated_at);
        return response()->json($order);
    }
    public function translate_track($track)
    {
        $track = array_values($track);
        for ($i = 0; $i < count($track); $i++){
            // $track[$i] = __('language.' . $track[$i]);
            $track[$i] = $track[$i];
        }

        return $track;
    }
}
