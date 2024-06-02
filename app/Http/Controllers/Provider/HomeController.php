<?php

namespace App\Http\Controllers\Provider;

use App\Models\Address;
use App\Models\Category;
use App\Models\Collaboration;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderRate;
use App\Models\OrderTechRequest;
use App\Models\Provider;
use App\Models\ProviderAdmin;
use App\Models\ProviderSubscription;
use App\Models\SubCompany;
use App\Models\Technician;
use App\Models\Warehouse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Response;
use Auth;

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
        $company_id_Request = $request->company_id;
        $sub_company_Request = $request->sub_company;
        if($sub_company_Request){
            $sub_company_Request = User::whereIn('sub_company_id',  $sub_company_Request)->pluck('id');
        }

        $collaboration = Collaboration::whereProviderId(provider()->provider_id)->pluck('company_id');
        $companies = Company::whereIn('id', $collaboration)->select('id', 'en_name')->get();
        $companiesIds = Company::whereIn('id', $collaboration)->pluck('id')->toArray();
        $sub_companies = SubCompany::whereIn('parent_id', $companiesIds)->where('status', 'active')->select('id', 'en_name')->get();

        $provider_id = Auth::guard('provider')->user()->provider_id;

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('provider_id', $provider_id)->get();
        $data['monthly_orders_count'] = $monthly_orders->count();
        $data['monthly_open'] = $monthly_orders->where('completed', 0)->where('canceled',0)->count();
        $data['monthly_closed'] = $monthly_orders->where('completed', 1)->where('canceled',0)->count();
        $data['monthly_canceled'] = $monthly_orders->where('canceled', 1)->count();

        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('provider_id', $provider_id)->get();
        $data['yearly_orders_count'] = $yearly_orders->count();
        $data['yearly_open'] = $yearly_orders->where('completed', 0)->where('canceled', 0)->count();
        $data['yearly_closed'] = $yearly_orders->where('completed', 1)->count();
        $data['yearly_canceled'] = $yearly_orders->where('canceled', 1)->count();

        $data['monthly_rate_commitment']  = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereMonth('order_rates.created_at', date('m'))->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->avg('appearance');
        $data['monthly_rate_performance'] = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereMonth('order_rates.created_at', date('m'))->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->avg('performance');
        $data['monthly_rate_appearance']  = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereMonth('order_rates.created_at', date('m'))->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->avg('commitment');
        $data['monthly_rate_cleanliness'] = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereMonth('order_rates.created_at', date('m'))->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->avg('cleanliness');
        $data['monthly_rate_count'] = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereMonth('order_rates.created_at', date('m'))->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->groupby('order_id')->count();

        $data['yearly_rate_commitment']  = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->avg('appearance');
        $data['yearly_rate_cleanliness'] = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->avg('cleanliness');
        $data['yearly_rate_performance'] = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->avg('performance');
        $data['yearly_rate_appearance']  = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->avg('commitment');
        $data['yearly_rate_count']  = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereYear('order_rates.created_at', date('Y'))->where('provider_id', $provider_id)->groupby('order_id')->count();


        $data['top_services'] = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum'));

        if(!empty($formRequest) && !empty($toRequest)){
            $data['top_services'] = $data['top_services']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }

        if(!empty($company_id_Request)){
            $data['top_services'] = $data['top_services']->whereIn('orders.company_id', $company_id_Request);
        }
        if(!empty($sub_company_Request)){
            $data['top_services'] = $data['top_services']->whereIn('orders.user_id', $sub_company_Request);
        }

        $data['top_services'] = $data['top_services']->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')->where('provider_id', $provider_id)->groupby('service.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');

        $data['top_companies'] = Order::select('companies.en_name as name', DB::raw('SUM(total_amount) as sum') );
        if(!empty($formRequest) && !empty($toRequest)){
            $data['top_companies'] = $data['top_companies']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }

        if(!empty($company_id_Request)){
            $data['top_companies'] = $data['top_companies']->whereIn('orders.company_id', $company_id_Request);
        }
        if(!empty($sub_company_Request)){
            $data['top_companies'] = $data['top_companies']->whereIn('orders.user_id', $sub_company_Request);
        }

        $data['top_companies'] = $data['top_companies']->join('companies', 'orders.company_id', '=', 'companies.id')->where('provider_id', $provider_id)->groupBy('companies.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum', 'name');



        $data['top_techs']     = Order::select('technicians.en_name as name', DB::raw('SUM(total_amount) as sum') );

        if(!empty($formRequest) && !empty($toRequest)){
            $data['top_techs'] = $data['top_techs']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }

        if(!empty($company_id_Request)){
            $data['top_techs'] = $data['top_techs']->whereIn('orders.company_id', $company_id_Request);
        }
        if(!empty($sub_company_Request)){
            $data['top_techs'] = $data['top_techs']->whereIn('orders.user_id', $sub_company_Request);
        }
        $data['top_techs'] = $data['top_techs']->join('technicians', 'orders.tech_id', '=', 'technicians.id')->where('orders.provider_id', $provider_id)->groupBy('technicians.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum', 'name');


        $data['top_items']     = [

        ];

        $data['least_services']  = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum') );
        if(!empty($formRequest) && !empty($toRequest)){
            $data['least_services'] = $data['least_services']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }

        if(!empty($company_id_Request)){
            $data['least_services'] = $data['least_services']->whereIn('orders.company_id', $company_id_Request);
        }
        if(!empty($sub_company_Request)){
            $data['least_services'] = $data['least_services']->whereIn('orders.user_id', $sub_company_Request);
        }
        $data['least_services'] = $data['least_services']->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')->where('provider_id', $provider_id)->groupBy('service.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');


        $data['least_companies'] = Order::select('companies.en_name as name', DB::raw('SUM(total_amount) as sum') );
        if(!empty($formRequest) && !empty($toRequest)){
            $data['least_companies'] = $data['least_companies']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }

        if(!empty($company_id_Request)){
            $data['least_companies'] = $data['least_companies']->whereIn('orders.company_id', $company_id_Request);
        }
        if(!empty($sub_company_Request)){
            $data['least_companies'] = $data['least_companies']->whereIn('orders.user_id', $sub_company_Request);
        }
        $data['least_companies'] = $data['least_companies']->join('companies', 'orders.company_id', '=', 'companies.id')->where('provider_id', $provider_id)->groupBy('companies.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');


        $data['least_techs'] = Order::select('technicians.en_name as name', DB::raw('SUM(total_amount) as sum') );
        if(!empty($formRequest) && !empty($toRequest)){
            $data['least_techs'] = $data['least_techs']->whereDate('orders.created_at','<=',$toRequest)
            ->whereDate('orders.created_at','>=',$formRequest);
          }

        if(!empty($company_id_Request)){
            $data['least_techs'] = $data['least_techs']->whereIn('orders.company_id', $company_id_Request);
        }
        if(!empty($sub_company_Request)){
            $data['least_techs'] = $data['least_techs']->whereIn('orders.user_id', $sub_company_Request);
        }
        $data['least_techs'] = $data['least_techs']->join('technicians', 'orders.tech_id', '=', 'technicians.id')->where('orders.provider_id', $provider_id)->groupBy('technicians.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');
        $data['least_items'] = [

        ];


        // MONTHLY TOTAL TANSACTIONS CASH CHART
        $monthly_revenue = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y%m') AS t"),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') AS m"),
                DB::raw('SUM(total_amount) as sum')
            )
            ->where('provider_id', provider()->provider_id);
            if(!empty($formRequest) && !empty($toRequest)){
                $monthly_revenue = $monthly_revenue->whereDate('created_at','<=',$toRequest)
                ->whereDate('created_at','>=',$formRequest);
              }
              if(!empty($company_id_Request)){
                $monthly_revenue = $monthly_revenue->whereIn('company_id', $company_id_Request);
             }
             if(!empty($sub_company_Request)){
                $monthly_revenue = $monthly_revenue->whereIn('user_id', $sub_company_Request);
            }
            $monthly_revenue = $monthly_revenue->orderBy('t')
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
            ->where('provider_id', provider()->provider_id);
            if(!empty($formRequest) && !empty($toRequest)){
                $monthly_revenue_count = $monthly_revenue_count->whereDate('created_at','<=',$toRequest)
                ->whereDate('created_at','>=',$formRequest);
              }
            if(!empty($company_id_Request)){
                $monthly_revenue_count = $monthly_revenue_count->whereIn('company_id', $company_id_Request);
             }
            if(!empty($sub_company_Request)){
                $monthly_revenue_count = $monthly_revenue_count->whereIn('user_id', $sub_company_Request);
            }
            $monthly_revenue_count =   $monthly_revenue_count->orderBy('t')
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
        // $daily_revenue = Order::select( DB::raw('DAY(created_at) as d'), DB::raw('SUM(total_amount) as sum') )/*->whereMonth('created_at', Date('m'))*/->whereYear('created_at', Date('Y'))->where('provider_id', $provider_id)->groupBy('d')->pluck('sum', 'd');
        $daily_revenue = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('SUM(total_amount) as sum') );
        /*->whereMonth('created_at', Date('m'))*/
        if(!empty($formRequest) && !empty($toRequest)){
            $daily_revenue =  $daily_revenue->whereDate('created_at','<=',$toRequest)
            ->whereDate('created_at','>=',$formRequest);
          }
        if(!empty($company_id_Request)){
            $daily_revenue =  $daily_revenue->whereIn('company_id', $company_id_Request);
         }
         if(!empty($sub_company_Request)){
            $daily_revenue =  $daily_revenue->whereIn('user_id', $sub_company_Request);
        }
        $daily_revenue = $daily_revenue->whereYear('created_at', Date('Y'))->where('provider_id', $provider_id)->groupBy('d')->pluck('sum', 'd');
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
        $daily_revenue_count = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('COUNT(id) as sum') );
        /*->whereMonth('created_at', Date('m'))*/
        if(!empty($formRequest) && !empty($toRequest)){
            $daily_revenue_count =  $daily_revenue_count->whereDate('created_at','<=',$toRequest)
            ->whereDate('created_at','>=',$formRequest);
          }
        if(!empty($company_id_Request)){
            $daily_revenue_count =  $daily_revenue_count->whereIn('company_id', $company_id_Request);
         }
         if(!empty($sub_company_Request)){
            $daily_revenue_count =  $daily_revenue_count->whereIn('user_id', $sub_company_Request);
        }

        $daily_revenue_count =  $daily_revenue_count->whereYear('created_at', Date('Y'))->where('provider_id', $provider_id)->groupBy('d')->pluck('sum', 'd');
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
        foreach($yearly_parts_data as $part)
        {
            $price = DB::table($part->provider_id.'_warehouse_parts')->where('id', $part->item_id)->select('price')->first();
            $pricetoAr = isset($price->price) ? $price->price : 0;
            array_push($yearly_arr, $pricetoAr);
        }
        $data['yearly_parts_prices'] = array_sum($yearly_arr);

        $data['yearly_revenue_widget'] = $yearly_orders->sum('order_total');

        return view('provider.dashboard', compact('data', 'companies', 'sub_companies'));
    }


    public function profile()
    {
        $admin = provider();
        return view('provider.profile.admin', compact('admin'));
    }


    public function update_profile(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required',
                'email' => 'required|unique:provider_admins,email,'.provider()->id,
                'phone' => 'required|unique:provider_admins,phone,'.provider()->id,
            ]
        );

        $admin = provider();
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

        $admin = provider();
            $admin->password = Hash::make($request->password);
        $admin->save();

        return back()->with('success', 'Password changed successfully !');
    }


    public function info()
    {
        $addresses = Address::where('parent_id', NULL)->get();
        $provider = Provider::find(provider()->provider_id);

        return view('provider.profile.show', compact('provider','addresses'));
    }


    public function update_info(Request $request)
    {
        $this->validate($request,
            [
                'address_id' => 'sometimes|exists:addresses,id',
                'ar_name' => 'required|unique:providers,ar_name,'.provider()->provider_id,
                'en_name' => 'required|unique:providers,en_name,'.provider()->provider_id,
                'ar_desc' => 'required',
                'en_desc' => 'required',
                'email' => 'required|email|unique:providers,email,'.provider()->provider_id,
                'phones' => 'required|array',
                'logo' => 'sometimes|image',
            ]
        );

        $provider = Provider::find(provider()->provider_id);
            if($request->address_id) $provider->address_id = $request->address_id;
            $provider->en_name = $request->en_name;
            $provider->ar_name = $request->ar_name;
            $provider->en_desc = $request->en_desc;
            $provider->ar_desc = $request->ar_desc;
            $provider->po_box = $request->po_box;
            $provider->ar_organization_name = $request->ar_organization_name;
            $provider->en_organization_name = $request->en_organization_name;
            $provider->vat = $request->vat;
            $provider->vat_registration = $request->vat_registration;
            $provider->phones = serialize(array_filter($request->phones));
            if($request->logo)
            {
                    unlink(base_path().'/public/providers/logos/'.$provider->logo);

                $name = unique_file($request->logo->getClientOriginalName());
                $request->logo->move(base_path().'/public/providers/logos/',$name);
                $provider->logo = $name;
            }
            if($request->cr_upload)
            {
                if($provider->cr_upload){
                    if(file_exists(base_path().'/public/providers/logos/'.$provider->cr_upload)){
                        unlink(base_path().'/public/providers/logos/'.$provider->cr_upload);
                    }
                }

                $name = unique_file($request->cr_upload->getClientOriginalName());
                $request->cr_upload->move(base_path().'/public/providers/logos/',$name);
                $provider->cr_upload = $name;

            }
            if($request->vat_certificate_upload)
            {
                if($provider->vat_upload){
                if(file_exists(base_path().'/public/providers/logos/'.$provider->vat_upload)){
                    unlink(base_path().'/public/providers/logos/'.$provider->vat_upload);
                }
            }
                $name = unique_file($request->vat_certificate_upload->getClientOriginalName());
                $request->vat_certificate_upload->move(base_path().'/public/providers/logos/',$name);
                $provider->vat_upload = $name;

            }
            if($request->agreement_upload)
            {
                if($provider->agreement_upload){
                    if(file_exists(base_path().'/public/providers/logos/'.$provider->agreement_upload)){
                        unlink(base_path().'/public/providers/logos/'.$provider->agreement_upload);
                    }
                }
                $name = unique_file($request->agreement_upload->getClientOriginalName());
                $request->agreement_upload->move(base_path().'/public/providers/logos/',$name);
                $provider->agreement_upload = $name;

            }
        $provider->save();
        // //-----------update admin logo as well ----------------//
        // $provider_admin = ProviderAdmin::find(provider()->provider_id);
        // if($request->logo)
        // {
        //     unlink(base_path().'/public/providers/admins/'.$provider_admin->image);
        //     $name = unique_file($request->logo->getClientOriginalName());
        //     $request->logo->move(base_path().'/public/providers/admins/',$name);
        //     $provider_admin->image = $name;
        // }
        // $provider_admin->save();
        //-----------------------------------------------//

        return back()->with('success', 'Info changed successfully !');
    }


    public function get_cities($parent)
    {
        $cities = Address::where('parent_id', $parent)->get();
        return response()->json($cities);
    }

    public  function tech_get_sub_cats($parent)
    {
        $parent = explode(',',$parent);

        $subscriptions = ProviderSubscription::where('provider_id', provider()->provider_id)->first()->subs;
        $subs = unserialize($subscriptions);

        $sub_cats = Category::whereIn('parent_id', $parent)->whereIn('id', $subs)->select('id','en_name')->get();
        return response()->json($sub_cats);
    }

    public  function get_sub_cats($parent)
    {
        $parent = explode(',',$parent);
        // $subscriptions = ProviderSubscription::where('provider_id', provider()->provider_id)->first()->subs;
        // $subs = unserialize($subscriptions);

        // $sub_cats = Category::where('parent_id', $parent)/*->whereIn('id', $subs)*/->with('parent')->get();
        $sub_cats = Category::whereIn('parent_id', $parent)/*->whereIn('id', $subs)*/->with('parent')->get();
        return response()->json($sub_cats);
    }

    public function date_orders($type)
    {
        $collaboration = Collaboration::whereProviderId(provider()->provider_id)->pluck('company_id');
        $companies = Company::whereIn('id', $collaboration)->select('id', 'en_name')->get();

        $subs = ProviderSubscription::where('provider_id', provider()->provider_id)->first();
        if(isset($subs)){
            $cat_ids = Category::whereIn('id', unserialize($subs->subs))->pluck('parent_id');
            $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();
        }else{
            $cats = Category::where('parent_id', null)->get();
        }

//        $company_ids = Collaboration::where('provider_id',provider()->provider_id)->pluck('company_id');

        $company_ids = Collaboration::where('provider_id',provider()->provider_id)->pluck('company_id');

        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('provider_id', provider()->provider_id);
//            ->whereIn('company_id', $company_ids);
        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('provider_id', provider()->provider_id);
//            ->whereIn('company_id', $company_ids);


        $get_orders = new Order;

        $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);

        $orders = $show_orders->latest()->paginate(50);

        return view('provider.orders.year_dashboard', compact('orders', 'type','companies','cats'));
    }

    public function date_items($type)
    {
        $company_ids = Collaboration::where('provider_id',provider()->provider_id)->pluck('company_id');
        $this_month = new Carbon('first day of this month');
        $this_year = new Carbon('first day of january this year');

        $monthly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->whereIn('company_id', $company_ids)->where('created_at','>=', $this_month->toDateTimeString());
        $yearly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->whereIn('company_id', $company_ids)->where('created_at','>=', $this_year->toDateTimeString());

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

    public function date_price($type)
    {
        $company_ids = Collaboration::where('provider_id',provider()->provider_id)->pluck('company_id');
        $this_month = new Carbon('first day of this month');
        $this_year = new Carbon('first day of january this year');

        $monthly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->whereIn('company_id', $company_ids)->where('created_at','>=', $this_month->toDateTimeString());
        $yearly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->whereIn('company_id', $company_ids)->where('created_at','>=', $this_year->toDateTimeString());

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

    public function data_rate_orders($type)
    {
        $company_ids = Collaboration::where('provider_id',provider()->provider_id)->pluck('company_id');

        //month
        $this_month = new Carbon('first day of this month');
        $monthly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->whereIn('company_id', $company_ids)->where('created_at','>=', $this_month->toDateTimeString())->get();
        $monthly_orders_ids = $monthly_orders->pluck('id');
        $monthly_rates_ids = OrderRate::whereIn('order_id', $monthly_orders_ids)->pluck('order_id');

        //year
        $this_year = new Carbon('first day of january this year');
        $yearly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)->
        whereIn('company_id', $company_ids)->where('created_at','>=', $this_year->toDateTimeString())->get();
        $yearly_orders_ids = $yearly_orders->pluck('id');
        $yearly_rates_ids = OrderRate::whereIn('order_id', $yearly_orders_ids)->pluck('order_id');

        if($type == 'monthly_rate'){
            $orders =  Order::whereIn('id', $monthly_rates_ids)->get();
        }elseif($type == 'yearly_rate'){
            $orders = Order::whereIn('id', $yearly_rates_ids)->get();
        }
        return view('provider.orders.rate_dashboard', compact('orders', 'type'));
    }

    public function search($type,Request $request)
    {
        $company_ids = Collaboration::whereProviderId(provider()->provider_id)->pluck('company_id');
        $companies = Company::whereIn('id', $company_ids)->select('id', 'en_name')->get();

        $subs = ProviderSubscription::where('provider_id', provider()->provider_id)->first()->subs;
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();

        $this_year = new Carbon('first day of january this year');
        $this_month = new Carbon('first day of this month');

        $yearly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)
            ->whereIn('company_id', $company_ids)->where('created_at','>=', $this_year->toDateTimeString());
        $monthly_orders = Order::raw('table orders')->where('provider_id', provider()->provider_id)
            ->whereIn('company_id', $company_ids)->where('created_at','>=', $this_month->toDateTimeString());

        $search = Input::get('search');

        $get_orders = new Order;

        $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);

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

        return view('provider.orders.year_dashboard', compact('orders','search', 'type','companies','cats'));
    }
}
