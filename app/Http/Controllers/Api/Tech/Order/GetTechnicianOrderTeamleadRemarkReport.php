<?php

namespace App\Http\Controllers\Api\Tech\Order;

use Validator;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Http\Controllers\Controller;

class GetTechnicianOrderTeamleadRemarkReport extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'tech_id'  => 'required|exists:technicians,id',
            'jwt'      => 'required|exists:technicians,jwt,id,'.$request->tech_id,
            // 'order_id' => 'required|exists:orders,id,tech_id,'.$request->tech_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');
        $order = Order::where('id', $request->order_id)->with(['user', 'orderTeamLeadReport' => function($q){
          $q->with(['orderTeamAttachment' => function($q){
          $q->latest('created_at')->limit(1);

          }])->latest('created_at')->limit(1);
        }])->first();


        if(!empty($order))
        {

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
            $order['smo'] = isset($order->smo) ? (string)$order->smo : null;
            $order['details'] = $order->get_details($order->id);
            $order['items_requested'] = $order->get_items_awaiting($lang, $order->id);

            $order['items_submitted'] = $order->get_items($lang, $order->id);
            $order['location'] = $order->get_user_location($order->user_id);

            $order['category'] = $order->get_category($lang, $order->cat_id);
            $order['order_address'] = $orderAddress;

            $order['teamLeadReportAttachments'] = !empty(collect($order)->toArray()) && !empty(collect($order)['id']) ? (object) $order->get_teamLead_report_attachment(collect($order)['id']): null;
            $order['orderTeamLeadReport'] = (object)  !empty(collect($order)->toArray()) && !empty(collect($order)->toArray()['order_team_lead_report'])? collect($order)->toArray()['order_team_lead_report']['0']: null;
            if ($order->type == 'urgent') {
                $date = $order->created_at->toDateTimeString();
            } elseif ($order->type == 're_scheduled' && $order->scheduled_at == null) {
                $date = '';
            } else {
                $date = $order->scheduled_at;
            }

            $order_track = OrderTracking::where('order_id', $request->order_id)->pluck('status')->toArray();
            $order['canceled_by'] = isset($order->canceled_by) ? $order->canceled_by : '';
            $order['date'] = $date;
            //  $order['steps'] = $order->get_steps($lang,$order->id);

            $order['track'] = $lang == 'en' ? 'Technician on the way' : 'الفني في الطريق';

            if (in_array('Technician on the way', $order_track)) {
                $order['track'] = $lang == 'en' ? 'Maintenance on progress' : 'الصيانة جارية';
            }
            if (in_array('Maintenance on progress', $order_track)) {
                $order['track'] = $lang == 'en' ? 'Finish order' : 'أنجزت المهمة';
                $order['is_finished_order'] = true;
            }

            unset($order->scheduled_at,$order->created_at,$order->updated_at);
        }


        return response()->json($order);
    }
}
