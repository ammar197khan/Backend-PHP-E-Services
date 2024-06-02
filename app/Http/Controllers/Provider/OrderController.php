<?php

namespace App\Http\Controllers\Provider;

use App\Imports\OrdersImport;
use App\Imports\OrderTechDetailsImport;
use App\Models\Category;
use App\Models\Collaboration;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderExpense;
use App\Models\OrderTechDetail;
use App\Models\OrderTechRequest;
use App\Models\OrderTracking;
use App\Models\OrderUserDetail;
use App\Models\Provider;
use App\Models\ProviderCategoryFee;
use App\Models\ProviderSubscription;
use App\Models\PushNotify;
use App\Models\SubCompany;
use App\Models\Technician;
use App\Models\TechNot;
use App\Models\TechToken;
use App\Models\UserNot;
use App\Models\UserToken;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\ActivityLog;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class OrderController extends Controller
{
    public function index($type)
    {
        $collaboration = Collaboration::where('provider_id',provider()->provider_id)->pluck('company_id');
        $companies = Company::whereIn('id', $collaboration)->select('id', 'en_name')->get();

        $providerSubscription = ProviderSubscription::where('provider_id', provider()->provider_id)->first();
        $subs = $providerSubscription->subs ?? serialize([]);

        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $items = Warehouse::pluck('en_name','id')->toArray();

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('provider_id', provider()->provider_id)->whereIn('company_id', $collaboration);
        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('provider_id', provider()->provider_id)->whereIn('company_id', $collaboration);

        if($type == 'monthly_parts_orders_count')
        {
            $orders = $monthly_orders->where('type','re_scheduled');
        }elseif($type == 'yearly_parts_orders_count')
        {
            $orders = $yearly_orders->where('type','re_scheduled');
        }elseif($type == 'all')
        {
            $orders = Order::where('provider_id', provider()->provider_id)->with(['category' => function($q){

            }]);
        }else{
            $orders = Order::where('provider_id', provider()->provider_id)->where('type', $type);
        }

        $orders = $orders->latest()->paginate(50);

        return view('provider.orders.index', compact('orders','type','cats','companies','items'));
    }

    public function tech_status_orders($id)
    {
        $type = 'Tech status orders';
        $collaboration = Collaboration::where('provider_id',provider()->provider_id)->pluck('company_id');
        $companies = Company::whereIn('id', $collaboration)->select('id', 'en_name')->get();

        $providerSubscription = ProviderSubscription::where('provider_id', provider()->provider_id)->first();
        $subs = $providerSubscription->subs ?? serialize([]);

        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $orders = Order::where('provider_id',provider()->provider_id)->where('tech_id', $id)->where('completed',0)->where('canceled',0)->paginate(50);
        return view('provider.orders.index', compact('orders','type','companies','cats','type'));
    }

    public function waiting()
    {
        $path    = base_path().'/public/orders/waiting';
        $files = array_diff(scandir($path), array('.', '..'));
        $collaboration = Collaboration::where('provider_id', provider()->provider_id)->pluck('company_id');

        $shows = [];
        foreach ($files as $file)
        {
            $provider_id = substr($file, 0, strpos($file, '_'));
            $company_id = substr($file, 2, strpos($file, '_'));
//            $show_company = Company::where('id', $company_id)->get();

            if(provider()->provider_id == $provider_id)
            {
                if(in_array($company_id, $collaboration->toArray()))
                {
                    $file = 'http://'.$_SERVER['SERVER_NAME'].'/orders/waiting/'.$file;
                    array_push($shows, $file);
                }
            }
        }

        return view('provider.orders.waiting', compact('shows'));
    }

    public function waiting_upload_view()
    {
        return view('provider.orders.waiting_upload');
    }

    public function waiting_upload(Request $request)
    {
        $this->validate($request,
            [
                'file' => 'required|file'
            ]
        );
        $array = Excel::toArray(new OrdersImport(),$request->file('file'));
//        unset($array[0][0]);


        foreach($array[0] as $data){
            $data = array_filter($data);
            try{
                $request->merge(['smo' => $data[0], 'type' => $data[1], 'company_id' => $data[2],
                    'provider_id'=>$data[3], 'service_type' => $data[4], 'cat_id' => $data[5],'tech_id' => $data[6],
                    'user_id' => $data[7], 'place'=> $data[8], 'part'=> $data[9],'desc'=> $data[10], 'scheduled_at'=>$data[11] ]);
            }
            catch (\Exception $e)
            {
                return back()->with('error','Missing Column | '.$e->getMessage().',Offsets start from 0');
            }

            $this->validate($request,
                [
                    'smo' => 'required',
                    'type' => 'required|in:urgent,scheduled',
                    'company_id' => 'required|exists:companies,id',
                    'provider_id' => 'required|exists:providers,id',
                    'service_type' => 'required|in:1,2,3',
                    'cat_id' => 'required|exists:categories,id,type,2',
                    'tech_id' => 'required|exists:technicians,badge_id',
                    'user_id' => 'required|exists:users,badge_id',
                ],
                [
                    'smo.required' => 'Missing data in SMO column.',
                    'type.required' => 'Missing data in Type column.',
                    'type.in' => 'Wrong data in Type must be urgent or scheduled.',
                    'company_id.required' => 'Missing data in Company ID column.',
                    'company_id.exists' => 'Wrong ID in Company ID column '.$request->company_id.'.',
                    'provider_id.required' => 'Missing data in Provider ID column.',
                    'provider_id.exists' => 'Wrong ID in Provider ID column '.$request->provider_id.'.',
                    'service_type.required' => 'Missing data in Service Type column.',
                    'service_type.in' => 'Wrong data in Service Type must be 1 or 2 or 3.',
                    'cat_id.required' => 'Missing data in Category ID column.',
                    'cat_id.exists' => 'Wrong ID in Category ID column '.$request->cat_id.'.',
                    'tech_id.required' => 'Missing data in Technician ID column.',
                    'tech_id.exists' => 'Wrong ID in Technician ID column '.$request->tech_id.'.',
                    'user_id.required' => 'Missing data in User ID column.',
                    'user_id.exists' => 'Wrong ID in User ID column '.$request->user_id.'.',
                ]
            );

            // Numbers of days between January 1, 1900 and 1970 (including 19 leap years)
//            define("MIN_DATES_DIFF", 25569);
//
//            // Numbers of second in a day:
//            define("SEC_IN_DAY", 86400 - 3);
//            function excel2timestamp($excelDate)
//            {
//                if ($excelDate <= MIN_DATES_DIFF)
//                    return 0;
//
//                return  ($excelDate - MIN_DATES_DIFF) * SEC_IN_DAY;
//            }
//
//            $scheduled_at = excel2timestamp($data[11]);

            if($data[8] == "null"){
                $data[8] = 0;
            }
            if($data[9] == "null")
            {
                $data[9] = 0;
            }
            if($data[10] == "null")
            {
                $data[10] = 0;
            }
            if($data[11] == "null" || $data[1] == 'urgent')
            {
                $data[11] = 0;
            }else{
                if( is_string($data[11]) )
                {
                    return back()->with('error', 'Column scheduled at has a wrong value it should enter like that ( 1/13/2020  3:40:00 )');
                }

                $UNIX_DATE = ($data[11] - 25569) * 86400;
                $scheduled_at = gmdate("Y-m-d H:i:s", $UNIX_DATE);

                if($scheduled_at < Carbon::now()->format('Y-m-d H:i:s'))
                {
                    return redirect('/provider/orders/open/waiting/upload/view')->with('error', 'Choose a valid date');
                }
            }

//            $cat_explode = explode(',', $data[5]);
//            $cats = Category::whereIn('id', $cat_explode)->get();
//
//            foreach ($cats as $cat)
//            {
//                if($data[4] == 1 && $cat->type == 3)
//                {
//                    return redirect('/provider/orders/open/waiting/upload/view')->with('error', 'Choose sub category with service type preview');
//                }else if($data[4] == 2 && $cat->type == 2)
//                {
//                    return redirect('/provider/orders/open/waiting/upload/view')->with('error', 'Choose third category with service type maintenance');
//                }else if($data[4] == 3 && $cat->type == 2)
//                {
//                    return redirect('/provider/orders/open/waiting/upload/view')->with('error', 'Choose third category with service type structure');
//                }
//            }

            if(provider()->provider_id != $data[3])
            {
                return redirect('/provider/orders/open/waiting/upload/view')->with('error', 'Your provider id does not match with entered provider id');
            }

            $collaboration = Collaboration::where('provider_id', provider()->provider_id)->pluck('company_id');
            if(!in_array($data[2], $collaboration->toArray()))
            {
                return redirect('/provider/orders/open/waiting/upload/view')->with('error', 'Choose a valid company id that have collaboration with');
            }

            $user = User::where('badge_id', $data[7])->first();
            $tech = Technician::where('badge_id', $data[6])->first();
            if($tech->provider_id != $data[3])
            {
                return redirect('/provider/orders/open/waiting/upload/view')->with('error', 'Choose a valid provider id that appropriate a technician');
            }

            $order = new Order();

            $order->smo = $data[0];
            $order->type = $data[1];
            $order->company_id = $data[2];
            $order->provider_id = $data[3];
            $order->service_type = $data[4];
            $order->cat_id = $data[5];
            $order->sub_cat_id = $data[5];
            $order->tech_id = $tech->id;
            $order->user_id = $user->id;
            $order->completed = 0;
            $order->code = rand(1000, 9999);
            $order->scheduled_at = $scheduled_at;
            $order->save();

            $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
            $en_text = 'You have a new order request,please respond';

            TechNot::create
            (
                [
                    'type' => 'order',
                    'tech_id' => $order->tech_id,
                    'order_id' => $order->id,
                    'ar_text' => $ar_text,
                    'en_text' => $en_text
                ]
            );

            $token = TechToken::where('tech_id', $order->tech_id)->pluck('token');

            PushNotify::tech_send($token, $ar_text, $en_text, 'order', $order->id);

            $order_details = new OrderUserDetail();
            $order_details->order_id = $order->id;
            $order_details->place = $data[8];
            $order_details->part = $data[9];
            $order_details->desc = $data[10];
            $order_details->save();
        }
        return redirect('/provider/orders/open/waiting/upload/view')->with('success', 'Order uploaded successfully');

    }

    public function get_sub_category_provider($parent)
    {
        $arr_parent = explode(',',$parent);
        $providerSubscription = ProviderSubscription::where('provider_id', provider()->provider_id)->first();
        $subs = $providerSubscription->subs ?? serialize([]);
        $cats = Category::whereIn('id', unserialize($subs))->whereIn('parent_id', $arr_parent)->select('id','en_name')->get();

        return response()->json($cats);
    }

    public function get_third_category_provider($parent)
    {
        $arr_parent = explode(',',$parent);
        $cats = Category::whereIn('parent_id', $arr_parent)->select('id','en_name')->get();

        return response()->json($cats);
    }

    public function search($type,Request $request)
    {
        $search = Input::get('search');
            // dd($request->all());
        $providerSubscription = ProviderSubscription::where('provider_id', provider()->provider_id)->first();
        $subs = $providerSubscription->subs ?? serialize([]);
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $collaboration = Collaboration::where('provider_id',provider()->provider_id)->pluck('company_id');
        $companies = Company::whereIn('id', $collaboration)->select('id', 'en_name')->get();

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('provider_id', provider()->provider_id)->whereIn('company_id', $collaboration);
        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('provider_id', provider()->provider_id)->whereIn('company_id', $collaboration);

        if($type == 'monthly_parts_orders_count')
        {
            $show_orders = $monthly_orders->where('type','re_scheduled');
        }elseif($type == 'yearly_parts_orders_count')
        {
            $show_orders = $yearly_orders->where('type','re_scheduled');
        }elseif($type == 'all')
        {
            $show_orders = Order::where('provider_id', provider()->provider_id);
        }else{
            $show_orders = Order::where('provider_id', provider()->provider_id)->where('type', $type);
        }
        $get_orders = new Order;

        $orders = $get_orders->search(
            $show_orders,
            $search,
            $request->company_id,
            provider()->provider_id,
            $request->company_id,
            $request->sub_company,
            $request->from,
            $request->to,
            $request->main_cats,
            $request->sub_cats,
            $request->price_range,
            $request->service_type,
            $request->third_cats,
            $request->order_type,
            $request->provider_name,
            $request->order_status,
            $request->items_status
          );

        $orders = $orders['orders'];


        return view('provider.orders.search', compact('orders','search','companies','cats','type'));
    }


    public function show($id,Request $request)
    {
        $order = Order::findOrFail($id);
        if($order->provider_id != provider()->provider_id){
            abort(404);
        }
        $order_tracks = OrderTracking::where('order_id',$id)->pluck('date', 'status');
        $history_log =
        ActivityLog::where('subject_type', 'App\Models\Order')
        ->where('subject_id', $id)
        ->get();
       $payments = Payment::where('order_id', $id)->first();
        return view('provider.orders.show', compact('order', 'order_tracks', 'history_log','payments'));
    }

    public function cancel(Request $request, $type)
    {
        $order = Order::where('id', $request->order_id)->first();
        $order->canceled = 1;
        $order->type = "canceled";
        Technician::whereId($order->tech_id)->update(['busy'=>0]);
        $order->save();
        event(new \App\Events\Order\CancelOrderEvent($order));
        return redirect('/provider/orders/'.$type)->with('success', 'Order canceled successfully');
    }


    public function orders_request($type, Request $request)
    {
        $request->merge(['type' => $type]);
        $this->validate($request,
            [
                'type' => 'required|in:all,urgent,scheduled,re_scheduled,canceled'
            ]
        );

        $types['urgent'] = 'Urgent';
        $types['scheduled'] = 'Scheduled';
        $types['re_scheduled'] = 'Re-scheduled';
        $types['canceled'] = 'Canceled';

        return view('provider.orders.orders_request', compact('type','types'));
    }


    public function orders_show(Request $request)
    {
        $this->validate($request,
            [
                'type' => 'in:urgent,scheduled,re_scheduled,canceled',
                'from' => 'required|date',
                'to' => 'required|date'
            ],
            [
                'type.required' => 'Please choose a type',
                'type.exists' => 'Invalid Type',
                'from.required' => 'Please choose a date to start from',
                'from.date' => 'Please choose a valid date to start from',
                'to.required' => 'Please choose a date to end with',
                'to.date' => 'Please choose a valid date to end with',
            ]
        );

        if($request->type == 'canceled')
        {
            $orders = Order::where('provider_id', provider()->provider_id)->where('canceled', 1)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->get();
        }
        else
        {
            $orders = Order::where('provider_id', provider()->provider_id)->where('type', $request->type)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->get();
        }
        $orders[] = collect(['total' => $orders->sum('order_total')]);

        if($request->type == 'urgent') {$type_key = 'Urgent'; $type_value = 'Urgent'; }
        elseif($request->type == 'scheduled') {$type_key = 'Scheduled' ; $type_value = 'Scheduled';}
        elseif($request->type == 're_scheduled') {$type_key = 'Re-Scheduled' ; $type_value = 'Re-Scheduled';}
        elseif($request->type == 'canceled') {$type_key = 'Canceled' ; $type_value = 'Canceled';}

        $from = $request->from;
        $to = $request->to;

        return view('provider.orders.orders_show', compact('orders','type_key','type_value','from','to'));
    }


    public function orders_export(Request $request)
    {
        $this->validate($request,
            [
                'type' =>'in:Urgent,Scheduled,Re_scheduled,Canceled',
                'from' => 'required|date',
                'to' => 'required|date'
            ]
        );


        $orders = new Collection();
        if($request->type == 'canceled')
        {
            $get_orders = Order::where('provider_id', provider()->provider_id)->where('canceled', 1)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->get();
        }
        else
        {
            $get_orders = Order::where('provider_id', provider()->provider_id)->where('type', $request->type)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->get();
        }

        foreach($get_orders as $order)
        {
            if($order->type == 'urgent') $type = 'Urgent';
            elseif($order->type == 'scheduled') $type = 'Scheduled';
            elseif($order->canceled == 1) $type = 'Canceled';

            $collect['Category'] = $order->category->parent->en_name . ' - ' . $order->category->en_name;
            $collect['provider_name'] = $order->provider->en_name ?? 'null';
            $collect['company_name'] = $order->company->en_name ?? 'null';
            $collect['tech_name'] = $order->tech->en_name;
            $collect['user_name'] = $order->user->en_name;
            $collect['Date'] = $order->created_at->toDateTimeString();
            $collect['Type'] = $type;
            if($order->type == 'canceled') $collect['By'] = $order->canceled_by;
            $collect['Revenue'] = $order->order_total;
            $collect['Total'] = '';

            $orders = $orders->push($collect);
        }


        if($request->type == 'canceled')
        {
            $orders[] = collect(['Category' => '-','Date' => '-','Type' => '-','By' => '-','Cost' => '-','Total' => $orders->sum('Revenue')]);
        }
        else
        {
            $orders[] = collect(['Category' => '-','Date' => '-','Type' => '-','Cost' => '-','Total' => $orders->sum('Revenue')]);
        }

        if($get_orders->count() > 0)
        {
            $orders = $orders->toArray();
            $provider = Provider::where('id', provider()->provider_id)->select('en_name')->first();
            $from = $request->from;
            $to = $request->to;
            $p_name = str_replace(' ','-',$provider->en_name);

            $filename = 'qareeb_'.$p_name.'_'.$type.'_'.$from.'_'.$to.'_orders_invoice.xls';

            header("Content-Disposition: attachment; filename=\"$filename\"");
            header("Content-Type: application/vnd.ms-excel");

            $heads = false;
            foreach($orders as $order)
            {
                if($heads == false)
                {
                    echo implode("\t", array_keys($order)) . "\n";
                    $heads = true;
                }
                {
                    echo implode("\t", array_values($order)) . "\n";
                }
            }

            die();
        }
        else
        {
            return redirect('/provider/orders/'.$request->type)->with('error', 'No Result !');
        }

    }

//    public function excel_view()
//    {
//        return view('provider.orders.upload');
//    }
//
//    public function excel_upload(Request $request)
//    {
//        $this->validate($request,
//            [
//                'file' => 'required|file'
//            ]
//        );
//
//        $array = Excel::toArray(new OrderTechDetailsImport(), $request->file('file'));
//
//        foreach ($array[0] as $data)
//        {
//            $data = array_filter($data);
//            if(count($data) > 0)
//            {
//                try
//                {
//                    $request->merge(['order_id' => $data[0] , 'type_id' => $data[1], 'desc' => $data[2] ]);
//                }
//
//                catch (\Exception $e)
//                {
//                    return back()->with('error','Missing Column | '.$e->getMessage().',Offsets start from 0');
//                }
//
//                $this->validate($request,
//                [
//                   'order_id' => 'required|exists:orders,id',
//                   'type_id' => 'required|exists:categories,id',
//                   'desc' => 'required',
//                ],
//                [
//                    'order_id.required' => 'Missing data in Order ID column',
//                    'order_id.exists' => 'Invalid data in Order ID column,which is'.$request->order_id,
//                    'type.required' => 'Missing data in Third Category column',
//                    'type.exists' => 'Invalid data in Third Category ID column,which is'.$request->type,
//                    'desc.required' => 'Missing data in Description column'
//                ]);
//
//                $exist = OrderTechDetail::where('order_id', $data[0])->first();
//
//                if ($exist == NULL)
//                {
//                    $item = new OrderTechDetail();
//                    $item->order_id = $data[0];
//                    $item->type_id = $data[1];
//                    $item->desc = $data[2];
//                    $item->save();
//                }
//                else
//                {
//                    $item = OrderTechDetail::where('order_id', $data[0])->first();
//                    $item->type_id = $data[1];
//                    $item->desc = $data[2];
//                    $item->save();
//                }
//            }
//        }
//        return redirect('/provider/orders/urgent')->with('success', 'Order Technician uploaded successfully');
//    }

    public function excel_tech_request_view()
    {
        return view('provider.orders.tech_request_upload');
    }

    public function excel_tech_request_upload(Request $request)
    {
        $this->validate($request,
            [
                'file' => 'required|file'
            ]
        );

        $array = Excel::toArray(new OrderTechDetailsImport(), $request->file('file'));

        foreach ($array[0] as $data)
        {
            $data = array_filter($data);
            if(count($data) > 0 && count($data) <= 3000)
            {
                try
                {
                    $request->merge(['order_id' => $data[0] ]);
//                    , 'cat_id'=> $data[1], 'item_id'=> $data[2], 'working_hours' => $data[4]
                }

                catch (\Exception $e)
                {
                    return back()->with('error','Missing Column | '.$e->getMessage().',Offsets start from 0');
                }

                $this->validate($request,
                    [
                        'order_id' => 'required|exists:orders,smo',
                        'cat_id' => 'sometimes|exists:categories,id,type,3',
                        'item_id' => 'sometimes|exists:'.provider()->provider_id.'_warehouse_parts,code',
                        'taken' => 'sometimes',
                        'working_hours' => 'sometimes',
                        'desc' => 'sometimes',
                    ],
                    [
                        'order_id.required' => 'Missing data in Order ID column',
                        'order_id.exists' => 'Invalid data in Order ID column,which is'.$request->order_id,
                        'item_id.exists' => 'Invalid data in Item ID column,which is'.$request->item_id,
                    ]);

//                $datetime1 = new \DateTime(Carbon::now());//start time
//                $datetime2 = new \DateTime('2019-6-26 11:55:06');//end time
//                $interval = $datetime1->diff($datetime2);
//
//                dd($interval->format('%Y years %m months %d days %H hours %i minutes %s seconds'));

                $order = Order::where('smo',$data[0])->first();
                $parent_id = Category::where('id',$order->cat_id)->first();
                $category = Category::where('id', $parent_id->parent_id)->first();
                if($category->active == 0)
                {
                    $order->order_total = $data[5];
                    $order->item_total = $data[6];
                    $order->completed = 1;
                    $order->save();
                }else {

                    $finalResult = array();

                    $tempData = $data;
                    $amazing = array();
                    $theKey = 0;
                    for ($j = 0; $j < count($tempData); $j++) {
                        if ($j > 0) {
                            array_push($amazing, $tempData[$j]);
                        } else {
                            $theKey = $tempData[$j];
                            //$theResult['test'.$tempData[0]] = array();
                        }

                    }
                    if (empty($finalResult[$theKey])) {
                        $finalResult[$theKey] = array();
                    }

                    array_push($finalResult[$theKey], $amazing);


                    foreach ($finalResult as $finalCounterResult) {
                        foreach ($finalCounterResult as $final) {
                            $order = Order::where('smo',$theKey)->first();
                            if($final[1] != "null")
                            {
                                $id_item = DB::table(provider()->provider_id . '_warehouse_parts')->where('code', $final[1])->select('id')->first()->id;
                            }

                            if ($order->service_type == 1) {
                                return redirect('/provider/orders/urgent')->with('error', 'Can\'t upload this file while order service type is preview');
                            } else if ($order->service_type == 2 || $order->service_type == 3) {
                                $fee = ProviderCategoryFee::where('provider_id', $order->provider_id)->where('cat_id', $final[0])->first();
                                $working_hour = $final[3];
                                $get_fee = 0;

                                $get_all = $working_hour * isset($fee) ? $fee->third_fee : 50;

                                $get_fee += $get_all;

                                if ($order->smo == $theKey) {
                                    $order->check_price += $get_fee;
                                    $order->save();
                                }

                                if ($order->check_price > $order->order_total) {
                                    $order->order_total = $get_fee;
                                    $order->save();
                                }

                                if ($final[1] != "null") {
                                    $price = Warehouse::where('id', $id_item)->first();

                                    if ($order->smo == $theKey) {
                                        $total_item = isset($price) ? $price->price : 0 * $final[2];
                                        $order->item_total = $total_item;
                                        $order->save();
                                    }
                                }
                            }

//                        $exist = OrderTechRequest::where('order_id', $theKey)->first();
//
//                        if ($exist == NULL)
//                        {

                            $item = new OrderTechRequest();
                            $item->order_id = $order->id;
                            if ($final[1] != "null" && $final[2] != "null" ) {
                                $item->item_id = $id_item;
                                $item->taken = $final[2];
                            }

                            if ($final[4] != "null") {
                                $item->desc = $final[4];
                            }
                            $item->provider_id = provider()->provider_id;
                            $item->status = 'confirmed';
                            $item->save();


                            $orderTechDetail = new OrderTechDetail();
                            $orderTechDetail->order_id = $order->id;
                            $orderTechDetail->type_id = $final[0];
                            $orderTechDetail->working_hours = $final[3];
                            if ($final[4] != "null") $orderTechDetail->desc = $final[4];
                            $orderTechDetail->save();

                            $order->completed = 1;
                            $order->save();
//                        else
//                        {
//                            $item = OrderTechRequest::where('order_id', $theKey)->first();
//                            if(isset($final[1]) && isset($final[2]))
//                            {
//                                $item->item_id = $id_item;
//                                $item->taken = $final[2];
//                            }
//                            if(isset($final[4]))
//                            {
//                                $item->desc = $final[4];
//                            }
//                            $item->provider_id = provider()->provider_id;
//                            $item->status = 'confirmed';
//                            $item->save();
//
//                            $orderTechDetail = OrderTechDetail::where('order_id', $theKey)->first();
//
//                            $orderTechDetail->type_id = $final[0];
//                            $orderTechDetail->working_hours = $final[3];
//                            if(isset($final[4]))
//                            {
//                                $item->desc = $final[4];
//                            }
//                            $orderTechDetail->save();
//                        }

                        }
                    }
                }

            }else{
                return redirect('/provider/orders/urgent')->with('error', 'Can\'t upload this file while count rows more than 3000 row');
            }
        }
        return redirect('/provider/orders/urgent')->with('success', 'Order Technician uploaded successfully');
    }

    public function order_expenses(Request $request)
    {
        OrderExpense::create([
            'order_id' => $request->order_id,
            'name' => $request->name,
            'cost' => $request->cost,
        ]);

        event(new \App\Events\Order\AdjustOrderExpensesEvent($request->order_id));

        return back()->with('success', 'Order expense saved successfully');
    }

    public function finish()
    {
        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('provider_id', provider()->provider_id)->whereIn('company_id', $collaboration);
        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('provider_id', provider()->provider_id)->whereIn('company_id', $collaboration);


    }

    public function order_finish(Request $request)
    {
        if($request->item_ids || $request->taken)
        {
            $this->validate($request,[
                'item_ids'  => 'regex:/^\d(?:,\d)*$/',
                'taken'     => 'regex:/^\d(?:,\d)*$/',
            ]);
        }

        $order = Order::where('id', $request->order_id)->first();

        $jobs = $request->jobs;
        $first_key = $jobs ? key($jobs) : '';

        if($jobs[$first_key] != null) {
            if ($request->item_ids)
                {
                    $explode_item_id = explode(',', $request->item_ids);
                    $explode_taken = explode(',', $request->taken);

                    for ($i = 0; $i < count($explode_item_id); $i++)
                    {
                        $order = Order::where('id', $request->order_id)->select('id', 'tech_id', 'item_total', 'provider_id')->first();

                        $this_item = DB::table($order->provider_id . '_warehouse_parts')->where('id', $explode_item_id[$i])->first();

                        $item_request = new OrderTechRequest();
                        $item_request->order_id = $request->order_id;
                        $item_request->provider_id = $order->provider_id;
                        $item_request->item_id = $explode_item_id[$i];
                        $item_request->taken = $explode_taken[$i];
                        $item_request->save();

                        if ($this_item->count > 0) {
                            $item_request->status = 'confirmed';
                            $item_request->save();

                            DB::table($order->provider_id . '_warehouse_parts')->where('id', $explode_item_id[$i])->update(['count' => $this_item->count - 1]);

                            $order->item_total = $this_item->price * $item_request->taken;
                            $order->save();

                            OrderTracking::create([
                                'order_id' => $order->id,
                                'status' => 'Spare parts approved',
                                'date' => Carbon::now()
                            ]);
                        } else {
                            DB::table($order->provider_id . '_warehouse_parts')->where('id', $explode_item_id[$i])->update(['requested_count' => $this_item->requested_count + 1]);
                        }
                    }
                }

            $get_fee = 0;
            foreach ($jobs as $key => $value)
                {
                    if ($value != null) {

                        //for sure exp same size to working hour
                        $cat_id = $key;
                        $working_hour = $value;
                        $details = new OrderTechDetail();
                        $details->order_id = $request->order_id;
                        $details->type_id = $cat_id;
                        $details->desc = $request->desc;
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

                }


            if ($request->before_images && $request->after_images) {
                $update_details = OrderTechDetail::where('id', $details->id)->first();
                $before = [];
                foreach ($request->before_images as $image) {
                    $name = unique_file($image->getClientOriginalName());
                    $image->move(base_path() . '/public/orders/', $name);
                    array_push($before, $name);
                }

                $after = [];
                foreach ($request->after_images as $image) {
                    $name = unique_file($image->getClientOriginalName());
                    $image->move(base_path() . '/public/orders/', $name);
                    array_push($after, $name);
                }

                $update_details->before_images = serialize($before);
                $update_details->after_images = serialize($after);
                $update_details->save();
            }
            Technician::where('id', $order->tech_id)->update([
                'busy' => 0
            ]);

            $order->completed = 1;
            $order->save();

            OrderTracking::create([
                'order_id' => $order->id,
                'status' => 'Job completed',
                'date' => Carbon::now()
            ]);

            Technician::where('id', $request->tech_id)->update(['busy' => 0]);
        }
        elseif($request->desc)
        {
            $details = new OrderTechDetail();
            $details->order_id = $request->order_id;
            $details->type_id = $order->cat_id;
            $details->desc = $request->desc;
            $details->save();

            if($request->before_images && $request->after_images)
            {
                $update_details = OrderTechDetail::where('id', $details->id)->first();
                $before = [];
                foreach($request->before_images as $image)
                {
                    $name = unique_file($image->getClientOriginalName());
                    $image->move(base_path().'/public/orders/',$name);
                    array_push($before,$name);
                }

                $after = [];
                foreach($request->after_images as $image)
                {
                    $name = unique_file($image->getClientOriginalName());
                    $image->move(base_path().'/public/orders/',$name);
                    array_push($after,$name);
                }

                $update_details->before_images = serialize($before);
                $update_details->after_images = serialize($after);
                $update_details->save();
            }

            $order->completed = 1;
            $order->save();
        }
        else
        {
            $order_total = ProviderCategoryFee::where('cat_id', $order->cat_id)->where('provider_id', $order->provider_id)->where('company_id', $order->company_id)->first();

            if($order->type == 'urgent')
            {
                $order->order_total = $order_total->urgent_fee ?? 0;
            }else{
                $order->order_total = $order_total->scheduled_fee ?? 0;
            }

            Technician::where('id', $order->tech_id)->update(['busy' => 0]);
            $order->completed = 1;
            $order->save();
        }
        Technician::where('id', $order->tech_id)->update(['busy' => 0]);
        event(new \App\Events\Order\FinishOrderEvent($order));
        return back()->with('success','Order finished successfully');
    }
    public function order_Update(Request $request){
       $this->validate($request,[
            'order_id' => 'required|exists:orders,id',
            'status'   => 'required',
            'tech_id'  => 'required',
        ]);

        $date = (!empty($request->date)? $request->date : "") ." ". (!empty($request->time)? $request->time : "");
        if(!empty($request->date)){
            $date = Carbon::parse($date)->format('Y-m-d H:i:s');
        }


        $request->request->add(['timestamp'=> $date]);
        $request->request->add(['scheduled_at'=> $date]);

         // GET AUTH USER
        $technician = Technician::find($request->tech_id);

        // GET PROVIDER PARTNERS
        $providers = Collaboration::where('company_id', $technician->company_id)->pluck('provider_id');
        $technician = Technician::where('id', $request->tech_id)->whereIn('provider_id', $providers)->first();


        // VALIDATE TECH IS BELONG TO PARTNER PROVIDER
        if($request->has('tech_id') && !$technician){
            return back()->with('error','Sorry,this technician is not available anymore,please choose another one');
        }

        // VALAIDATE REQUEST HAS 'tech_id' IF ORDER IS URGENT TYPE
        if ($request->type == 'urgent' && !$request->has('tech_id')) {
            return back()->with('error','Sorry,this technician is not available anymore,please choose another one');

        }


        // TODO: VALIDATE IF TECH SERVE USER SubCompany

        // TODO: VALIDATE IF TECH IS ACTIVE

        // TODO: VALIDATE IF TECH IS CAN DO THIS CATEGORY SERVICE

        // TODO: VALIDATE IF TECH IS ONLINE && ACTIVE ROTATION IF URGENT ORDER

        // VALAIDATE TECH IS NOT BUSY IF ORDER IS URGENT
        if ($request->type == 'urgent' && $technician->busy) {
            return back()->with('error','Sorry,this technician is not available anymore,please choose another one');
        }

        // VALAIDATE REQUEST HAS 'scheduled_at' IF ORDER IS SCHEDULED TYPE
        if ($request->type == 'scheduled' && !$request->has('scheduled_at')) {
            return back()->with('error','invalid scheduled_at');
        }
        $typeArr = ['scheduled', 're_scheduled'];

        if (in_array($request->type, $typeArr) && $request->scheduled_at == " ") {
            return back()->with('error','please select a Scheduled Date for order type Schedule/Re-Scheduled!');
        }
        if ($request->scheduled_at !== " " && !in_array($request->type, $typeArr)  ) {
            return back()->with('error','please select a Type Schedule/Re-Scheduled for Schedule Date!');
        }


        $alreadyTech  =  Order::where('id', $request->order_id)->where('tech_id', $request->tech_id)->first();
        if(!empty($alreadyTech)){
            $alreadyTech = false;
        }else{
            $alreadyTech = true;
        }

        $status = Order_Tracking_Statuses(strval($request->status) , 'value');

        $lang = $request->header('lang');
        $order =  Order::where('id', $request->order_id)->first();
        $prev_techId  =  $order->tech_id;
        $dataOrder = array();
        $dataOrder['tech_id'] = $request->tech_id;
        $dataOrder['type'] = $request->type;

        if( $request->type == 're_scheduled' || $request->type == 'scheduled' ){

            $dataOrder['scheduled_at'] = $request->timestamp;

        }

        if(!empty($order)){
            $order->update($dataOrder);
        }

        OrderTracking::where('order_id', $request->order_id)->whereIn('status' ,['Service request','Technician selected', 'Assigned to Technician', 'Technician On the Way', 'Maintenance In Progress', 'Reschedule the Visit'])->update(['technicain_id'=> $request->tech_id ]);
        // 'status'   => 'Reschedule the visit',
        // FIXME: SHOULD BE firstOrCreate
      if($alreadyTech){

        $prevTechnician =  Technician::where('id', $prev_techId);
        if($prevTechnician->first()){
         $prevTechnician->update(['busy' => 0]);
         $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
         $en_text = 'Your order has been  assigned to another technician';
         $techNot = TechNot::where('tech_id', $prevTechnician->first()->id)->where('order_id', $request->order_id);

         if($techNot->first()){
             $techNot->delete();
         }
         TechNot::create(
             [
                 'type' => 'order',
                 'tech_id' => $prev_techId,
                 'order_id' => null,
                 'ar_text' => $ar_text,
                 'en_text' => $en_text
             ]
         );
         $token = TechToken::where('tech_id', $prev_techId)->pluck('token');
         PushNotify::tech_send($token, $ar_text, $en_text, 'order', null,  null,  0, $lang);

        }


      }


         if(!empty($request->timestamp)  && ( $request->type == 're_scheduled' || $request->type == 'scheduled' )){

            OrderTracking::create([
                'order_id' => $request->order_id,
                'status'   => "Reschedule the Visit",
                'technicain_id' => $request->tech_id,
                'date' => Carbon::now()
            ]);


            $orders = Order::where('id', $request->order_id)->with(['track', 'user' => function($q){
                $q->with('company');
            }])->first();

            if(!empty($orders) && !empty($orders) && !empty($orders['track']) ){

                $track = collect($orders['track'])->unique('technicain_id');
                $status =   !empty($order['user']['company']) && !empty($order['user']['company']['order_process_id']) &&  $order['user']['company']['order_process_id']  == 1 ? 'Technician selected' : 'Service request';

                $filtered = $track->filter(function($item) use($status){
                    return $item['status'] == $status;
                });
                $track = array_values(collect($filtered)->toArray());
                 foreach($track as $dataTeach){
                     if(!empty($dataTeach['technicain_id'])){
                         $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
                         $en_text = 'Admin want to Reschedule the visit';

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



            //Code here
         }

         if($alreadyTech){

         if(!empty($request->tech_id)){
            OrderTracking::create([
                'order_id' => $request->order_id,
                'status'   => "Assigned to Technician",
                'technicain_id' => $request->tech_id,
                'date' => Carbon::now()
            ]);


            if ($status) {
                $user_id = Order::whereId($request->order_id)->select('user_id')->first()->user_id;

                $ar_text = '';
                $en_text = '';

                UserNot::create([
                    'type' => 'order',
                    'user_id' => $user_id,
                    'order_id' => $request->order_id,
                    'ar_text' => $ar_text,
                    'en_text' => $en_text
                ]);

                $token = UserToken::where('user_id', $user_id)->pluck('token');
                PushNotify::user_send($token, $ar_text, $en_text, 'order', $request->order_id, null,  0, $lang);

            }
            if ($request->tech_id != null) {
                $technician->update(['busy' => 1]);
                $ar_text = 'لديك طلب خدمة جديد,الرجاء الإستجابة';
                $en_text = 'You have a new order request,please respond';

                TechNot::create(
                    [
                        'type' => 'order',
                        'tech_id' => $order->tech_id,
                        'order_id' => $order->id,
                        'ar_text' => $ar_text,
                        'en_text' => $en_text
                    ]
                );

                $token = TechToken::where('tech_id', $order->tech_id)->pluck('token');

                PushNotify::tech_send($token, $ar_text, $en_text, 'order', $order->id, null,  0, $lang);

            }

         }
        }

         return back()->with('success',success());

    }
}
