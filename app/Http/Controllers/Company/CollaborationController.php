<?php

namespace App\Http\Controllers\Company;

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
use App\Models\ProviderCategoryFee;
use App\Models\ProviderSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class CollaborationController extends Controller
{
    public function index()
    {
        $providers = Collaboration::where('company_id', company()->company_id)->paginate(50);
        foreach($providers as $provider)
        {
            $provider['orders'] = $provider->orders_count($provider->provider_id,company()->company_id);
        }

        return view('company.collaborations.index', compact('providers'));
    }


    public function statistics($id, Request $request)
    {
        $request->merge(
            [
                'collaboration_id' => $id
            ]
        );

        $this->validate($request,
            [
                'collaboration_id' => 'required|exists:collaborations,id,company_id,'.company()->company_id
            ]
        );

        $collaboration = Collaboration::find($id);

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
            ->where('provider_id', $collaboration->provider_id)->where('company_id',company()->company_id)->get();
        $data['monthly_orders_count'] = $monthly_orders->count();
        $data['monthly_open'] = $monthly_orders->where('completed', 0)->where('canceled',0)->count();
        $data['monthly_closed'] = $monthly_orders->where('completed', 1)->where('canceled',0)->count();
        $data['monthly_canceled'] = $monthly_orders->where('canceled', 1)->count();

        $yearly_orders = Order::whereYear('created_at', date('Y'))
            ->where('provider_id', $collaboration->provider_id)->where('company_id',company()->company_id)->get();
        $data['yearly_orders_count'] = $yearly_orders->count();
        $data['yearly_open'] = $yearly_orders->where('completed', 0)->where('canceled', 0)->count();
        $data['yearly_closed'] = $yearly_orders->where('completed', 1)->count();
        $data['yearly_canceled'] = $yearly_orders->where('canceled', 1)->count();

        $monthly_rate = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereMonth('order_rates.created_at', date('m'))->whereYear('order_rates.created_at', date('Y'))
            ->where('provider_id', $collaboration->provider_id)->where('company_id',company()->company_id);
        $data['monthly_rate_commitment']  = $monthly_rate->avg('appearance');
        $data['monthly_rate_performance'] = $monthly_rate->avg('performance');
        $data['monthly_rate_appearance']  = $monthly_rate->avg('commitment');
        $data['monthly_rate_cleanliness'] = $monthly_rate->avg('cleanliness');
        $data['monthly_rate_count'] = $monthly_rate->groupby('order_id')->count();

        $yearly_rate = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereYear('order_rates.created_at', date('Y'))
            ->where('provider_id', $collaboration->provider_id)->where('company_id',company()->company_id);
        $data['yearly_rate_commitment']  = $yearly_rate->avg('appearance');
        $data['yearly_rate_cleanliness'] = $yearly_rate->avg('cleanliness');
        $data['yearly_rate_performance'] = $yearly_rate->avg('performance');
        $data['yearly_rate_appearance']  = $yearly_rate->avg('commitment');
        $data['yearly_rate_count']  = $yearly_rate->groupby('order_id')->count();


        $data['top_services']  = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')
            ->where('orders.provider_id', $collaboration->provider_id)->where('orders.company_id',company()->company_id)->groupBy('service.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum', 'name');
        $data['top_techs']     = Order::select('technicians.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('technicians', 'orders.tech_id', '=', 'technicians.id')
            ->where('orders.provider_id', $collaboration->provider_id)->where('orders.company_id',company()->company_id)->groupBy('technicians.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum', 'name');
        $data['top_items']     = [

        ];

        $data['least_services']  = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')
            ->where('provider_id', $collaboration->provider_id)->where('orders.company_id',company()->company_id)->groupBy('service.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');
        $data['least_techs'] = Order::select('technicians.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('technicians', 'orders.tech_id', '=', 'technicians.id')
            ->where('orders.provider_id', $collaboration->provider_id)->where('orders.company_id',company()->company_id)->groupBy('technicians.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');
        $data['least_items'] = [

        ];

        // MONTHLY TOTAL TANSACTIONS CASH CHART
        $monthly_revenue = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y%m') AS t"),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') AS m"),
                DB::raw('SUM(total_amount) as sum')
            )
            ->where('company_id', company()->company_id)
            ->where('provider_id', $collaboration->provider_id)
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
            ->where('company_id', company()->company_id)
            ->where('provider_id', $collaboration->provider_id)
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
        $daily_revenue = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('SUM(total_amount) as sum') )
        ->where('company_id', company()->company_id)
        ->where('provider_id', $collaboration->provider_id)
        ->groupBy('d')
        ->pluck('sum', 'd');
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
        $daily_revenue_count = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('COUNT(id) as sum') )
        ->where('company_id', company()->company_id)
        ->where('provider_id', $collaboration->provider_id)
        ->groupBy('d')
        ->pluck('sum', 'd');
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

        // ===


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
        foreach($yearly_parts_data as $part)
        {
            $price = DB::table($part->provider_id.'_warehouse_parts')->where('id', $part->item_id)->select('price')->first()->price;
            array_push($yearly_arr, $price);
        }
        $data['yearly_parts_prices'] = array_sum($yearly_arr);

        $data['yearly_revenue_widget'] = $yearly_orders->sum('order_total');

        return view('company.dashboard', compact('data'));
    }


    public function date_year_orders($id, $type)
    {
        $provider_id = Collaboration::where('id',$id)->select('provider_id')->first()->provider_id;

        $company = Company::where('id', company()->company_id)->select('id', 'en_name')->first();

        $subs = ProviderSubscription::where('provider_id', $provider_id)->first()->subs;
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $collaborations = Collaboration::where('company_id', $company->id)->pluck('provider_id');
        $providers = Provider::whereIn('id', $collaborations)->get();

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('provider_id', $provider_id)
            ->where('company_id', $company->id);
        $yearly_orders = Order::whereYear('created_at',date('Y'))->where('provider_id', $provider_id)
            ->where('company_id', $company->id);

        if($type == 'monthly_orders_count')
        {
            $orders = $monthly_orders;
        }
        elseif($type == 'yearly_orders_count')
        {
            $orders = $yearly_orders;
        }
        elseif($type == 'monthly_open')
        {
            $orders = $monthly_orders->where('completed', 0)->where('canceled', 0);
        }
        elseif($type == 'yearly_open')
        {
            $orders = $yearly_orders->where('completed', 0)->where('canceled', 0);
        }
        elseif($type == 'monthly_closed')
        {
            $orders = $monthly_orders->where('completed', 1)->where('canceled', 0);
        }
        elseif($type == 'yearly_closed')
        {
            $orders = $yearly_orders->where('completed', 1)->where('canceled', 0);
        }
        elseif($type == 'monthly_canceled')
        {
            $orders = $monthly_orders->where('canceled', 1);
        }
        elseif($type == 'yearly_canceled')
        {
            $orders = $yearly_orders->where('canceled', 1);
        }
        elseif($type == 'monthly_parts_orders_count')
        {
            $orders = $monthly_orders->where('type','re_scheduled');
        }
        elseif($type == 'yearly_parts_orders_count')
        {
            $orders = $yearly_orders->where('type','re_scheduled');
        }

        $orders = $orders->latest()->paginate(50);

        return view('company.collaborations.statistics_orders_dashboard',
            compact('orders', 'id', 'type','cats','company','providers'));
    }

    public function search($id, $type,Request $request)
    {
        $provider_id = Collaboration::where('id',$id)->select('provider_id')->first()->provider_id;
        $company = Company::where('id', company()->company_id)->select('id', 'en_name')->first();

        $subs = ProviderSubscription::where('provider_id', $provider_id)->first()->subs;
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('provider_id', $provider_id)
            ->where('company_id',$company->id);
        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))
            ->where('provider_id', $provider_id)->where('company_id',$company->id);

        $search = Input::get('search');

        $get_orders = new Order;

        $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);

        $orders = $get_orders->search($show_orders,$search,$company->id,$provider_id,$request->company_id,
            $request->sub_company,$request->from,$request->to,$request->main_cats,$request->sub_cats,$request->price_range,
            $request->service_type);
        $orders = $orders['orders'];

        return view('company.collaborations.statistics_orders_dashboard',
            compact('orders','search', 'id', 'type','company','cats'));
    }

    public function date_items($id,$type)
    {
        $provider_id = Collaboration::where('id',$id)->select('provider_id')->first()->provider_id;

        $this_year = new Carbon('first day of january this year');
        $this_month = new Carbon('first day of this month');

        $yearly_orders = Order::raw('table orders')->where('provider_id', $provider_id)
            ->where('company_id', company()->company_id)->where('created_at','>=', $this_year->toDateTimeString());
        $monthly_orders = Order::raw('table orders')->where('provider_id', $provider_id)
            ->where('company_id', company()->company_id)->where('created_at','>=', $this_month->toDateTimeString());

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

    public function date_price($id,$type)
    {
        $provider_id = Collaboration::where('id',$id)->select('provider_id')->first()->provider_id;
        $company_ids = Company::where('id',company()->company_id)->select('id')->first()->id;

        $this_month = new Carbon('first day of this month');
        $this_year = new Carbon('first day of january this year');

        $monthly_orders = Order::raw('table orders')->where('provider_id', $provider_id)->where('company_id', company()->company_id)->where('created_at','>=', $this_month->toDateTimeString());
        $yearly_orders = Order::raw('table orders')->where('provider_id', $provider_id)->where('company_id', company()->company_id)->where('created_at','>=', $this_year->toDateTimeString());

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

            return view('company.orders.price_statistics',compact('orders','company_ids','total_sum'));

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
        $provider_id = Collaboration::where('id',$id)->select('provider_id')->first()->provider_id;

        //month
        $this_month = new Carbon('first day of this month');
        $monthly_orders = Order::raw('table orders')->where('provider_id', $provider_id)->where('company_id', company()->company_id)->where('created_at','>=', $this_month->toDateTimeString())->get();
        $monthly_orders_ids = $monthly_orders->pluck('id');
        $monthly_rates_ids = OrderRate::whereIn('order_id', $monthly_orders_ids)->pluck('order_id');

        //year
        $this_year = new Carbon('first day of january this year');
        $yearly_orders = Order::raw('table orders')->where('provider_id', $provider_id)->
        where('company_id', company()->company_id)->where('created_at','>=', $this_year->toDateTimeString())->get();
        $yearly_orders_ids = $yearly_orders->pluck('id');
        $yearly_rates_ids = OrderRate::whereIn('order_id', $yearly_orders_ids)->pluck('order_id');

        if($type == 'monthly_rate'){
            $orders =  Order::whereIn('id', $monthly_rates_ids)->get();
        }elseif($type == 'yearly_rate'){
            $orders = Order::whereIn('id', $yearly_rates_ids)->get();
        }
        return view('provider.orders.rate_dashboard', compact('orders', 'type'));
    }

    public function show($collaboration_id,$id,Request $request)
    {
        $order = Order::findOrFail($id);
        if($order->company_id != company()->company_id){
            abort(404);
        }
        $order_tracks = OrderTracking::where('order_id',$id)->pluck('date', 'status');

        return view('company.orders.show', compact('order', 'order_tracks'));
    }


    public function orders_request($coll_id)
    {
        $provider_id = Collaboration::where('id', $coll_id)->select('provider_id')->first()->provider_id;
        $provider_ids = Collaboration::where('company_id', company()->company_id)->pluck('provider_id');
        $providers = Provider::whereIn('id', $provider_ids)->select('id','en_name')->get();

        return view('company.collaborations.orders_info_request', compact('coll_id','provider_id','providers'));
    }


    public function orders_show(Request $request)
    {
        $this->validate($request,
            [
                'coll_id' => 'required|exists:collaborations,id,company_id,'.company()->company_id,
                'provider_id' => 'required|exists:collaborations,provider_id,company_id,'.company()->company_id,
                'from' => 'required|date',
                'to' => 'required|date'
            ],
            [
                'provider_id.required' => 'Please choose a provider',
                'provider_id.exists' => 'Invalid Provider',
                'from.required' => 'Please choose a date to start from',
                'from.date' => 'Please choose a valid date to start from',
                'to.required' => 'Please choose a date to end with',
                'to.date' => 'Please choose a valid date to end with',
            ]
        );

        $sub_cats = Order::where('company_id', company()->company_id)->where('provider_id', $request->provider_id)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->distinct()->pluck('cat_id');
        $cats = new Collection();

        foreach($sub_cats as $cat)
        {
            $parent_id = Category::where('id', $cat)->select('parent_id')->first()->parent_id;
            $parent = Category::where('id', $parent_id)->select('id','en_name as name')->first();

            $cats = $cats->push($parent);
        }
//        dd($cats->toArray());

            foreach($cats as $cat)
            {
                if(isset($cat)){
                    $subs = Category::where('parent_id', $cat->id)->pluck('id');


                    $all_orders = Order::where('company_id', company()->company_id)->where('provider_id', $request->provider_id)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->whereIn('cat_id', $subs)->select('type','cat_id','order_total')->get();


                    $cat['urgent'] = $all_orders->where('type', 'urgent')->count();
                    $cat['scheduled'] = $all_orders->where('type', 'scheduled')->count();
                    $cat['re_scheduled'] = $all_orders->where('type', 're_scheduled')->count();
                    $cat['quantity'] = $all_orders->count();
                    $cat['rates'] = $all_orders->sum('order_total');

                    unset($cat->id);
                }
        }


        $cats[] = collect(['total' => $cats->sum('rates')]);
        $provider = Provider::where('id', $request->provider_id)->select('en_name as name')->first();
        $from = $request->from;
        $to = $request->to;
        $coll_id = $request->coll_id;

        return view('company.collaborations.orders_info_show', compact('coll_id','cats','provider','from','to'));
    }


    public function orders_export(Request $request)
    {
        $this->validate($request,
            [
                'coll_id' => 'required|exists:collaborations,id,company_id,'.company()->company_id,
                'from' => 'required|date',
                'to' => 'required|date'
            ]
        );

        $provider_id = Collaboration::where('id', $request->coll_id)->first()->provider_id;
        $provider = Provider::where('id', $provider_id)->select('id','en_name')->first();

        $sub_cats = Order::where('company_id', company()->company_id)->where('provider_id', $provider->id)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->distinct()->pluck('cat_id');
        $cats = new Collection();

        foreach($sub_cats as $cat)
        {
            $parent_id = Category::where('id', $cat)->select('parent_id')->first()->parent_id;
            $parent = Category::where('id', $parent_id)->select('id','en_name as Name')->first();;

            $cats = $cats->push($parent);
        }

        foreach($cats as $cat)
        {
            if(isset($cat)) {
                $subs = Category::where('parent_id', $cat->id)->pluck('id');


                $all_orders = Order::where('company_id', company()->company_id)->where('provider_id', $provider->id)->where('created_at', '>=', $request->from)->where('created_at', '<=', $request->to)->whereIn('cat_id', $subs)->select('type', 'cat_id', 'order_total')->get();


                $cat['Urgent'] = $all_orders->where('type', 'urgent')->count();
                $cat['Scheduled'] = $all_orders->where('type', 'scheduled')->count();
                $cat['Re_scheduled'] = $all_orders->where('type', 're_scheduled')->count();
                $cat['Quantity'] = $all_orders->count();
                $cat['Rates'] = $all_orders->sum('order_total');
                $cat['Total'] = '';
                unset($cat->id);
            }
        }


        $cats[] = collect(['Name' => '-','Urgent' => '-','Scheduled' => '-','Re_scheduled' => '-','Quantity' => '-','Rates' => '-','Total' => $cats->sum('Rates')]);

        $cats = $cats->toArray();
        $from = $request->from;
        $to = $request->to;
        $p_name = str_replace(' ','-',$provider->en_name);

        $filename = 'qareeb_'.$p_name.'_'.$from.'_'.$to.'_orders_invoice.xls';

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $heads = false;
        foreach($cats as $cat)
        {
            if(isset($cat)){

                if($heads == false)
                {
                    echo implode("\t", array_keys($cat)) . "\n";
                    $heads = true;
                }
                {
                    echo implode("\t", array_values($cat)) . "\n";
                }
            }
        }

        die();
    }


    public function fees_show($provider_id,Request $request)
    {
        $request->merge(['provider_id' => $provider_id]);

        $this->validate($request,
            [
                'provider_id' => 'required|exists:collaborations,provider_id,company_id,'.company()->company_id
            ]
        );

        $cats = CompanySubscription::where('company_id', company()->company_id)->select('subs')->first()->subs;
        $cats = unserialize($cats);

        $subs = ProviderCategoryFee::where('provider_id', $request->provider_id)->whereIn('cat_id', $cats)->select('cat_id','urgent_fee','scheduled_fee', 'emergency_fee')->get();
        $provider = Provider::where('id', $request->provider_id)->select('id','en_name as name')->first();

        return view('company.collaborations.fees_info_show', compact('subs','provider'));
    }


    public function fees_export($provider_id,Request $request)
    {
        $request->merge(['provider_id' => $provider_id]);

        $this->validate($request,
            [
                'provider_id' => 'required|exists:collaborations,provider_id,company_id,'.company()->company_id
            ]
        );

        $cats = CompanySubscription::where('company_id', company()->company_id)->select('subs')->first()->subs;
        $cats = unserialize($cats);

        $subs = ProviderCategoryFee::where('provider_id', $request->provider_id)->whereIn('cat_id', $cats)->select('cat_id','urgent_fee','scheduled_fee')->get();

        $provider = Provider::where('id', $request->provider_id)->select('en_name as name')->first();

        foreach($subs as $sub)
        {
            $cat = Category::where('id', $sub->cat_id)->select('parent_id','en_name')->first();
            $parent = Category::where('id', $cat->parent_id)->select('en_name')->first();

            $sub['Category'] = $parent->en_name .' - '. $cat->en_name;
            $sub['Urgent Fee'] = $sub->urgent_fee;
            $sub['Scheduled Fee'] = $sub->scheduled_fee;

            unset($sub->cat_id,$sub->urgent_fee,$sub->scheduled_fee);
        }


        $subs = $subs->toArray();
        $p_name = str_replace(' ','-',$provider->name);

        $filename = 'qareeb_'.$p_name.'_categories_fees.xls';


        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $heads = false;
        foreach($subs as $sub)
        {
            if($heads == false)
            {
                echo implode("\t", array_keys($sub)) . "\n";
                $heads = true;
            }
            {
                echo implode("\t", array_values($sub)) . "\n";
            }
        }

        die();
    }
}
