<?php

namespace App\Http\Controllers\Company;

use App\Models\Address;
use App\Models\Category;
use App\Models\Collaboration;
use App\Models\Company;
use App\Models\CompanyAdmin;
use App\Models\CompanySubscription;
use App\Models\Order;
use App\Models\OrderRate;
use App\Models\OrderTechRequest;
use App\Models\Provider;
use App\Models\Rotation;
use App\Models\SubCompany;
use App\Models\Technician;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Response;
use Illuminate\Support\Facades\Input;

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
    public function dashboard(Request $request)
    {
        $formRequest = $request->from;
        if(!empty($formRequest)){
            $formRequest = Carbon::createFromFormat('d/m/yy', $formRequest);
            $formRequest = Carbon::parse($formRequest)->format('Y-m-d');
        }


        $toRequest = $request->to;
       if(!empty($toRequest)){
        $toRequest = Carbon::createFromFormat('d/m/yy', $toRequest);
        $toRequest = Carbon::parse($toRequest)->format('Y-m-d');
       }
        $provider_name_Request = $request->provider_name;
        $sub_company_Request = $request->sub_company;

        if($sub_company_Request){
            $sub_company_Request = User::whereIn('sub_company_id',  $sub_company_Request)->pluck('id');
        }


        $sub_company = SubCompany::where('parent_id', company()->company_id)->where('status', 'active')->select('id', 'en_name')->get();
        $company = Company::where('id',company()->company_id)->select('id')->first();
        $collaboration = Collaboration::where('company_id',$company->id)->pluck('provider_id');
        $providers = Provider::whereIn('id',$collaboration)->get();

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('company_id', company()->company_id)->get();
        $data['monthly_orders_count'] = $monthly_orders->count();
        $data['monthly_open'] = $monthly_orders->where('completed', 0)->where('canceled',0)->count();
        $data['monthly_closed'] = $monthly_orders->where('completed', 1)->where('canceled',0)->count();
        $data['monthly_canceled'] = $monthly_orders->where('canceled', 1)->count();

        $yearly_orders = Order::whereYear('created_at',date('Y'))->where('company_id', company()->company_id)->get();
        $data['yearly_orders_count'] = $yearly_orders->count();
        $data['yearly_open'] = $yearly_orders->where('completed', 0)->where('canceled', 0)->count();
        $data['yearly_closed'] = $yearly_orders->where('completed', 1)->where('canceled', 0)->count();
        $data['yearly_canceled'] = $yearly_orders->where('canceled', 1)->count();

//        $monthly_orders_rate = OrderRate::whereMonth('created_at', date('m'))->whereYear('created_at',date('Y'))->where('company_id',company()->company_id)->get();
//        $data['monthly_rate_count'] = $monthly_orders_rate->groupby('order_id')->count();
//        $data['monthly_rate_commitment'] = $monthly_orders_rate->avg('commitment');
//        $data['monthly_rate_performance'] = $monthly_orders_rate->avg('performance');
//        $data['monthly_rate_appearance'] = $monthly_orders_rate->avg('appearance');
//        $data['monthly_rate_cleanliness'] = $monthly_orders_rate->avg('cleanliness');
//
//        $yearly_orders_rate = OrderRate::whereYear('created_at',date('Y'))->where('company_id',company()->company_id)->get();
//        $data['yearly_rate_count'] = $yearly_orders_rate->groupby('order_id')->count();
//        $data['yearly_rate_commitment'] = $yearly_orders_rate->avg('commitment');
//        $data['yearly_rate_performance'] = $yearly_orders_rate->avg('performance');
//        $data['yearly_rate_appearance'] = $yearly_orders_rate->avg('appearance');
//        $data['yearly_rate_cleanliness'] = $yearly_orders_rate->avg('cleanliness');

        $data['top_services'] = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum'));

        if(!empty($formRequest) && !empty($toRequest)){
            $data['top_services'] = $data['top_services']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }

        if(!empty($provider_name_Request)){
            $data['top_services'] = $data['top_services']->whereIn('orders.provider_id', $provider_name_Request);
        }
        if(!empty($sub_company_Request)){
            $data['top_services'] = $data['top_services']->whereIn('orders.user_id', $sub_company_Request);
        }

        $data['top_services'] = $data['top_services']->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')->where('company_id',company()->company_id)->groupby('service.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');

        $data['top_users'] = Order::select('users.en_name as name', DB::raw('SUM(total_amount) as sum'));
        if(!empty($formRequest) && !empty($toRequest)){
            $data['top_users'] = $data['top_users']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }
        if(!empty($provider_name_Request)){
            $data['top_users'] = $data['top_users']->whereIn('orders.provider_id', $provider_name_Request);
         }
        if(!empty($sub_company_Request)){
            $data['top_users'] = $data['top_users']->whereIn('orders.user_id', $sub_company_Request);
        }

        $data['top_users'] =  $data['top_users']->join('users', 'orders.user_id', '=', 'users.id')->where('orders.company_id',company()->company_id)->groupby('users.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');


        $data['top_sub_companies'] = Order::select('sub_companies.en_name as name', DB::raw('SUM(total_amount) as sum'));
        if(!empty($formRequest) && !empty($toRequest)){
            $data['top_sub_companies'] = $data['top_sub_companies']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }
        if(!empty($provider_name_Request)){
            $data['top_sub_companies'] = $data['top_sub_companies']->whereIn('orders.provider_id', $provider_name_Request);
         }
         if(!empty($sub_company_Request)){
            $data['top_sub_companies'] = $data['top_sub_companies']->whereIn('orders.user_id', $sub_company_Request);
        }
        $data['top_sub_companies'] = $data['top_sub_companies']->join('users', 'orders.user_id', '=', 'users.id')->join('sub_companies', 'users.sub_company_id', '=', 'sub_companies.id')->where('orders.company_id',company()->company_id)->groupby('sub_companies.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');

        $data['least_services']  = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum') );
        if(!empty($formRequest) && !empty($toRequest)){
            $data['least_services'] = $data['least_services']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }
        if(!empty($provider_name_Request)){
            $data['least_services'] = $data['least_services']->whereIn('orders.provider_id', $provider_name_Request);
         }
        if(!empty($sub_company_Request)){
            $data['least_services'] = $data['least_services']->whereIn('orders.user_id', $sub_company_Request);
        }
        $data['least_services']  = $data['least_services']->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')->where('company_id', company()->company_id)->groupBy('service.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');
        $data['least_users'] = Order::select('users.en_name as name', DB::raw('SUM(total_amount) as sum'));
        if(!empty($formRequest) && !empty($toRequest)){
            $data['least_users'] = $data['least_users']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }
        if(!empty($provider_name_Request)){
            $data['least_users'] = $data['least_users']->whereIn('orders.provider_id', $provider_name_Request);
         }
        if(!empty($sub_company_Request)){
            $data['least_users'] = $data['least_users']->whereIn('orders.user_id', $sub_company_Request);
        }
        $data['least_users'] = $data['least_users']->join('users', 'orders.user_id', '=', 'users.id')->where('orders.company_id',company()->company_id)->groupby('users.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum','name');

        $data['least_sub_companies'] = Order::select('sub_companies.en_name as name', DB::raw('SUM(total_amount) as sum'));
        if(!empty($formRequest) && !empty($toRequest)){
            $data['least_sub_companies'] = $data['least_sub_companies']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }
        if(!empty($provider_name_Request)){
            $data['least_sub_companies'] = $data['least_sub_companies']->whereIn('orders.provider_id', $provider_name_Request);
         }
        if(!empty($sub_company_Request)){
            $data['least_sub_companies'] = $data['least_sub_companies']->whereIn('orders.user_id', $sub_company_Request);
        }
        $data['least_sub_companies'] = $data['least_sub_companies']->join('sub_companies', 'orders.company_id', '=', 'sub_companies.parent_id')->where('orders.company_id',company()->company_id)->groupby('sub_companies.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum','name');

        // MONTHLY TOTAL TANSACTIONS CASH CHART
        $monthly_revenue = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y%m') AS t"),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') AS m"),
                DB::raw('SUM(total_amount) as sum')
            )
            ->where('company_id', company()->company_id);
          if(!empty($formRequest) && !empty($toRequest)){
            $monthly_revenue = $monthly_revenue->whereDate('created_at','<=',$toRequest)
            ->whereDate('created_at','>=',$formRequest);
          }
          if(!empty($provider_name_Request)){
            $monthly_revenue = $monthly_revenue->whereIn('provider_id', $provider_name_Request);
         }
         if(!empty($sub_company_Request)){
            $monthly_revenue = $monthly_revenue->whereIn('user_id', $sub_company_Request);
        }

          $monthly_revenue = $monthly_revenue->orderBy('t')
            ->groupBy('m', 't')
            ->pluck('sum', 'm');

        $data['monthly_revenue'] =  array_divide($monthly_revenue->toArray());
        if(count($monthly_revenue) > 1) {
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
            ->where('company_id', company()->company_id);
            if(!empty($formRequest) && !empty($toRequest)){
                $monthly_revenue_count = $monthly_revenue_count->whereDate('created_at','<=',$toRequest)
                ->whereDate('created_at','>=',$formRequest);
              }
            if(!empty($provider_name_Request)){
                $monthly_revenue_count = $monthly_revenue_count->whereIn('provider_id', $provider_name_Request);
             }
            if(!empty($sub_company_Request)){
                $monthly_revenue_count = $monthly_revenue_count->whereIn('user_id', $sub_company_Request);
            }
              $monthly_revenue_count =   $monthly_revenue_count->orderBy('t')
            ->groupBy('m', 't')
            ->pluck('sum', 'm');
        $data['monthly_revenue_count'] =  array_divide($monthly_revenue_count->toArray());

        if(count($monthly_revenue_count) > 1) {
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
        // $daily_revenue = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('SUM(total_amount) as sum') )->whereMonth('created_at', Date('m'))->whereYear('created_at', Date('Y'))->where('company_id', company()->company_id)->groupBy('d')->pluck('sum', 'd');
        $daily_revenue = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('SUM(total_amount) as sum') );
        if(!empty($formRequest) && !empty($toRequest)){
            $daily_revenue =  $daily_revenue->whereDate('created_at','<=',$toRequest)
            ->whereDate('created_at','>=',$formRequest);
          }
        if(!empty($provider_name_Request)){
            $daily_revenue =  $daily_revenue->whereIn('provider_id', $provider_name_Request);
         }
         if(!empty($sub_company_Request)){
            $daily_revenue =  $daily_revenue->whereIn('user_id', $sub_company_Request);
        }
        /*->whereMonth('created_at', Date('m'))->whereYear('created_at', Date('Y'))*/
        $daily_revenue = $daily_revenue->where('company_id', company()->company_id)->groupBy('d')->pluck('sum', 'd');
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
        // $daily_revenue = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('SUM(total_amount) as sum') )->whereMonth('created_at', Date('m'))->whereYear('created_at', Date('Y'))->where('company_id', company()->company_id)->groupBy('d')->pluck('sum', 'd');
        $daily_revenue_count = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('COUNT(id) as sum') );/*->whereMonth('created_at', Date('m'))->whereYear('created_at', Date('Y'))*/
        if(!empty($formRequest) && !empty($toRequest)){
            $daily_revenue_count =  $daily_revenue_count->whereDate('created_at','<=',$toRequest)
            ->whereDate('created_at','>=',$formRequest);
          }
        if(!empty($provider_name_Request)){
            $daily_revenue_count =  $daily_revenue_count->whereIn('provider_id', $provider_name_Request);
         }
         if(!empty($sub_company_Request)){
            $daily_revenue_count =  $daily_revenue_count->whereIn('user_id', $sub_company_Request);
        }

         $daily_revenue_count =  $daily_revenue_count->where('company_id', company()->company_id)->groupBy('d')->pluck('sum', 'd');
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

        return view('company.dashboard', compact('data', 'providers', 'sub_company'));
    }

    public function get_sub_company()
    {
        $sub_company = SubCompany::where('parent_id', company()->company_id)->where('status', 'active')->select('id', 'en_name')->get();
        return response()->json($sub_company);
    }

    public function get_sub_category($parent)
    {
        $arr_parent = explode(',',$parent);
        $subs = CompanySubscription::where('company_id', company()->company_id)->first()->subs;
        $cats = Category::whereIn('id', unserialize($subs))->whereIn('parent_id', $arr_parent)->select('id','en_name')->get();

        return response()->json($cats);
    }
    public function get_sub_cate($parent)
    {
        $subs = CompanySubscription::where('company_id', company()->company_id)->first()->subs;
        $cats = Category::whereIn('id', unserialize($subs))->where('parent_id', $parent)->select('id','en_name')->get();

        return response()->json($cats);
    }

    public function get_cities($parent)
    {
        $cities = Address::where('parent_id', $parent)->get();
        return response()->json($cities);
    }


    public function get_subs($parent)
    {
        $subs = CompanySubscription::where('company_id', company()->company_id)->first()->subs;
        $cats = Category::whereIn('id', unserialize($subs))->where('parent_id', $parent)->select('id','en_name')->get();

        return response()->json($cats);
    }

    public function get_technician($parent)
    {
    //        $explode = explode(",", $parent);
//        $tech = Technician::where('cat_ids', 'like', "%$parent%")->select('id','en_name')->get();
        $provider = Collaboration::where('company_id', company()->company_id)->pluck('provider_id');
       $techRole = '';
       if(!empty(Company()->company) && !empty(Company()->company->order_process_id) && Company()->company->order_process_id == 1){
        $techRole = 2;
       }elseif(!empty(Company()->company) && !empty(Company()->company->order_process_id) && Company()->company->order_process_id == 2){
        $techRole = 1;
       }
        $show_techs = Technician::whereIn('provider_id', $provider)->where('technician_role_id' , $techRole)->where('active', 1)->where('online', 1)->where('busy', 0)->select('id','en_name','rotation_id','cat_ids', 'technician_role_id')->with('TechnicainRole')->get();

        $techs = [];
        foreach ($show_techs as $show_tech)
        {
            $cat_ids = explode(',', $show_tech->cat_ids);
            foreach ($cat_ids as $cat_id)
            {
                if($parent == $cat_id)
                {
                    if(isset($show_tech->rotation_id))
                    {
                        $rotation = Rotation::where('id',$show_tech->rotation_id)->first();

                        if($rotation->from <= Carbon::now()->format('H:i') && $rotation->to >= Carbon::now()->format('H:i'))
                        {

                            array_push($techs, $show_tech);
                        }
                    }
                    else{
                        array_push($techs, $show_tech);
                    }
                }
            }
//            $array_search = array_search($cat_id, $get_items);
        }
        return response()->json( $techs);
    }

    public function date_orders($type)
    {
        $company = Company::where('id',company()->company_id)->select('id')->first();
        $collaboration = Collaboration::where('company_id',$company->id)->pluck('provider_id');
        $providers = Provider::whereIn('id',$collaboration)->get();

        $subs = CompanySubscription::where('company_id', company()->company_id)->first();
        if(isset($subs)){
            $cat_ids = Category::whereIn('id', unserialize($subs->subs))->pluck('parent_id');
            $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();
        }else{
            $cats = Category::where('parent_id', null)->get();
        }

        $yearly_orders = Order::whereYear('created_at',date('Y'))->where('company_id', company()->company_id);
        $monthly_orders = Order::whereMonth('created_at', date('m') )->whereYear('created_at', date('Y') )->where('company_id', company()->company_id);

        $get_orders = new Order;

        $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);

        $orders = $show_orders->latest()->paginate(50);

        return view('company.orders.year_dashboard', compact('orders', 'type','company','cats','providers'));
    }

    public function date_items($type)
    {
        $this_month = new Carbon('first day of this month');
        $this_year = new Carbon('first day of january this year');

        $monthly_orders = Order::raw('table orders')->where('company_id', company()->company_id)->where('created_at','>=', $this_month->toDateTimeString());
        $yearly_orders = Order::raw('table orders')->where('company_id', company()->company_id)->where('created_at','>=', $this_year->toDateTimeString());

        if($type == 'monthly_parts_count')
        {
            $orders = $monthly_orders->where('type','re_scheduled')->latest()->paginate(50);
        }
        elseif($type == 'yearly_parts_count')
        {
            $orders = $yearly_orders->where('type','re_scheduled')->latest()->paginate(50);
        }

        return view('company.orders.show_items_dashboard',compact('orders','type'));
    }

    public function date_price($type)
    {
        $this_month = new Carbon('first day of this month');
        $this_year = new Carbon('first day of january this year');

        $monthly_orders = Order::raw('table orders')->where('company_id', company()->company_id)->where('created_at','>=', $this_month->toDateTimeString());
        $yearly_orders = Order::raw('table orders')->where('company_id', company()->company_id)->where('created_at','>=', $this_year->toDateTimeString());

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

            return view('company.orders.price_statistics',compact('orders','total_sum'));

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

            return view('company.orders.item_statistics',compact('orders','company_ids','total_sum'));}
    }

    public function data_rate_orders($type)
    {
        //month
        $this_month = new Carbon('first day of this month');
        $monthly_orders = Order::raw('table orders')->where('company_id', company()->company_id)->where('created_at','>=', $this_month->toDateTimeString())->get();
        $monthly_orders_ids = $monthly_orders->pluck('id');
        $monthly_rates_ids = OrderRate::whereIn('order_id', $monthly_orders_ids)->pluck('order_id');

        //year
        $this_year = new Carbon('first day of january this year');
        $yearly_orders = Order::raw('table orders')->where('company_id', company()->company_id)->where('created_at','>=', $this_year->toDateTimeString())->get();
        $yearly_orders_ids = $yearly_orders->pluck('id');
        $yearly_rates_ids = OrderRate::whereIn('order_id', $yearly_orders_ids)->pluck('order_id');

        if($type == 'monthly_rate'){
            $orders =  Order::whereIn('id', $monthly_rates_ids)->get();
        }elseif($type == 'yearly_rate'){
            $orders = Order::whereIn('id', $yearly_rates_ids)->get();
        }
        return view('company.orders.rate_dashboard', compact('orders', 'type'));
    }

    public function search($type,Request $request)
    {
        $company = Company::where('id', company()->company_id)->select('id')->first();
        $provider_ids = Collaboration::where('company_id',$company->id)->pluck('provider_id');
        $providers = Provider::whereIn('id',$provider_ids)->get();

        $subs = CompanySubscription::where('company_id', company()->company_id)->first()->subs;
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $this_year = new Carbon('first day of january this year');
        $this_month = new Carbon('first day of this month');

        $yearly_orders = Order::raw('table orders')->where('company_id', company()->company_id)->where('created_at','>=', $this_year->toDateTimeString());
        $monthly_orders = Order::raw('table orders')->where('company_id', company()->company_id)->where('created_at','>=', $this_month->toDateTimeString());

        $search = Input::get('search');


        $get_orders = new Order;

        $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);

        $orders = $get_orders->search(
            $show_orders,
            $search,
            $company->id,
            $provider_ids,
            $request->company_id,
            $request->sub_company,
            $request->from,
            $request->to,
            $request->main_cats,
            $request->sub_cats,
            $request->price_range,
            $request->service_type,
            '',
            $request->order_type,
            null,
            $request->order_status,
            $request->items_status
        );
        $orders = $orders['orders'];

        return view('company.orders.year_dashboard', compact('providers','orders','search', 'type','company','cats'));
    }
}

//$parent = '89';
//
//$provider = Collaboration::where('company_id', company()->company_id)->pluck('provider_id');
//$cat_ids = Technician::whereIn('provider_id', $provider)->where('active', 1)->where('online', 1)->where('busy', 0)->pluck('cat_ids');
//$cat_array = explode(',', $cat_ids);
//dd($cat_array);
//$key = array_search($parent, $cat_array);
//dd((int)$cat_array[$key]);
//$tech = Technician::where('cat_ids',(int)$cat_array[$key])->whereIn('provider_id', $provider)->where('active', 1)->where('online', 1)->where('busy', 0)->select('id','en_name')->get();
//dd($tech);
