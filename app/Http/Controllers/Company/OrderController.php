<?php

namespace App\Http\Controllers\Company;

use App\Models\Category;
use App\Models\Collaboration;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Order;
use App\Models\OrderTechDetail;
use App\Models\OrderTracking;
use App\Models\OrderUserDetail;
use App\Models\Provider;
use App\Models\ProviderCategoryFee;
use App\Models\PushNotify;
use App\Models\SubCompany;
use App\Models\Technician;
use App\Models\TechNot;
use App\Models\TechToken;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrdersImport;
use App\Models\OrderProcessType;

class OrderController extends Controller
{
    public function index($type)
    {
        $company = Company::whereId(company()->company_id)->select('id')->first();
        $collaboration = Collaboration::where('company_id',$company->id)->pluck('provider_id');
        $providers = Provider::whereIn('id',$collaboration)->get();
        $subs = CompanySubscription::where('company_id', company()->company_id)->first();
        $cat_ids = Category::whereIn('id', isset($subs) ? unserialize($subs->subs) : [])->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();
        $provider_ids = Collaboration::where('company_id',company()->company_id)->pluck('provider_id');
        $this_month = new Carbon('first day of this month');
        if(company()->sub_company_id && company()->sub_company_id != null){
            $monthly_orders = Order::select('orders.*','users.id AS user_id','users.type AS user_type')->join('users', 'users.id', '=', 'orders.user_id')->whereIn('orders.provider_id', $provider_ids)->where('users.sub_company_id', company()->sub_company_id)->where('orders.created_at','>=', $this_month->toDateTimeString());
        }
        else {
            $monthly_orders = Order::raw('table orders')->whereIn('provider_id', $provider_ids)->where('company_id', company()->company_id)->where('created_at','>=', $this_month->toDateTimeString());
        }
        $this_year = new Carbon('first day of january this year');
        if(company()->sub_company_id && company()->sub_company_id != null){
           $yearly_orders = Order::select('orders.*','users.id AS user_id','users.type AS user_type')->join('users', 'users.id', '=', 'orders.user_id')->whereIn('orders.provider_id', $provider_ids)->where('users.sub_company_id', company()->sub_company_id)->where('orders.created_at','>=', $this_year->toDateTimeString())->get();
        }
        else {
            $yearly_orders = Order::raw('table orders')->whereIn('provider_id', $provider_ids)->where('company_id', company()->company_id)->where('created_at','>=', $this_year->toDateTimeString())->get();
        }
        if($type == 'monthly_parts_orders_count')
        {
            // $orders = $monthly_orders->where('user_type','re_scheduled');
            $orders = $monthly_orders->where('orders.user_type','re_scheduled');
        }elseif($type == 'yearly_parts_orders_count')
        {
            // $orders = $yearly_orders->where('user_type','re_scheduled');
            $orders = $yearly_orders->where('orders.user_type','re_scheduled');
        }
        elseif($type == 'all')
        {
            // $orders = Order::where('company_id', company()->company_id);
            //----------new query to join with users for sub company-----------//
            if(company()->sub_company_id && company()->sub_company_id != null){
                $orders = Order::select('orders.*','users.id AS user_id','users.type AS user_type')->join('users', 'users.id', '=', 'orders.user_id')->where('users.sub_company_id', company()->sub_company_id);
            } else {
                $orders = Order::where('company_id', company()->company_id);
            }
        }else{
            $orders = Order::where('company_id', company()->company_id)->where('type', $type);
        }
        // $orders = $orders->latest()->paginate(50);
        $orders = $orders->orderBy('orders.created_at', 'desc')->paginate(50);

        return view('company.orders.index', compact('orders','type','company','cats','providers'));
    }

    public function user_orders($type,$id)
    {
        $company = Company::whereId(company()->company_id)->select('id')->first();

        $subs = CompanySubscription::where('company_id', company()->company_id)->first()->subs;
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $orders = Order::where('company_id', company()->company_id)->where('user_id', $id)->paginate(50);
        return view('company.orders.index', compact('orders','type','company','cats','type'));
    }

    public function search($type,Request $request)
    {
        $company = Company::whereId(company()->company_id)->select('id')->first();
        $collaboration = Collaboration::where('company_id',$company->id)->pluck('provider_id');
        $providers = Provider::whereIn('id',$collaboration)->get();

        $search = Input::get('search');

        $subs = CompanySubscription::where('company_id', company()->company_id)->first();
        $cat_ids = Category::whereIn('id', isset($subs) ? unserialize($subs->subs) : [])->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $provider_ids = Collaboration::where('company_id',company()->company_id)->pluck('provider_id');

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('company_id', company()->company_id);
        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('company_id', company()->company_id);
        if($type == 'monthly_parts_orders_count')
        {
            $show_orders = $monthly_orders->where('type','re_scheduled');
        }elseif($type == 'yearly_parts_orders_count')
        {
            $show_orders = $yearly_orders->where('type','re_scheduled');
        }elseif($type == 'all')
        {
            $show_orders = Order::where('company_id', company()->company_id);
        }else{
            $show_orders = Order::where('company_id', company()->company_id)->where('type', $type);
        }
        $get_orders = new Order;

        $orders = $get_orders->search(
            $show_orders,
            $search,
            company()->company_id,
            $provider_ids,
            $request->company_id,
            $request->sub_company,
            $request->from,
            $request->to,
            $request->main_cats,
            $request->sub_cats,
            $request->price_range,
            $request->service_type,
            null,
            $request->order_type,
            null,
            $request->order_status,
            $request->items_status
        );

        $orders = $orders['orders'];

        return view('company.orders.search', compact('orders','search', 'company','cats','type','providers'));
    }


    public function show($id,Request $request)
    {
        $order = Order::findOrFail($id);
        if($order->company_id != company()->company_id){
          abort(404);
        }
        $order_tracks = OrderTracking::where('order_id',$id)->pluck('date', 'status');
        $history_log =
        ActivityLog::where('subject_type', 'App\Models\Order')
        ->where('subject_id', $id)
        ->get();
       $orderProcessId = !empty(company()->company) && !empty(company()->company->order_process_id)? company()->company->order_process_id : '';
       $orderProcessType  = '';
       if(!empty($orderProcessId)){
        $orderProcessType = OrderProcessType::where('id', $orderProcessId)->pluck('name')->first();
       }
       $order->stages = getAllTracks($orderProcessType);
        $payments = Payment::where('order_id', $id)->first();
        return view('company.orders.show', compact('order', 'order_tracks', 'history_log','payments'));
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

        return view('company.orders.orders_request', compact('type','types'));
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
            $orders = Order::where('company_id', company()->company_id)->where('canceled', 1)->where('created_at', '>=', $request->from)->where('created_at', '<=', $request->to)->get();
        }
        else
        {
            $orders = Order::where('company_id', company()->company_id)->where('type', $request->type)->where('created_at', '>=', $request->from)->where('created_at', '<=', $request->to)->get();
        }

        $orders[] = collect(['total' => $orders->sum('order_total')]);

        if($request->type == 'urgent') {$type_key = 'urgent' ; $type_value = 'Urgent';}
        elseif($request->type == 'scheduled') {$type_key = 'scheduled' ; $type_value = 'Scheduled';}
        elseif($request->type == 're_scheduled') {$type_key = 're_scheduled' ; $type_value = 'Re-Scheduled';}
        elseif($request->type == 'canceled') {$type_key = 'canceled' ; $type_value = 'Canceled';}

        $from = $request->from;
        $to = $request->to;

        return view('company.orders.orders_show', compact('orders','type_key','type_value','from','to'));
    }


    public function orders_export(Request $request)
    {
        $this->validate($request,
            [
                'type' =>'in:urgent,scheduled,re_scheduled,canceled',
                'from' => 'required|date',
                'to' => 'required|date'
            ]
        );


        $orders = new Collection();
        if($request->type == 'canceled')
        {
            $get_orders = Order::where('company_id', company()->company_id)->where('canceled', 1)->where('created_at', '>=', $request->from)->where('created_at', '<=', $request->to)->get();
        }
        else
        {
            $get_orders = Order::where('company_id', company()->company_id)->where('type', $request->type)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->get();
        }

        foreach($get_orders as $order)
        {
            if($order->type == 'urgent') $type = 'Urgent';
            elseif($order->type == 'scheduled') $type = 'Scheduled';
            elseif($order->type == 're_scheduled') $type = 'Re-Scheduled';


            $collect['Category'] = $order->category->parent->en_name . ' - ' . $order->category->en_name;
            $collect['Date'] = $order->created_at->toDateTimeString();
            $collect['Type'] = $type;
            if($order->canceled == 1)
            {
                if($order->canceled_by== 'user') $by = 'User';
                else $by = 'Tech';

                $collect['Canceled By'] = $by;
            }
            else
            {
                $collect['Canceled By'] = '-';
            }

            $collect['Cost'] = $order->order_total;
            $collect['Total'] = '';

            $orders = $orders->push($collect);
        }

        if($request->type == 'canceled')
        {
            $orders[] = collect(['Category' => '-','Date' => '-','Type' => '-','By' => '-','Cost' => '-','Total' => $orders->sum('Cost')]);
        }
        else
        {
            $orders[] = collect(['Category' => '-', 'Date' => '-', 'Type' => '-', 'Cost' => '-', 'Total' => $orders->sum('Cost')]);
        }

        if($get_orders->count() > 0)
        {
            $orders = $orders->toArray();

            $company = Company::where('id', company()->company_id)->select('en_name')->first();
            $from = $request->from;
            $to = $request->to;
            $p_name = str_replace(' ','-',$company->en_name);

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
            return redirect('/company/orders/'.$request->type)->with('error', 'No Result !');
        }

    }

    public function excel_view($type)
    {
        return view('company.orders.upload', compact('type'));
    }

    public function excel_upload(Request $request)
    {
        $this->validate($request,
            [
                'file' => 'required|file'
            ]
        );
        $array = Excel::toArray(new OrdersImport(),$request->file('file'));
//        unset($array[0][0]);

        if($request->type === 'urgent'){

            foreach($array[0] as $data){
                $data = array_filter($data);
                try{
                    $request->merge(['smo' => $data[1], 'provider_id' => $data[2],
                        'service_type'=>$data[3], 'cat_id' => $data[4],'tech_id' => $data[5], 'user_id' => $data[6],
                        'created_at'=>$data[10] ]);
                }

                catch (\Exception $e)
                {
                    return back()->with('error','Missing Column | '.$e->getMessage().',Offsets start from 0');
                }

                $this->validate($request,
                    [
                        'smo' => 'required',
                        'provider_id' => 'required|exists:providers,id',
                        'service_type' => 'required|in:1,2,3',
                        'cat_id' => 'required|exists:categories,id,type,2',
                        'tech_id' => 'required|exists:technicians,badge_id',
                        'user_id' => 'required|exists:users,badge_id',
                    ],
                    [
                        'smo.required' => 'Missing data in SMO column.',
                        'service_type.required' => 'Missing data in Service Type column.',
                        'service_type.in' => 'Service type must be 1 or 2 or 3.',
                        'provider_id.required' => 'Missing data in Provider ID column.',
                        'provider_id.exists' => 'Wrong ID in Provider ID column '.$request->provider_id.'.',
                        'cat_id.required' => 'Missing data in Category ID column.',
                        'cat_id.exists' => 'Wrong ID in Category ID column '.$request->cat_id.'.',
                        'tech_id.required' => 'Missing data in Technician ID column.',
                        'tech_id.exists' => 'Wrong ID in Technician ID column '.$request->tech_id.'.',
                        'user_id.required' => 'Missing data in User ID column.',
                        'user_id.exists' => 'Wrong ID in User ID column '.$request->user_id.'.',
                    ]
                );

                // Numbers of days between January 1, 1900 and 1970 (including 19 leap years)
//                define("MIN_DATES_DIFF", 25569);
//
//                // Numbers of second in a day:
//                define("SEC_IN_DAY", 86400 - 3);
//                function excel2timestamp($excelDate)
//                {
//                    if ($excelDate <= MIN_DATES_DIFF)
//                        return 0;
//
//                    return  ($excelDate - MIN_DATES_DIFF) * SEC_IN_DAY;
//                }
//
//                $created_at = excel2timestamp($data[10]);

                $UNIX_DATE = ($data[10] - 25569) * 86400;
                $created_at = gmdate("Y-m-d H:i:s", $UNIX_DATE);

                $provider_fee = ProviderCategoryFee::where('provider_id', $data[2])->where('cat_id', $data[4])->first();

                $tech = Technician::where('badge_id', $data[5])->select('id')->first();
                $user = User::where('badge_id', $data[6])->select('id')->first();

                $order = new Order();
                $order->timestamps = false;

                if(isset($data[0]))
                {
                    $order->id = $data[0];
                }
                $order->smo = $data[1];
                $order->type = 'urgent';
                $order->company_id = company()->company_id;
                $order->provider_id = $data[2];
                $order->service_type = $data[3];
                $order->cat_id = $data[4];
                $order->sub_cat_id = $data[4];
                $order->tech_id = $tech->id;
                $order->user_id = $user->id;
                $order->completed = 1;
                $order->code = rand(1000, 9999);
                $order->created_at = $created_at;
                if(isset($provider_fee))
                {
                    $order->order_total =  $provider_fee->urgent_fee;
                }else{
                    $order->order_total = Category::where('id', $data[4])->first()->urgent_price;
                }
                $order->save();

                OrderTracking::create([
                    'order_id' => $order->id,
                    'status' => 'Service request',
                    'date' => Carbon::now()
                ]);

                OrderTracking::create([
                    'order_id' => $order->id,
                    'status' => 'Technician selected',
                    'date' => Carbon::now()
                ]);

                if(isset($data[7])  && isset($data[8]) && isset($data[9]))
                {
                    $order_details = new OrderUserDetail();
                    $order_details->order_id = $order->id;
                    $order_details->place = $data[7];
                    $order_details->part = $data[8];
                    $order_details->desc = $data[9];
                    $order_details->created_at = $created_at;
                    $order_details->save();
                }
            }

        }
        else{

            foreach($array[0] as $data){
                $data = array_filter($data);

                try{
                    $request->merge(['smo' => $data[1], 'provider_id' => $data[2],
                        'service_type'=>$data[3],'cat_id' => $data[4],'tech_id' => $data[5], 'user_id' => $data[6],
                        'scheduled_at' => $data[10],'created_at' => $data[11] ]);

                }

                catch (\Exception $e)
                {
                    return back()->with('error','Missing Column | '.$e->getMessage().',Offsets start from 0');
                }


                $this->validate($request,
                    [
                        'smo' => 'required|unique:orders,smo',
                        'provider_id' => 'required|exists:providers,id',
                        'service_type' => 'required',
                        'cat_id' => 'required|exists:categories,id,type,2',
                        'tech_id' => 'required|exists:technicians,badge_id',
                        'user_id' => 'required|exists:users,badge_id',
                        'scheduled_at' => 'required'
                    ],
                    [
                        'smo.required' => 'Missing data in SMO column.',
                        'provider_id.required' => 'Missing data in Provider ID column.',
                        'service_type.required' => 'Missing data in Service Type column.',
                        'provider_id.exists' => 'Wrong ID in Provider ID column '.$request->provider_id.'.',
                        'cat_id.required' => 'Missing data in Category ID column.',
                        'cat_id.exists' => 'Wrong ID in Category ID column '.$request->cat_id.'.',
                        'tech_id.required' => 'Missing data in Technician ID column.',
                        'tech_id.exists' => 'Wrong ID in Technician ID column '.$request->tech_id.'.',
                        'user_id.required' => 'Missing data in User ID column.',
                        'user_id.exists' => 'Wrong ID in User ID column '.$request->user_id.'.',
                        'scheduled_at.required' => 'Missing data in Scheduled At Total column.',
                    ]
                );

// Numbers of days between January 1, 1900 and 1970 (including 19 leap years)
//                define("MIN_DATES_DIFF", 25569);
//
//// Numbers of second in a day:
//                define("SEC_IN_DAY", 86400 - 3);
//                function excel2timestamp($excelDate)
//                {
//                    if ($excelDate <= MIN_DATES_DIFF)
//                        return 0;
//
//                    return  ($excelDate - MIN_DATES_DIFF) * SEC_IN_DAY;
//                }
//
//                $time = excel2timestamp($data[10]);
//                $created_at = excel2timestamp($data[11]);

                $UNIX_DATE = ($data[10] - 25569) * 86400;
                $scheduled_at = gmdate("Y-m-d H:i:s", $UNIX_DATE);

                $UNIX_DATE = ($data[11] - 25569) * 86400;
                $created_at = gmdate("Y-m-d H:i:s", $UNIX_DATE);

                $provider_fee = ProviderCategoryFee::where('provider_id', $data[2])->where('cat_id', $data[4])->first();

                $tech = Technician::where('badge_id', $data[5])->select('id')->first();
                $user = User::where('badge_id', $data[6])->select('id')->first();

                $order = new Order();
                $order->timestamps = false;

                if(isset($data[0]))
                {
                    $order->id = $data[0];
                }
                $order->smo = $data[1];
                $order->type = 'scheduled';
                $order->company_id = company()->company_id;
                $order->provider_id = $data[2];
                $order->service_type = $data[3];
                $order->cat_id = $data[4];
                $order->sub_cat_id = $data[4];
                $order->tech_id = $tech->id;
                $order->user_id = $user->id;
                $order->completed = 1;
                $order->code = rand(1000, 9999);
                $order->scheduled_at = $scheduled_at;
                $order->created_at = $created_at;
                if(isset($provider_fee))
                {
                    $order->order_total =  $provider_fee->scheduled_fee;
                }else{
                    $order->order_total = Category::where('id', $data[4])->first()->scheduled_price;
                }
                $order->save();

                OrderTracking::create([
                    'order_id' => $order->id,
                    'status' => 'Service request',
                    'date' => Carbon::now()
                ]);

                OrderTracking::create([
                    'order_id' => $order->id,
                    'status' => 'Technician selected',
                    'date' => Carbon::now()
                ]);

                if(isset($data[7])  && isset($data[8]) && isset($data[9]))
                {
                    $order_details = new OrderUserDetail();
                    $order_details->order_id = $order->id;
                    $order_details->place = $data[7];
                    $order_details->part = $data[8];
                    $order_details->desc = $data[9];
                    $order_details->created_at = $created_at;
                    $order_details->save();
                }

//                $order = Order::create
////                (
////                    [
////                        'smo' => $data[0],
////                        'type' => 'scheduled',
////                        'company_id' => company()->company_id,
////                        'provider_id' => $data[1],
////                        'cat_id' => $data[2],
////                        'tech_id' => $data[3],
////                        'user_id' => $data[4],
////                        'completed' => 1,
////                        'code' => rand(1000, 9999),
////                        'item_total' => $data[8],
////                        'order_total' => $data[9],
////                        'scheduled_at' => date('Y-m-d H:i', $time),
////                        'created_at' => date('Y-m-d H:i', $created_at),
//////
////                    ]
////                );
            }

        }

        return redirect('/company/orders/'.$request->type.'/excel/view')->with('success', 'Order uploaded successfully');
    }

    public function excel_open_view($type)
    {
        return view('company.orders.upload_open', compact('type'));
    }

    public function excel_open_upload(Request $request)
    {
//        $this->validate($request,
//            [
//                'file' => 'required|file'
//            ]
//        );
//
//        $collaborations = Collaboration::where('company_id', company()->company_id)->select('provider_id')->get();
//        $file = $request->file->getClientOriginalName();
//        foreach ($collaborations as $collaboration)
//        {
//            if($file == $collaboration->provider_id.'_'.\company()->company_id.'_'.Carbon::now()->format('Y-m-d').'.xlsx')
//            {
//                $request->file->move(base_path() .'/public/orders/waiting/',$file);
//
//                return redirect('/company/orders/open/'.$request->type.'/excel/view')->with('success', 'Order uploaded successfully');
//            }else{
//                return redirect('/company/orders/open/'.$request->type.'/excel/view')->with('error', 'File name must be providerID_companyID_dateNow');
//            }
//
//        }

        $this->validate($request,
            [
                'file' => 'required|file'
            ]
        );
        $array = Excel::toArray(new OrdersImport(),$request->file('file'));

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
                $scheduled_at = null;
            }else{
                if( is_string($data[11]) )
                {
                    return back()->with('error', 'Choose a valid date');
                }
                $UNIX_DATE = ($data[11] - 25569) * 86400;
                $scheduled_at = gmdate("Y-m-d H:i:s", $UNIX_DATE);
                if($scheduled_at < Carbon::now()->format('Y-m-d H:i:s'))
                {
                    return redirect('/company/orders/open/urgent/excel/view')->with('error', 'Choose a valid date');
                }
            }

            $user = User::where('badge_id', $data[7])->first();
            $tech = Technician::where('badge_id', $data[6])->first();

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
        return redirect('/company/orders/open/'.$request->type.'/excel/view')->with('success', 'Order uploaded successfully');

    }

    public function bills()
    {
        $orders = Order::where('company_id', company()->company_id)->where('completed', 1)->paginate(50);

        return view('company.bills.index', compact('orders'));
    }

    public function bills_export()
    {

        $orders = Order::where('company_id', company()->company_id)->where('completed', 1)->get();

        foreach($orders as $order)
        {
            $order['Id'] = $order->id;
            $order['Smo'] = $order->smo;
            $order['Type'] = $order->type;
            $order['Technician'] = $order->tech->en_name;
            $order['status'] = 'Completed';
            $order['Service Fee'] = $order->get_cat_fee($order->id);
            $order['Items Total'] = $order->item_total;
            $order['Total Amount'] = $order->order_total;

            unset($order->tech,$order->id,$order->smo,$order->type,$order->company_id,$order->provider_id,
            $order->cat_id,$order->tech_id,$order->user_id,$order->code,$order->completed,$order->canceled,
            $order->canceled_by,$order->item_total,$order->order_total,$order->scheduled_at,$order->created_at,
            $order->updated_at);
        }

        $orders = $orders->toArray();
        $filename = 'qareeb_bills_data.xls';


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
}
