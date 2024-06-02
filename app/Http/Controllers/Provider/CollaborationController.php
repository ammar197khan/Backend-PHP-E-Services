<?php

namespace App\Http\Controllers\Provider;

use App\Models\Category;
use App\Models\Collaboration;
use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Order;
use App\Models\OrderRate;
use App\Models\OrderTechDetail;
use App\Models\OrderTechRequest;
use App\Models\OrderTracking;
use App\Models\Provider;
use App\Models\ProviderSubscription;
use App\Models\SubCompany;
use App\Models\Technician;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class CollaborationController extends Controller
{
    public function index(Request $request)
    {
        $collaboration = Collaboration::where('provider_id', provider()->provider_id)->get();

        $sorter = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction = $request->sort ? explode('.', $request->sort)[1] : 'asc';
        $companies =
            DB::table('companies')->select([
                'companies.id',
                'companies.en_name',
                'companies.ar_name',
                'companies.email',
                'companies.phones',
                'companies.logo',
                'companies.active',
                DB::raw("COALESCE(COUNT(DISTINCT orders.id), 0) AS orders_count"),
                DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
                DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
                DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
                DB::raw("COALESCE(COUNT(DISTINCT order_rates.id), 0) AS rate_count"),
                DB::raw("COALESCE(AVG(order_rates.average), 0) AS rate_average"),
                'companies.created_at',
                'companies.updated_at',
            ])
                ->leftJoin('collaborations', 'companies.id', '=', 'collaborations.company_id')
                ->leftJoin('orders', 'collaborations.company_id', '=', 'orders.provider_id')
                ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
                ->where('collaborations.provider_id',provider()->provider_id)
                ->whereNull('companies.deleted_at')
                ->orderBy($sorter, $direction)
                ->groupBy('companies.id')
                ->paginate(50);

        return view('provider.collaborations.index', compact('companies'));
    }


    public function statistics($id, Request $request)
    {
        $collaboration = Collaboration::where('company_id',$id)->first();

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
            ->where('company_id', $collaboration->company_id)->where('provider_id', provider()->provider_id)->get();
        $data['monthly_orders_count'] = $monthly_orders->count();
        $data['monthly_open'] = $monthly_orders->where('completed', 0)->where('canceled',0)->count();
        $data['monthly_closed'] = $monthly_orders->where('completed', 1)->where('canceled',0)->count();
        $data['monthly_canceled'] = $monthly_orders->where('canceled', 1)->count();

        $yearly_orders = Order::whereYear('created_at', date('Y'))
            ->where('company_id', $collaboration->company_id)->where('provider_id', provider()->provider_id)->get();
        $data['yearly_orders_count'] = $yearly_orders->count();
        $data['yearly_open'] = $yearly_orders->where('completed', 0)->where('canceled', 0)->count();
        $data['yearly_closed'] = $yearly_orders->where('completed', 1)->count();
        $data['yearly_canceled'] = $yearly_orders->where('canceled', 1)->count();

        $monthly_rate = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereMonth('order_rates.created_at', date('m'))->whereYear('order_rates.created_at', date('Y'))
            ->where('company_id', $collaboration->company_id)->where('provider_id', provider()->provider_id);
        $data['monthly_rate_commitment']  = $monthly_rate->avg('commitment');
        $data['monthly_rate_performance'] = $monthly_rate->avg('performance');
        $data['monthly_rate_appearance']  = $monthly_rate->avg('appearance');
        $data['monthly_rate_cleanliness'] = $monthly_rate->avg('cleanliness');
        $data['monthly_rate_count'] = $monthly_rate->groupby('order_id')->count();

        $yearly_rate = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereYear('order_rates.created_at', date('Y'))
            ->where('company_id', $collaboration->company_id)->where('provider_id', provider()->provider_id);
        $data['yearly_rate_commitment']  = $yearly_rate->avg('commitment');
        $data['yearly_rate_performance'] = $yearly_rate->avg('performance');
        $data['yearly_rate_cleanliness'] = $yearly_rate->avg('cleanliness');
        $data['yearly_rate_appearance']  = $yearly_rate->avg('appearance');
        $data['yearly_rate_count']  = $yearly_rate->groupby('order_id')->count();

        $data['top_services'] = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')
            ->where('company_id',$collaboration->company_id)->where('provider_id', provider()->provider_id)->groupby('service.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');
        $data['top_users'] = Order::select('users.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.company_id',$collaboration->company_id)->where('orders.provider_id', provider()->provider_id)->groupby('users.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');
        $data['top_sub_companies'] = Order::select('sub_companies.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('users', 'orders.user_id', '=', 'users.id')->join('sub_companies', 'users.sub_company_id', '=', 'sub_companies.id')
            ->where('orders.company_id',$collaboration->company_id)->where('orders.provider_id', provider()->provider_id)->groupby('sub_companies.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');

        $data['least_services']  = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')
            ->where('company_id', $collaboration->company_id)->where('provider_id', provider()->provider_id)->groupBy('service.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');
        $data['least_users'] = Order::select('users.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.company_id',$collaboration->company_id)->where('orders.provider_id', provider()->provider_id)->groupby('users.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum','name');
        $data['least_sub_companies'] = Order::select('sub_companies.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('sub_companies', 'orders.company_id', '=', 'sub_companies.parent_id')
            ->where('orders.company_id',$collaboration->company_id)->where('orders.provider_id', provider()->provider_id)->groupby('sub_companies.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum','name');

            // MONTHLY TOTAL TANSACTIONS CASH CHART
            $monthly_revenue = Order::select(
                    DB::raw("DATE_FORMAT(created_at, '%Y%m') AS t"),
                    DB::raw("DATE_FORMAT(created_at, '%b %Y') AS m"),
                    DB::raw('SUM(total_amount) as sum')
                )
                ->where('provider_id', provider()->provider_id)
                ->where('company_id', $id)
                ->orderBy('t')
                ->groupBy('m', 't')
                ->pluck('sum', 'm');
            $data['monthly_revenue'] =  array_divide($monthly_revenue->toArray());

            if(count($monthly_revenue)){
                $firstMonthTransaction = Carbon::parse($data['monthly_revenue'][0][0]);
                $lastMonthTransaction  = Carbon::parse(end($data['monthly_revenue'][0]));
                $firstMonthChart       = Carbon::parse(end($data['monthly_revenue'][0]))->subYear();
                $differenceInMonth     = $firstMonthTransaction->diffInMonths($lastMonthTransaction->subYear());
                for ($i=0; $i < $differenceInMonth-1; $i++) {
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
                ->where('provider_id', provider()->provider_id)
                ->where('company_id', $id)
                ->orderBy('t')
                ->groupBy('m', 't')
                ->pluck('sum', 'm');
            $data['monthly_revenue_count'] =  array_divide($monthly_revenue_count->toArray());

            if(count($monthly_revenue_count)){
                $firstMonthTransaction = Carbon::parse($data['monthly_revenue_count'][0][0]);
                $lastMonthTransaction  = Carbon::parse(end($data['monthly_revenue_count'][0]));
                $firstMonthChart       = Carbon::parse(end($data['monthly_revenue_count'][0]))->subYear();
                $differenceInMonth     = $firstMonthTransaction->diffInMonths($lastMonthTransaction->subYear());
                for ($i=0; $i < $differenceInMonth-1; $i++) {
                  array_unshift($data['monthly_revenue_count'][0], $firstMonthTransaction->subMonth()->format('M Y'));
                  array_unshift($data['monthly_revenue_count'][1], 0);
                }
            }


            // DAILY TOTAL TANSACTIONS CASH GRAPH
            // $daily_revenue = Order::select( DB::raw('DAY(created_at) as d'), DB::raw('SUM(total_amount) as sum') )/*->whereMonth('created_at', Date('m'))*/->whereYear('created_at', Date('Y'))->where('provider_id', $provider_id)->groupBy('d')->pluck('sum', 'd');
            $daily_revenue = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('SUM(total_amount) as sum') )/*->whereMonth('created_at', Date('m'))*/
            ->whereYear('created_at', Date('Y'))
            ->where('provider_id', provider()->provider_id)
            ->where('company_id', $id)->groupBy('d')->pluck('sum', 'd');
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

            // DAILY TOTAL TANSACTIONS CASH GRAPH
            // $daily_revenue = Order::select( DB::raw('DAY(created_at) as d'), DB::raw('SUM(total_amount) as sum') )/*->whereMonth('created_at', Date('m'))*/->whereYear('created_at', Date('Y'))->where('provider_id', $provider_id)->groupBy('d')->pluck('sum', 'd');
            $daily_revenue_count = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('COUNT(id) as sum') )/*->whereMonth('created_at', Date('m'))*/
            ->whereYear('created_at', Date('Y'))
            ->where('provider_id', provider()->provider_id)
            ->where('company_id', $id)
            ->groupBy('d')->pluck('sum', 'd');
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

        return view('provider.dashboard', compact('data'));
    }

    public function date_year_orders($company_id, $type)
    {
        $id = $company_id;
        $company = Company::where('id', $company_id)->select('id', 'en_name')->first();

        $subs = CompanySubscription::where('company_id', $company_id)->first()->subs;
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
            ->where('provider_id', provider()->provider_id)->where('company_id', $company_id);

        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('provider_id', provider()->provider_id)
            ->where('company_id', $company_id);

        $get_orders = new Order;

        $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);

        $orders = $show_orders->latest()->paginate(50);

//        return view('provider.collaborations.statistics_orders_dashboard',
//            compact('orders', 'id', 'type','cats','company'));
        return view('provider.collaborations.statistics_orders_dashboard',
            compact('orders', 'id', 'type','cats','company'));
    }

    public function search($id, $type,Request $request)
    {
        $company_id = $id;

        $company = Company::where('id', $company_id)->select('id', 'en_name')->first();

        $subs = CompanySubscription::where('company_id', $company_id)->first()->subs;
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $yearly_orders = Order::whereYear('created_at', date('Y'))
            ->where('provider_id', provider()->provider_id)->where('company_id', $company->id);
        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
            ->where('provider_id', provider()->provider_id)->where('company_id', $company->id);

        $search = Input::get('search');

        $get_orders = new Order;

        $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);

        $orders = $get_orders->search($show_orders,$search,$company_id,provider()->provider_id,$request->company_id,
            $request->sub_company,$request->from,$request->to,$request->main_cats,$request->sub_cats,$request->price_range,
            $request->service_type);
        $orders = $orders['orders'];

        return view('provider.collaborations.statistics_orders_dashboard',
            compact('orders','search', 'id', 'type','company','cats'));
    }

    public function date_items($id,$type)
    {
        $company_id = Collaboration::where('id',$id)->select('company_id')->first()->company_id;

        $this_year = new Carbon('first day of january this year');
        $this_month = new Carbon('first day of this month');

        $yearly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)
            ->where('company_id', $company_id)->where('created_at','>=', $this_year->toDateTimeString());
        $monthly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)
            ->where('company_id', $company_id)->where('created_at','>=', $this_month->toDateTimeString());

        if($type == 'monthly_parts_count')
        {
            $orders = $monthly_orders->where('type','re_scheduled')->latest()->paginate(50);
        }
        elseif($type == 'yearly_parts_count')
        {
            $orders = $yearly_orders->where('type','re_scheduled')->latest()->paginate(50);
        }

        return view('provider.orders.show_items_dashboard',compact('orders','type'));
    }

    public function date_price($id,$type)
    {
        $company_ids = Collaboration::where('id',$id)->select('company_id')->first()->company_id;

        $this_month = new Carbon('first day of this month');
        $this_year = new Carbon('first day of january this year');

        $monthly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->where('company_id', $company_ids)->where('created_at','>=', $this_month->toDateTimeString());
        $yearly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->where('company_id', $company_ids)->where('created_at','>=', $this_year->toDateTimeString());

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

            return view('provider.orders.price_statistics',compact('orders','company_ids','total_sum'));

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

            return view('provider.orders.item_statistics',compact('orders','company_ids','total_sum'));}
    }

    public function date_rate($id,$type)
    {
        $company_ids = Collaboration::where('id',$id)->select('company_id')->first()->company_id;

        //month
        $this_month = new Carbon('first day of this month');
        $monthly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->where('company_id', $company_ids)->where('created_at','>=', $this_month->toDateTimeString())->get();
        $monthly_orders_ids = $monthly_orders->pluck('id');
        $monthly_rates_ids = OrderRate::whereIn('order_id', $monthly_orders_ids)->pluck('order_id');

        //year
        $this_year = new Carbon('first day of january this year');
        $yearly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->
        where('company_id', $company_ids)->where('created_at','>=', $this_year->toDateTimeString())->get();
        $yearly_orders_ids = $yearly_orders->pluck('id');
        $yearly_rates_ids = OrderRate::whereIn('order_id', $yearly_orders_ids)->pluck('order_id');

        if($type == 'monthly_rate'){
            $orders =  Order::whereIn('id', $monthly_rates_ids)->get();
        }elseif($type == 'yearly_rate'){
            $orders = Order::whereIn('id', $yearly_rates_ids)->get();
        }
        return view('provider.orders.rate_dashboard', compact('orders', 'type'));
    }

    public function show($company_id,$id,Request $request)
    {
        $request->merge(['order_id' => $id]);
        $this->validate($request,
            [
                'order_id' => 'required|exists:orders,id,company_id,'.$company_id
            ]
        );

        $order = Order::find($id);

        $order_tracks = OrderTracking::where('order_id',$id)->pluck('date', 'status');

//        return view('provider.collaborations.show', compact('order', 'collaboration_id', 'count'));
        return view('provider.orders.show', compact('order','order_tracks'));
    }

    public function bills($id)
    {
//        $collaboration = Collaboration::find($id);
//        $orders = Order::where('company_id', $collaboration->company_id)->where('completed', 1)->paginate(50);
//
//        return view('provider.bills.index', compact('orders', 'id', 'collaboration'));

        $collaboration = Collaboration::find($id);
        $company = Company::where('id', $collaboration->company_id)->select('id', 'en_name')->first();

        $subs = CompanySubscription::where('company_id', $collaboration->company_id)->first();
        if(isset($subs))
        {
            $get_subs = $subs->subs;
            $cat_ids = Category::whereIn('id', unserialize($get_subs))->pluck('parent_id');
            $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

            $orders = Order::where('provider_id',provider()->provider_id)->where('company_id', $collaboration->company_id)->where('completed', 1)->where('canceled',0)->latest()->paginate(50);
            return view('provider.bills.index', compact('orders', 'id', 'company', 'cats','collaboration','companies'));
        }else{

            $orders = Order::where('provider_id',provider()->provider_id)->where('company_id', $collaboration->company_id)->where('completed', 1)->where('canceled',0)->latest()->paginate(50);

            return view('provider.bills.index', compact('orders', 'id', 'collaboration','company'));
        }
    }

    public function view_bills($id,Request $request)
    {
        $company_id = Collaboration::whereId($id)->select('company_id')->first()->company_id;
        $company_name = Company::whereId($company_id)->select('en_name')->first()->en_name;
        $provider_name = Provider::whereId(provider()->provider_id)->select('en_name')->first()->en_name;

        $get_orders = json_decode($request->order_data);

        $orders = [];
        $total_orders = [];
        $total_items = [];
        foreach ($get_orders as $order)
        {
            $order = Order::whereId($order->id)->first();
            array_push($orders,$order);
            array_push($total_orders,$order->order_total);
            array_push($total_items,$order->item_total);
        }
        $total_sum = array_sum($total_orders) + array_sum($total_items);

        return view('provider.bills.show',compact('orders','company_id','total_sum','company_name','provider_name'));
    }

    public function bills_search($id,Request $request)
    {
        $collaboration = Collaboration::find($id);
        $company = Company::where('id', $collaboration->company_id)->select('id', 'en_name')->first();
        $companies = Company::where('id', $collaboration->company_id)->select('id', 'en_name')->get();
//        $get_order = Order::where('company_id', $collaboration->company_id)->where('completed', 1);

        $subs = CompanySubscription::where('company_id', $collaboration->company_id)->first()->subs;
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $search = Input::get('search');

        $get_orders = new Order;
        $show_orders = Order::where('company_id', $collaboration->company_id)->where('completed', 1)->where('canceled',0);
        $orders = $get_orders->search($show_orders,$search,$collaboration->company_id,$collaboration->provider_id,$request->company_id,
            $request->sub_company,$request->from,$request->to,$request->main_cats,$request->sub_cats,$request->price_range,
            $request->service_type);

        $bills_export = $orders['bills_export'];
        $orders = $orders['orders'];

        return view('provider.bills.index', compact('orders', 'id', 'search', 'collaboration', 'cats',
            'companies','cats','company','bills_export'));
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

    public function bills_export($id)
    {
        $collaboration = Collaboration::find($id);

        $orders = Order::where('company_id', $collaboration->company_id)->where('completed', 1)->get();

        foreach($orders as $order)
        {
            $order['Id'] = $order->id;
            $order['Smo'] = $order->smo;
            $order['Type'] = $order->type;
            $order['User'] = $order->user->en_name;
            $order['Technician'] = $order->tech->en_name;
            $order['status'] = 'Completed';
            $order['Service Fee'] = $order->get_cat_fee($order->id);
            $order['Items Total'] = $order->item_total;
            $order['Total Amount'] = $order->order_total;
            $order['Date'] = $order->created_at;

            unset($order->tech,$order->user,$order->id,$order->smo,$order->type,$order->company_id,$order->provider_id,
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

    public function bills_export_search($id,$search)
    {
        $collaboration = Collaboration::find($id);
        $order = Order::where('company_id', $collaboration->company_id)->where('completed', 1);
        $orders = new Collection();

        if($search != '')
        {

            $user = User::where('company_id', $collaboration->company_id)->where(function($q) use($search)
            {
                $q->where('en_name','like','%'.$search.'%');
                $q->orWhere('ar_name','like','%'.$search.'%');
                $q->orWhere('email','like','%'.$search.'%');
                $q->orWhere('phone','like','%'.$search.'%');
                $q->orWhere('badge_id', $search);
            }
            )->first();

            $tech = Technician::where('provider_id', $collaboration->provider_id)->where(function($q) use($search)
            {
                $q->where('en_name','like','%'.$search.'%');
                $q->orWhere('ar_name','like','%'.$search.'%');
                $q->orWhere('email','like','%'.$search.'%');
                $q->orWhere('phone','like','%'.$search.'%');
                $q->orWhere('badge_id', $search);
            }
            )->first();

            if($search == isset(Order::where('company_id', $collaboration->company_id)->where('id', $search)->first()->id))
            {
                $by_order_id = $order->where('id', $search)->get();
                $orders = $orders->merge($by_order_id);
            }
            if($search == isset(Order::where('smo', $search)->first()->smo))
            {
                $by_smo = $order->where('smo', $search)->get();
                $orders = $orders->merge($by_smo);
            }
            if($user)
            {
                $by_user =$order->where('user_id', $user->id)->get();
                $orders = $orders->merge($by_user);
            }
            if($tech)
            {
                $by_tech = $order->where('tech_id', $tech->id)->get();
                $orders = $orders->merge($by_tech);
            }

        }else{
            $all = $order->get();
            $orders = $orders->merge($all);
        }

        foreach($orders as $order)
        {
            $order['Id'] = $order->id;
            $order['Smo'] = $order->smo;
            $order['Type'] = $order->type;
            $order['User'] = $order->user->en_name;
            $order['Technician'] = $order->tech->en_name;
            $order['status'] = 'Completed';
            $order['Service Fee'] = $order->get_cat_fee($order->id);
            $order['Items Total'] = $order->item_total;
            $order['Total Amount'] = $order->order_total;
            $order['Date'] = $order->created_at;

            unset($order->tech,$order->user,$order->id,$order->smo,$order->type,$order->company_id,$order->provider_id,
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

    }

//    public function print_bills()
//    {
//        return view('provider.bill')
//    }
}
