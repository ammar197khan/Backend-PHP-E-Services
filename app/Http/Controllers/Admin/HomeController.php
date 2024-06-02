<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Collaboration;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Order;
use App\Models\OrderRate;
use App\Models\OrderTechDetail;
use App\Models\OrderTechRequest;
use App\Models\Provider;
use App\Models\SubCompany;
use App\Models\Technician;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Payment;
use Carbon\Carbon;
use App\Models\OrderTracking;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Response;


class HomeController extends Controller
{
    public function getDownload(Request $request)
    {
    $file_name = $request->file_name ;

    if(!empty($file_name)){
        $file = base_path().'/public/providers/logos/'. $file_name;
        return Response::download($file);
    }
    return back();
    }
    public function dashboard()
    {
        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get();
        $data['monthly_orders_count'] = $monthly_orders->count();
        $data['monthly_open'] = $monthly_orders->where('completed', 0)->where('canceled',0)->count();
        $data['monthly_closed'] = $monthly_orders->where('completed', 1)->where('canceled',0)->count();
        $data['monthly_canceled'] = $monthly_orders->where('canceled', 1)->count();

        $yearly_orders = Order::whereYear('created_at', date('Y'))->get();
        $data['yearly_orders_count'] = $yearly_orders->count();
        $data['yearly_open'] = $yearly_orders->where('completed', 0)->where('canceled', 0)->count();
        $data['yearly_closed'] = $yearly_orders->where('completed', 1)->count();
        $data['yearly_canceled'] = $yearly_orders->where('canceled', 1)->count();

        $data['monthly_rate_commitment']  = OrderRate::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->avg('commitment');
        $data['monthly_rate_performance'] = OrderRate::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->avg('performance');
        $data['monthly_rate_appearance']  = OrderRate::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->avg('appearance');
        $data['monthly_rate_cleanliness'] = OrderRate::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->avg('cleanliness');
        $data['monthly_rate_count'] = OrderRate::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->groupby('order_id')->count();

        $data['yearly_rate_commitment']  = OrderRate::whereYear('created_at', date('Y'))->avg('commitment');
        $data['yearly_rate_performance'] = OrderRate::whereYear('created_at', date('Y'))->avg('performance');
        $data['yearly_rate_cleanliness'] = OrderRate::whereYear('created_at', date('Y'))->avg('cleanliness');
        $data['yearly_rate_appearance']  = OrderRate::whereYear('created_at', date('Y'))->avg('appearance');
        $data['yearly_rate_count']  = OrderRate::whereYear('created_at', date('Y'))->groupby('order_id')->count();


        $data['top_services']  = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service', 'service.id','=','categories.parent_id')->groupBy('service.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum', 'name');
        $data['top_providers'] = Order::select('providers.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('providers', 'orders.provider_id', '=', 'providers.id')->groupBy('providers.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum', 'name');
        $data['top_companies'] = Order::select('companies.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('companies', 'orders.company_id', '=', 'companies.id')->groupBy('companies.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum', 'name');
        $data['top_techs']     = Order::select('technicians.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('technicians', 'orders.tech_id', '=', 'technicians.id')->groupBy('technicians.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum', 'name');
        // $data['top_items']     = [];

        $data['least_services']  = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service', 'service.id', '=', 'categories.parent_id')->groupBy('service.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');;
        $data['least_providers'] = Order::select('providers.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('providers', 'orders.provider_id', '=', 'providers.id')->groupBy('providers.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');;
        $data['least_companies'] = Order::select('companies.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('companies', 'orders.company_id', '=', 'companies.id')->groupBy('companies.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');;
        $data['least_techs'] = Order::select('technicians.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('technicians', 'orders.tech_id', '=', 'technicians.id')->groupBy('technicians.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');;
        // $data['least_items'] = [];


        // MONTHLY TOTAL TANSACTIONS CASH CHART
        $monthly_revenue = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y%m') AS t"),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') AS m"),
                DB::raw('SUM(total_amount) as sum')
            )
            ->orderBy('t')
            ->groupBy('m', 't')
            ->pluck('sum', 'm');
        $data['monthly_revenue'] =  array_divide($monthly_revenue->toArray());

        if(count($monthly_revenue)) {
            $firstMonthTransaction = Carbon::parse($data['monthly_revenue'][0][0]);
            $lastMonthTransaction = Carbon::parse(end($data['monthly_revenue'][0]));
            $firstMonthChart = Carbon::parse(end($data['monthly_revenue'][0]))->subYear();
            $differenceInMonth = $firstMonthTransaction->diffInMonths($lastMonthTransaction->subYear());
            for ($i = 0; $i < $differenceInMonth - 1; $i++) {
                array_unshift($data['monthly_revenue'][0], $firstMonthTransaction->subMonth()->format('M Y'));
                array_unshift($data['monthly_revenue'][1], 0);
            }
        }

        // MONTHLY TOTAL TANSACTIONS COUNT CHART
        $monthly_revenue_count = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y%m') AS t"),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') AS m"),
                DB::raw('COUNT(id) as sum')
            )
            ->orderBy('t')
            ->groupBy('m', 't')
            ->pluck('sum', 'm');
        $data['monthly_revenue_count'] =  array_divide($monthly_revenue_count->toArray());

        if(count($monthly_revenue_count)) {
            $firstMonthTransaction = Carbon::parse($data['monthly_revenue_count'][0][0]);
            $lastMonthTransaction = Carbon::parse(end($data['monthly_revenue_count'][0]));
            $firstMonthChart = Carbon::parse(end($data['monthly_revenue_count'][0]))->subYear();
            $differenceInMonth = $firstMonthTransaction->diffInMonths($lastMonthTransaction->subYear());
            for ($i = 0; $i < $differenceInMonth - 1; $i++) {
                array_unshift($data['monthly_revenue_count'][0], $firstMonthTransaction->subMonth()->format('M Y'));
                array_unshift($data['monthly_revenue_count'][1], 0);
            }
        }

        // DAILY TOTAL TANSACTIONS CASH GRAPH
        $daily_revenue = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('SUM(total_amount) as sum') )/*->whereMonth('created_at', Date('m'))->whereYear('created_at', Date('Y'))*/->groupBy('d')->pluck('sum', 'd');
        $data['daily_revenue'] = [];
        foreach ($daily_revenue as $d => $sum) {
          $y = Date('y');
          $m = Date('m');
          $data['daily_revenue'][] = [
            // 't' => Carbon::parse("$y-$m-$d")->timestamp * 1000,
            't' => Carbon::parse($d)->timestamp * 1000,
            'y' => $sum
          ];
        }
        $data['daily_revenue'][] = [
          't' => Carbon::now()->timestamp * 1000,
          'y' => 0
        ];

        // DAILY TOTAL TANSACTIONS COUNT GRAPH
        $daily_revenue_count = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('COUNT(id) as sum') )/*->whereMonth('created_at', Date('m'))->whereYear('created_at', Date('Y'))*/->groupBy('d')->pluck('sum', 'd');
        $data['daily_revenue_count'] = [];
        foreach ($daily_revenue_count as $d => $sum) {
          $y = Date('y');
          $m = Date('m');
          $data['daily_revenue_count'][] = [
            // 't' => Carbon::parse("$y-$m-$d")->timestamp * 1000,
            't' => Carbon::parse($d)->timestamp * 1000,
            'y' => $sum
          ];
        }
        $data['daily_revenue_count'][] = [
          't' => Carbon::now()->timestamp * 1000,
          'y' => 0
        ];

        $monthly_parts_orders = $monthly_orders->where('type','re_scheduled');
        $data['monthly_parts_orders_count'] = $monthly_parts_orders->count();

        $monthly_parts = OrderTechRequest::whereIn('order_id', $monthly_parts_orders->pluck('id'));
        $data['monthly_parts_count'] = $monthly_parts->count();

        $monthly_parts_data = $monthly_parts->select('item_id','provider_id')->get();

        $monthly_arr= [];
        foreach($monthly_parts_data as $part)
        {
            $price = DB::table($part->provider_id.'_warehouse_parts')->where('id', $part->item_id)->select('price')->first()->price;
            array_push($monthly_arr, $price);
        }
        $data['monthly_parts_prices'] = array_sum($monthly_arr);

        $data['monthly_revenue_widget'] = $monthly_orders->sum('order_total');

        $yearly_parts_orders = $yearly_orders->where('type','re_scheduled');
        $data['yearly_parts_orders_count'] = $yearly_parts_orders->count();

        $yearly_parts = OrderTechRequest::whereIn('order_id', $yearly_parts_orders->pluck('id'));
        $data['yearly_parts_count'] = $yearly_parts->count();

        $yearly_parts_data = $yearly_parts->select('item_id','provider_id')->get();

        $yearly_arr= [];
        $p_price = "";
        foreach($yearly_parts_data as $part)
        {
            $price = DB::table($part->provider_id.'_warehouse_parts')->where('id', $part->item_id)->select('price')->first();
            if($price && $price != ""){ $p_price = $price->price; }
            array_push($yearly_arr, $p_price);
        }
        $data['yearly_parts_prices'] = array_sum($yearly_arr);

        $data['yearly_revenue_widget'] = $yearly_orders->sum('order_total');

        return view('admin.home.dashboard', compact('data'));
    }


    public function profile()
    {
        $admin = admin();

        if($admin->role == 'owner')
        {
            $admin['role'] = 'Owner';
        }
        elseif($admin->role == 'system_admin')
        {
            $admin['role'] = 'System Admin';
        }
        elseif($admin->role == 'app_manager')
        {
            $admin['role'] = 'App Manager';
        }
        elseif($admin->role == 'users_manager')
        {
            $admin['role'] = 'Users Manager';
        }
        elseif($admin->role == 'service_desk')
        {
            $admin['role'] = 'Service Desk';
        }
        elseif($admin->role == 'user')
        {
            $admin['role'] = 'User';
        }

        return view('admin.profile.admin', compact('admin'));
    }


    public function update_profile(Request $request)
    {
        $this->validate($request,
            [

                'name' => 'required',
                'email' => 'required|unique:admins,email,'.admin()->id,
                'phone' => 'required|unique:admins,phone,'.admin()->id
            ],
            [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists,please choose another one',
                'phone.required' => 'Phone is required',
                'phone.unique' => 'Phone already exists,please choose another one',

            ]
        );

        $admin = admin();
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
        $admin->save();

        return back()->with('success', 'Info changed successfully !');
    }


    public function change_password(Request $request)
    {
        $this->validate($request,
            [
                'password' => 'required|min:6|confirmed',
            ]
        );

        $admin = admin();
            $admin->password = Hash::make($request->password);
        $admin->save();

        return back()->with('success', 'Password changed successfully !');
    }


    public function get_cities($parent)
    {
        $cities = Address::where('parent_id', $parent)->get();
        return response()->json($cities);
    }


    public  function get_sub_cats($parent)
    {
        $arr_parent = explode(',',$parent);
        $cats = Category::whereIn('parent_id', $arr_parent)->select('id','en_name')->get();
        return response()->json($cats);
    }


    public function policy()
    {
        return view('policy');
    }

    public function get_sub_company($parent)
    {
        $arr_parent = explode(',',$parent);
        $sub_company = SubCompany::whereIn('parent_id', $arr_parent)->where('status', 'active')->select('id', 'en_name')->get();
        return response()->json($sub_company);
    }

    public function get_sub_category_provider($company_id,$parent)
    {
        $subs = CompanySubscription::where('company_id', $company_id)->first()->subs;
        $cats = Category::whereIn('id', unserialize($subs))->where('parent_id', $parent)->select('id','en_name')->get();
        return response()->json($cats);
    }

    public function date_orders($type)
    {
        $companies = Company::select('id', 'en_name')->get();
        $providers = Provider::select('id', 'en_name')->get();

        $cat_ids = Category::where('parent_id',null)->pluck('id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'));

        $yearly_orders = Order::whereYear('created_at', date('Y'));

        $get_orders = new Order;

        if($type == 'all')
        {
            $show_orders = $get_orders->check_search($type,null,null,$get_orders);
        }else{
            $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);
        }

        $orders = $show_orders->latest()->paginate(50);

        return view('admin.orders.index', compact('orders', 'type','providers','companies','cats'));
    }

    public function date_items($type)
    {
        $this_month = new Carbon('first day of this month');
        $monthly_orders = Order::raw('table orders')->where('created_at','>=', $this_month->toDateTimeString());

        $this_year = new Carbon('first day of january this year');
        $yearly_orders = Order::raw('table orders')->where('created_at','>=', $this_year->toDateTimeString());

        if($type == 'monthly_parts_count')
        {
            $orders = $monthly_orders->where('type','re_scheduled')->latest()->paginate(50);
        }
        elseif($type == 'yearly_parts_count')
        {
            $orders = $yearly_orders->where('type','re_scheduled')->latest()->paginate(50);
        }

        return view('admin.orders.show_items_dashboard',compact('orders','type'));
    }

    public function date_price($type)
    {
        $company_ids = Company::pluck('id');

        $this_month = new Carbon('first day of this month');
        $monthly_orders = Order::raw('table orders')->where('created_at','>=', $this_month->toDateTimeString());

        $this_year = new Carbon('first day of january this year');
        $yearly_orders = Order::raw('table orders')->where('created_at','>=', $this_year->toDateTimeString());

        if(strpos($type,'revenue'))
        {
            if($type == 'monthly_revenue')
            {
                $orders = $monthly_orders;
                $total_sum = $monthly_orders->sum('order_total');
            }
            elseif($type == 'yearly_revenue')
            {
                $orders = $yearly_orders;
                $total_sum = $yearly_orders->sum('order_total');
            }
            $orders = $orders->paginate(50);

            return view('admin.orders.price_statistics',
                compact('orders','company_ids','provider_ids','total_sum'));

        }else{
            $monthly_parts_orders = $monthly_orders->where('type','re_scheduled');
            $yearly_parts_orders = $yearly_orders->where('type','re_scheduled');

            if($type == 'monthly_parts_prices')
            {
                $monthly_parts = OrderTechRequest::whereIn('order_id', $monthly_parts_orders->pluck('id'));
                $monthly_parts_data = $monthly_parts->select('item_id','provider_id')->get();

                $monthly_arr= [];
                foreach($monthly_parts_data as $part)
                {
                    $price = DB::table($part->provider_id.'_warehouse_parts')->where('id', $part->item_id)->select('price')->first()->price;
                    array_push($monthly_arr, $price);
                }
                $total_sum = array_sum($monthly_arr);

                $orders = $monthly_parts_orders;
            }
            elseif($type == 'yearly_parts_prices')
            {
                $yearly_parts = OrderTechRequest::whereIn('order_id', $yearly_parts_orders->pluck('id'));
                $yearly_parts_data = $yearly_parts->select('item_id','provider_id')->get();

                $yearly_arr= [];
                foreach($yearly_parts_data as $part)
                {
                    $price = DB::table($part->provider_id.'_warehouse_parts')->where('id', $part->item_id)->select('price')->first()->price;
                    array_push($yearly_arr, $price);
                }
                $total_sum = array_sum($yearly_arr);

                $orders = $yearly_parts_orders;
            }
            $orders = $orders->paginate(50);

            return view('admin.orders.item_statistics',compact('orders','company_ids','total_sum'));
        }
    }

    public function data_rate_orders($type)
    {
        //month
        $this_month = new Carbon('first day of this month');
        $monthly_orders = Order::raw('table orders')->where('created_at','>=', $this_month->toDateTimeString())->get();
        $monthly_orders_ids = $monthly_orders->pluck('id');
        $monthly_rates_ids = OrderRate::whereIn('order_id', $monthly_orders_ids)->pluck('order_id');

        //year
        $this_year = new Carbon('first day of january this year');
        $yearly_orders = Order::raw('table orders')->where('created_at','>=', $this_year->toDateTimeString())->get();
        $yearly_orders_ids = $yearly_orders->pluck('id');
        $yearly_rates_ids = OrderRate::whereIn('order_id', $yearly_orders_ids)->pluck('order_id');

        if($type == 'monthly_rate'){
            $orders =  Order::whereIn('id', $monthly_rates_ids)->get();
        }elseif($type == 'yearly_rate'){
            $orders = Order::whereIn('id', $yearly_rates_ids)->get();
        }
        return view('admin.orders.rate_dashboard', compact('orders', 'type'));
    }

    public function date_re_orders($type)
    {
        $companies = Company::select('id', 'en_name')->get();

        $cat_ids = Category::pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $providers = Provider::all();

        $monthly_orders = Order::whereMonth('created_at', date('m'));
        $yearly_orders = Order::whereYear('created_at', date('Y'));

        if($type == 'monthly_parts_orders_count')
        {
            $orders = $monthly_orders->where('type','re_scheduled');
        }elseif($type == 'yearly_parts_orders_count')
        {
            $orders = $yearly_orders->where('type','re_scheduled');
        }

        $orders = $orders->latest()->paginate(50);
        return view('admin.orders.index', compact('orders','type',
            'cats','companies','providers'));
    }

    public function show(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order_tracks = OrderTracking::where('order_id',$id)->pluck('date', 'status');
        $history_log =
        ActivityLog::where('subject_type', 'App\Models\Order')
        ->where('subject_id', $id)
        ->get();
$payments = Payment::where('order_id', $id)->first();
        return view('admin.orders.show', compact('order', 'order_tracks', 'history_log','payments'));
    }

    public function search($type,Request $request)
    {
        $companies = Company::select('id', 'en_name')->get();
        $providers = Provider::select('id', 'en_name')->get();

        $cat_ids = Category::where('parent_id',null)->pluck('id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $provider_ids = Provider::pluck('id');

        $monthly_orders = Order::whereMonth('created_at', date('m'));
        $yearly_orders = Order::whereYear('created_at', date('Y'));

        $search = Input::get('search');

        $get_orders = new Order;

        if($type == 'all')
        {
            $show_orders = $get_orders->check_search($type,null,null,$get_orders);
        }else{
            $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);
        }

        $orders = $get_orders->search(
            $show_orders,
            $search,
            $request->company_id,
            $provider_ids,
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

        return view('admin.orders.index', compact('orders','search', 'type','companies','cats','providers'));
    }
    function setLocal(Request $request){
        $data = array();
        $lan = $request->lan;

        $lan =  substr($lan, 0, 2);
        $lan = strtolower($lan);
        \Session::put('current_locale',$lan);
        $data['success'] = true;
        return $data;
    }
}
