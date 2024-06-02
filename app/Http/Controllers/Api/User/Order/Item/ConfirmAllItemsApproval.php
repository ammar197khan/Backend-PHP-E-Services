<?php

namespace App\Http\Controllers\Api\User\Order\Item;

use DB;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\UserNot;
use App\Models\Company;
use App\Models\TechNot;
use App\Models\UserToken;
use App\Models\Technician;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\OrderItemUser;
use App\Models\OrderTechRequest;
use App\Models\ItemRequestState;
use App\Http\Controllers\Controller;

class ConfirmAllItemsApproval extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|exists:users,id',
            'jwt'      => 'required|exists:users,jwt,id,' . $request->user_id,
            'order_id' => 'required|exists:orders,id,user_id,' . $request->user_id
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }
        $lang = $request->header('lang');
        $requested_items = OrderItemUser::where('order_id', $request->order_id)->where('status', '!=', 'declined')->get();

        $check = $requested_items->pluck('status');

        if (in_array('awaiting', $check->toArray())) {
            return response()->json(msg($request, failed(), 'items_approval_missing'));
        }
        dd('working');


        $company_id = User::where('id', $request->user_id)->select('company_id')->first()->company_id;
        // dd($company_id);
        $limit = Company::where('id', $company_id)->select('item_limit')->first()->item_limit;
        foreach ($requested_items as $item) {
            $order = Order::where('id', $request->order_id)->select('id', 'tech_id', 'item_total')->first();

            $this_item = DB::table($item->provider_id . '_warehouse_parts')->where('id', $item->item_id)->first();

            $item_request = new OrderTechRequest();
            $item_request->order_id = $request->order_id;
            $item_request->provider_id = $item->provider_id;
            $item_request->item_id = $item->item_id;
            $item_request->taken = $item->taken;
            $item_request->save();

            if ($this_item->count > 0) {
                if ($this_item->price * $item_request->taken <= $limit) {
                    $item_request->status = 'confirmed';
                    $item_request->save();

                    DB::table($item->provider_id . '_warehouse_parts')->where('id', $item->item_id)->update(['count' => $this_item->count - 1]);

                    $order->item_total = $this_item->price * $item_request->taken;
                    //                        $order->order_total = $this_item->price + $order->order_total;
                    $order->save();

                    OrderTracking::create([
                        'order_id' => $order->id,
                        'status'   => 'Spare parts approved',
                        'date' => Carbon::now()
                    ]);
                } else {
                    ItemRequestState::create([
                        'request_id'  => $item_request->id,
                        'company_id'  => $company_id,
                        'provider_id' => $item->provider_id,
                    ]);
                }
            } else {
                DB::table($item->provider_id . '_warehouse_parts')->where('id', $item->item_id)->update(['requested_count' => $this_item->requested_count + 1]);
            }
        }

        OrderItemUser::where('order_id', $request->order_id)->delete();

        //        Technician::where('id', $order->tech_id)->update(['busy' => 0]);
        Order::where('id', $request->order_id)->update(['type' => 're_scheduled']);

        //        TechNot::where('order_id', $request->order_id)->where('tech_id',$request->tech_id)->delete();

        $submitted = OrderTechRequest::where('order_id', $request->order_id)->get();

        if (in_array('awaiting', $submitted->pluck('status')->toArray()) == false) {
            $ar_text = 'تم الموافقة علي القطع المطلوبة للطلب خاصتك,الرجاء تحديد موعد لإعادة الزيارة';
            $en_text = 'The request for your order items is approved,please select the reschedule time';

            UserNot::create([
                'type'     => 'time',
                'user_id'  => $request->user_id,
                'order_id' => $request->order_id,
                'ar_text'  => $ar_text,
                'en_text'  => $en_text
            ]);

            $token = UserToken::where('user_id', $request->user_id)->pluck('token');
            PushNotify::user_send($token, $ar_text, $en_text, 'time', $request->order_id, null, 0, $lang);
        } else {
            $ar_text = 'سعر القطع أعلي من الحد الأقصي للموافقة التلقائية,في إنتظار الموافقة علي القطع خاصتك,الرجاءالإنتظار';
            $en_text = 'Parts prices for your order exceed the items price limit,parts are under approval,please wait';

            UserNot::create([
                'type'     => 'order',
                'user_id'  => $request->user_id,
                'order_id' => $request->order_id,
                'ar_text'  => $ar_text,
                'en_text'  => $en_text
            ]);

            $token = UserToken::where('user_id', $request->user_id)->pluck('token');
            PushNotify::user_send($token, $ar_text, $en_text, 'order', $request->order_id, null, 0, $lang);
        }

        return response()->json(msg($request, success(), 'please_wait'));
    }
}
