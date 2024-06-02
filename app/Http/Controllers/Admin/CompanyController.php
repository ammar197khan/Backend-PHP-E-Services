<?php

namespace App\Http\Controllers\Admin;

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
use App\Models\User;
use App\Models\Sla;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class CompanyController extends Controller
{
    protected $SlaModel;
    public function __construct()
    {
        $SlaModel       = new Sla();
        $this->SlaModel = $SlaModel;

    }
    public function index(Request $request)
    {
//        $this->add_permission_individuals();
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
                DB::raw("COALESCE(COUNT(DISTINCT collaborations.company_id), 0) AS collaborations_count"),
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
                ->leftJoin('orders', 'companies.id', '=', 'orders.company_id')
                ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
                ->whereNull('companies.deleted_at')
                ->orderBy($sorter, $direction)
                ->groupBy('companies.id');

        if ($request->has('active') &&  $request->active != '') {
            $companies->where('companies.active', $request->active);
        }

        $companies = $companies->paginate(50);

        return view('admin.companies.index', compact('companies'));
    }


    public function search()
    {
        $search = Input::get('search');
        $active =  Input::get('active');
        $companies = '';
            $companies = new Company();
            if(isset($active)){
                $companies = $companies->where('active', '=',  (int)$active );
            }

            $companies = $companies->where(function ($q) use ($search) {
                if(isset($search)){
                    $q->where('en_name', 'like', '%' . $search . '%');
                    $q->orWhere('ar_name', 'like', '%' . $search . '%');
                    $q->orWhere('email', 'like', '%' . $search . '%');
                }

            }
            )->paginate(50);

        return view('admin.companies.index', compact('companies', 'search'));
    }


    public function create()
    {
        $countries = Address::where('parent_id', NULL)->get();
        $categories = Category::where('parent_id', NULL)->get();
        return view('admin.companies.single', compact('countries', 'categories'));
    }


    public function show($id)
    {
        $company = Company::find($id);
        return view('admin.companies.show', compact('company'));
    }

    public function show_users($id)
    {
        $users = User::where('company_id', $id)->where('active', 1)->paginate(50);
        return view('admin.companies.show_users', compact('users', 'id'));
    }

    public function show_users_search($id)
    {
        $search = Input::get('search');
        $users = User::where('company_id', $id)->where('active', 1)->where(function ($query) use ($search){
           $query->where('en_name', 'like', '%'.$search.'%');
           $query->orWhere('ar_name', 'like', '%'.$search.'%');
           $query->orWhere('badge_id', 'like', '%'.$search.'%');
           $query->orWhere('email', 'like', '%'.$search.'%');
           $query->orWhere('phone', 'like', '%'.$search.'%');
        })->paginate(50);

        return view('admin.companies.show_users', compact('users', 'id'));
    }

    public function view_user($id)
    {
        $user = User::where('id', $id)->first();
        return view('admin.companies.view_user', compact('user'));
    }

    public function statistics($id)
    {
        $company = Company::find($id);

        $monthly_orders = Order::where('company_id', $company->id)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->get();
        $data['monthly_orders_count'] = $monthly_orders->count();
        $data['monthly_open'] = $monthly_orders->where('completed', 0)->where('canceled',0)->count();
        $data['monthly_closed'] = $monthly_orders->where('completed', 1)->where('canceled',0)->count();
        $data['monthly_canceled'] = $monthly_orders->where('canceled', 1)->count();

        $yearly_orders = Order::where('company_id', $company->id)->whereYear('created_at', date('Y'))->get();
        $data['yearly_orders_count'] = $yearly_orders->count();
        $data['yearly_open'] = $yearly_orders->where('completed', 0)->where('canceled', 0)->count();
        $data['yearly_closed'] = $yearly_orders->where('completed', 1)->count();
        $data['yearly_canceled'] = $yearly_orders->where('canceled', 1)->count();

        $monthly_rate = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereMonth('order_rates.created_at', date('m'))->whereYear('order_rates.created_at', date('Y'))->where('company_id', $company->id);
        $data['monthly_rate_commitment']  = $monthly_rate->avg('commitment');
        $data['monthly_rate_performance'] = $monthly_rate->avg('performance');
        $data['monthly_rate_appearance']  = $monthly_rate->avg('appearance');
        $data['monthly_rate_cleanliness'] = $monthly_rate->avg('cleanliness');
        $data['monthly_rate_count'] = $monthly_rate->groupby('order_id')->count();

        $yearly_rate = OrderRate::join('orders', 'order_rates.order_id', '=', 'orders.id')->whereYear('order_rates.created_at', date('Y'))->where('company_id', $company->id);
        $data['yearly_rate_commitment']  = $yearly_rate->avg('commitment');
        $data['yearly_rate_performance'] = $yearly_rate->avg('performance');
        $data['yearly_rate_cleanliness'] = $yearly_rate->avg('cleanliness');
        $data['yearly_rate_appearance']  = $yearly_rate->avg('appearance');
        $data['yearly_rate_count']  = $yearly_rate->groupby('order_id')->count();

        $data['top_services'] = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')->where('company_id',$company->id)->groupby('service.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');
        $data['top_users'] = Order::select('users.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('users', 'orders.user_id', '=', 'users.id')->where('orders.company_id',$company->id)->groupby('users.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');
        $data['top_sub_companies'] = Order::select('sub_companies.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('users', 'orders.user_id', '=', 'users.id')->join('sub_companies', 'users.sub_company_id', '=', 'sub_companies.id')->where('orders.company_id',$company->id)->groupby('sub_companies.en_name')->orderBy('sum', 'DESC')->limit(4)->pluck('sum','name');

        $data['least_services']  = Order::select('service.en_name as name', DB::raw('SUM(total_amount) as sum') )->join('categories', 'orders.cat_id', '=', 'categories.id')->join('categories AS service','service.id','=','categories.parent_id')->where('company_id', $company->id)->groupBy('service.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum', 'name');
        $data['least_users'] = Order::select('users.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('users', 'orders.user_id', '=', 'users.id')->where('orders.company_id',$company->id)->groupby('users.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum','name');
        $data['least_sub_companies'] = Order::select('sub_companies.en_name as name', DB::raw('SUM(total_amount) as sum'))->join('sub_companies', 'orders.company_id', '=', 'sub_companies.parent_id')->where('orders.company_id',$company->id)->groupby('sub_companies.en_name')->orderBy('sum', 'ASC')->limit(4)->pluck('sum','name');

        // MONTHLY TOTAL TANSACTIONS CASH CHART
        $monthly_revenue = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y%m') AS t"),
                DB::raw("DATE_FORMAT(created_at, '%b %Y') AS m"),
                DB::raw('SUM(total_amount) as sum')
            )
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
        $daily_revenue = Order::select( DB::raw('CAST(created_at as DATE) as d'), DB::raw('SUM(total_amount) as sum') )
        ->where('company_id', $id)
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
        ->where('company_id', $id)
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

        return view('admin.home.dashboard', compact('data'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'address_id' => 'required|exists:addresses,id',
                'interest_fee' => 'required|numeric',
                'ar_name' => 'required|unique:companies,ar_name',
                'en_name' => 'required|unique:companies,en_name',
                'ar_desc' => 'required',
                'en_desc' => 'required',
                'email' => 'required|email|unique:companies,email',
                'phones' => 'required',
                'logo' => 'required|image',
                'item_limit' => 'required',
                'username' => 'required|unique:company_admins',
                'password' => 'required|confirmed|min:6',
                'mobile' => 'required|unique:provider_admins,phone',
                'badge_id' => 'required'
            ],
            [
                'address_id.required' => 'Address is required',
                'interest_fee.required' => 'Interest Fee is required',
                'interest_fee.numeric' => 'Interest Fee is not a number',
                'ar_name.required' => 'Arabic name is required',
                'ar_name.unique' => 'Arabic name already exists',
                'en_name.required' => 'English name is required',
                'en_name.unique' => 'English name already exists',
                'ar_desc.required' => 'Arabic description is required',
                'en_desc.required' => 'English description is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists',
                'phones.required' => 'Phones are required',
                'logo.required' => 'Logo is required',
                'item_limit.required' => 'Order Item Limit is required',
                'username.required' => 'Username is required',
                'username.exists' => 'Username already exists',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be 6 digits at minimum',
                'password.confirmed' => 'Password and its confirmation does not match',
                'mobile.required' => 'Admin Mobile is required',
                'mobile.unique' => 'Admin Mobile already exists',
                'badge_id.required' => 'Admin Badge ID is required',
            ]
        );
        //----------upload logo image-------------//
        $image = "";
        if($request->logo) {
            $image = unique_file($request->logo->getClientOriginalName());
            $request->logo->move(base_path().'/public/companies/logos/', $image);
        }
        $company = Company::create(
            [
                'address_id'    => $request->address_id,
                'interest_fee'  => $request->interest_fee,
                'ar_name'       => $request->ar_name,
                'en_name'       => $request->en_name,
                'ar_desc'       => $request->ar_desc,
                'en_desc'       => $request->en_desc,
                'email'         => $request->email,
                'phones'        => serialize(array_filter($request->phones)),
                'logo'          => $image,
                'item_limit'    => $request->item_limit
            ]
        );

        $admin = CompanyAdmin::create(
            [
                'role'          => 'system_admin',
                'company_id'    => $company->id,
                'badge_id'      => $request->badge_id,
                'active'        => 1,
                'name'          => $company->en_name,
                'email'         => $company->email,
                'phone'         => $request->mobile,
                'username'      => $request->username,
                'password'      => Hash::make($request->password),
                'image'         => $image
            ]
        );

        $permission = Permission::where('guard_name','company')->pluck('id')->toArray();
        $admin->syncPermissions($permission);

        return redirect('/admin/companies')->with('success', 'Company added successfully !');
    }


    public function edit($id)
    {
        $company = Company::find($id);
        $countries = Address::where('parent_id', NULL)->get();
        $categories = Category::where('parent_id', NULL)->get();
        return view('admin.companies.single', compact('company','countries','categories'));
    }


    public function update(Request $request)
    {
        $admin = CompanyAdmin::where('company_id', $request->company_id)->first();
        $request->merge(['admin_id' => $admin->id]);

        $this->validate($request,
            [
                'company_id' => 'required|exists:companies,id',
                'interest_fee' => 'required|numeric',
                'address_id' => 'sometimes|exists:addresses,id',
                'ar_name' => 'required|unique:companies,ar_name,'.$request->company_id,
                'en_name' => 'required|unique:companies,en_name,'.$request->company_id,
                'ar_desc' => 'required',
                'en_desc' => 'required',
                'email' => 'required|email|unique:companies,email,'.$request->company_id,
                'phones' => 'required',
                'logo' => 'sometimes|image',
                'item_limit' => 'required',
                'username' => 'required|unique:company_admins,username,'.$request->admin_id,
                'password' => 'sometimes|confirmed',

            ],
            [
                'interest_fee.required' => 'Interest Fee is required',
                'interest_fee.numeric' => 'Interest Fee is not a number',
                'ar_name.required' => 'Arabic name is required',
                'ar_name.unique' => 'Arabic name already exists',
                'en_name.required' => 'English name is required',
                'en_name.unique' => 'English name already exists',
                'ar_desc.required' => 'Arabic description is required',
                'en_desc.required' => 'English description is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists',
                'phones.required' => 'Phones are required',
                'logo.required' => 'Logo is required',
                'item_limit.required' => 'Order Item Limit is required',
                'username.required' => 'Username is required',
                'username.unique' => 'Username already exists',
                'password.required' => 'Password is required',
                'password.confirmed' => 'Password does not match',
            ]
        );


        $company = Company::where('id',$request->company_id)->first();
        if($request->address_id) $company->address_id = $request->address_id;
        $company->interest_fee = $request->interest_fee;
        $company->ar_name = $request->ar_name;
        $company->en_name = $request->en_name;
        $company->ar_desc = $request->ar_desc;
        $company->en_desc = $request->en_desc;
        $company->email = $request->email;
        $company->vat_registration = !empty($request->vat_registration)? $request->vat_registration: '';
        $company->vat = $request->vat;
        $company->po_box = $request->po_box;
        $company->en_organization_name = $request->en_organization_name;
        $company->ar_organization_name = $request->ar_organization_name;
        $company->order_process_id = (int)$request->order_process_id;
        $company->phones = serialize(array_filter($request->phones));
        $company->item_limit = $request->item_limit;
        if($request->logo) {
            $image = unique_file($request->logo->getClientOriginalName());
            $request->logo->move(base_path().'/public/companies/logos/', $image);
            $company->logo = $image;
        }
        if($request->cr_upload)
        {
         if($company->cr_upload){
            if(file_exists(base_path().'/public/providers/logos/'.$company->cr_upload)){
                unlink(base_path().'/public/providers/logos/'.$company->cr_upload);
            }
        }


            $name = unique_file($request->cr_upload->getClientOriginalName());
            $request->cr_upload->move(base_path().'/public/providers/logos/',$name);
            $company->cr_upload = $name;

        }
        if($request->vat_certificate_upload)
        {
            if($company->vat_upload){
            if(file_exists(base_path().'/public/providers/logos/'.$company->vat_upload)){
                unlink(base_path().'/public/providers/logos/'.$company->vat_upload);
            }
        }


            $name = unique_file($request->vat_certificate_upload->getClientOriginalName());
            $request->vat_certificate_upload->move(base_path().'/public/providers/logos/',$name);
            $company->vat_upload = $name;

        }
        if($request->agreement_upload)
        {
            if($company->agreement_upload){
            if(file_exists(base_path().'/public/providers/logos/'.$company->agreement_upload)){
                unlink(base_path().'/public/providers/logos/'.$company->agreement_upload);
            }
        }

            $name = unique_file($request->agreement_upload->getClientOriginalName());
            $request->agreement_upload->move(base_path().'/public/providers/logos/',$name);
            $company->agreement_upload = $name;

        }

        $company->save();

        $admin->username = $request->username;
        if($request->password) {
          $admin->password = Hash::make($request->password);
        }
        $admin->save();

        if($company->active == 1) $text = 'active';
        else $text = 'suspended';

        return redirect('/admin/companies')->with('success', 'Company updated successfully');
    }


    public function destroy(Request $request)
    {
        $this->validate($request, [
            'company_id' => 'required|exists:companies,id',
        ]);
        $company = Company::where('id',$request->company_id)->first();
        $company->delete();
        return redirect('/admin/companies')->with('success', 'Company deleted successfully !');
    }


    public function change_state(Request $request)
    {
        $this->validate($request, [
            'company_id' => 'required|exists:companies,id',
            'state'      => 'required|in:0,1',
        ]);

        $company = Company::find($request->company_id);
        $company->active = $request->state;
        $company->save();

        $msg = $company->active
        ? 'Company activated successfully !'
        : 'Company suspended successfully !';

        return back()->with('success', $msg);
    }


    public function get_subscriptions($company_id)
    {
        $company = Company::find($company_id);
        $subscriptions = CompanySubscription::where('company_id', $company_id)->first();

        $subs = isset($subscriptions) ? unserialize($subscriptions->subs) : [];

        $cats = Category::where('parent_id', NULL)->get();

        return view('admin.companies.subscriptions', compact('company','subs','cats'));
    }


    public function set_subscriptions(Request $request)
    {
        $this->validate($request, [
            'company_id' => 'required|exists:companies,id',
            'subs'       => 'required|array',
        ]);
        $datas = [];
        $subs = $request->subs;
        $type = ['urgent', 'emergency', 'scheduled'];
        $cats = Category::where('parent_id', NULL)->get();
        $slaSubCategory = $this->SlaModel::where('provider_id' , $request->company_id)->select('sub_category_id')->groupBy('sub_category_id')->get();
        // dd($subs, $slaSubCategory);

        $dataArray = [];
        foreach(collect($slaSubCategory)->toArray() as $slaSub){
            // dd((string)$slaSub['sub_category_id'], $subs);
            $dataArray =   array_filter($subs, function() use($slaSub, $subs) { return !in_array((string)$slaSub['sub_category_id'], $subs); });
        }
        // dd($dataArray);
        foreach($cats as $key => $cat){
            foreach($cat->sub_cats as $sub){
                if(in_array($sub->id, $subs)) {
                  foreach($type as $value){
                    $defaultTime = '00:00';
                    if($value == 'scheduled'){
                        $defaultTime = '01:60';
                    }
                    $datas[] = [
                        "category_id" => $sub->parent_id,
                        "sub_category_id" => $sub->id,
                        "request_type" => $value,
                        'provider_id' => $request->company_id,
                        'response_time' =>$defaultTime,
                        'assessment_time' => $defaultTime,
                        'rectification_time' =>$defaultTime,

                    ];
                  }
                }
            }
        }
         $sla = $this->SlaModel::where('provider_id' , $request->company_id);
         if(!empty($sla->first())){
            $this->SlaModel::where('provider_id' , $request->company_id)->delete();
         }
        foreach($datas as $data){

            $request->request->add(['provider_id' => $data['provider_id']]);
            $request->request->add(['category_id'  => $data['category_id']]);
            $request->request->add(['sub_category_id'=> $data['sub_category_id']]);
            $request->request->add(['request_type' => $data['request_type']]);
            $request->request->add(['response_time' => $data['response_time']]);
            $request->request->add(['assessment_time' => $data['assessment_time']]);
            $request->request->add(['rectification_time' => $data['rectification_time']]);

            $this->SlaModel->SaveOrUpdate($this->SlaModel, $request);
        }
        CompanySubscription::updateOrCreate(
            ['company_id' => $request->company_id],
            ['subs'       => serialize($request->subs)]
        );

        return redirect('/admin/companies')->with('success', 'Subscriptions have been set successfully !');
    }

    public function date_year_orders($id, $type)
    {
        $company = Company::where('id',$id)->select('id')->first();
        $collaboration = Collaboration::where('company_id',$company->id)->pluck('provider_id');
        $providers = Provider::whereIn('id',$collaboration)->get();

        $subs = CompanySubscription::where('company_id', $id)->first();
        if(isset($subs)){
            $cat_ids = Category::whereIn('id', unserialize($subs->subs))->pluck('parent_id');
            $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();
        }else{
            $cats = Category::where('parent_id', null)->get();
        }
        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('company_id', $id);
        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('company_id', $id);

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
            $orders = $monthly_orders->where('completed', 1)->where('canceled', 0);;
        }
        elseif($type == 'yearly_closed')
        {
            $orders = $yearly_orders->where('completed', 1)->where('canceled', 0);;
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

        return view('admin.orders.index2',
            compact('orders', 'id', 'type','cats','company','providers'));
    }

    public function date_search($id, $type,Request $request)
    {
        $company = Company::where('id',$id)->select('id')->first();
        $collaboration = Collaboration::where('company_id',$company->id)->pluck('provider_id');
        $providers = Provider::whereIn('id',$collaboration)->get();

        $subs = CompanySubscription::where('company_id', $id)->first();
        if(isset($subs)){
            $cat_ids = Category::whereIn('id', unserialize($subs->subs))->pluck('parent_id');
            $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();
        }else{
            $cats = Category::where('parent_id', null)->get();
        }

        $monthly_orders = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('company_id', $id);
        $yearly_orders = Order::whereYear('created_at', date('Y'))->where('company_id', $id);

        $search = Input::get('search');

        $get_orders = new Order;

        $show_orders = $get_orders->check_search($type,$monthly_orders,$yearly_orders);

        $orders = $get_orders->search($show_orders,$search,$id,$request->provider_name,$request->company_id,
            $request->sub_company,$request->from,$request->to,$request->main_cats,$request->sub_cats,$request->price_range,
            $request->service_type);
        $orders = $orders['orders'];

        return view('admin.orders.search2',
            compact('orders','search', 'id', 'type','company','cats','providers'));
    }

    public function date_items($id,$type)
    {
        $this_year = new Carbon('first day of january this year');
        $this_month = new Carbon('first day of this month');

        $yearly_orders = Order::raw('table orders')->where('company_id', $id)->where('created_at','>=', $this_year->toDateTimeString());
        $monthly_orders = Order::raw('table orders')->where('company_id', $id)->where('created_at','>=', $this_month->toDateTimeString());

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

    public function date_price($id,$type)
    {
        $company_ids = Company::whereId($id)->select('id')->first()->id;
        $this_month = new Carbon('first day of this month');
        $this_year = new Carbon('first day of january this year');

        $monthly_orders = Order::raw('table orders')->where('company_id', $id)->where('created_at','>=', $this_month->toDateTimeString());
        $yearly_orders = Order::raw('table orders')->where('company_id', $id)->where('created_at','>=', $this_year->toDateTimeString());

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

            return view('admin.orders.price_statistics',compact('orders','company_ids','total_sum'));

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

            return view('admin.orders.item_statistics',compact('orders','company_ids','total_sum'));}
    }

    public function date_rate($id,$type)
    {
        //month
        $this_month = new Carbon('first day of this month');
        $monthly_orders = Order::raw('table orders')->where('company_id', $id)->where('created_at','>=', $this_month->toDateTimeString())->get();
        $monthly_orders_ids = $monthly_orders->pluck('id');
        $monthly_rates_ids = OrderRate::whereIn('order_id', $monthly_orders_ids)->pluck('order_id');

        //year
        $this_year = new Carbon('first day of january this year');
        $yearly_orders = Order::raw('table orders')->where('company_id', $id)->where('created_at','>=', $this_year->toDateTimeString())->get();
        $yearly_orders_ids = $yearly_orders->pluck('id');
        $yearly_rates_ids = OrderRate::whereIn('order_id', $yearly_orders_ids)->pluck('order_id');

        if($type == 'monthly_rate'){
            $orders =  Order::whereIn('id', $monthly_rates_ids)->get();
        }elseif($type == 'yearly_rate'){
            $orders = Order::whereIn('id', $yearly_rates_ids)->get();
        }
        return view('admin.orders.rate_dashboard', compact('orders', 'type'));
    }

    public  function get_sub_cats($id,$parent)
    {
        $arr_parent = explode(',',$parent);
        $subs = CompanySubscription::where('company_id', $id)->first()->subs;
        $cats = Category::whereIn('id', unserialize($subs))->whereIn('parent_id', $arr_parent)->select('id','en_name')->get();
        return response()->json($cats);
    }

    public  function get_sub_companies($id,$parent)
    {
        $arr_parent = explode(',',$parent);
        $subs = CompanySubscription::where('company_id', $id)->first()->subs;
        $cats = Category::whereIn('id', unserialize($subs))->whereIn('parent_id', $arr_parent)->select('id','en_name')->get();
        return response()->json($cats);
    }

    public function add_permission_individuals()
    {
        $admin = CompanyAdmin::where('username', 'individuals')->select('id')->first();

        $check =
        DB::table('model_has_permissions')
        ->where('model_type', 'App\Models\CompanyAdmin')
        ->where('model_id', $admin->id)
        ->exists();
        if($check){
            return 0;
        }
        $permissions = Permission::where('guard_name','company')->get();

        foreach($permissions as $permission){
            DB::table('model_has_permissions')->insert([
                'permission_id'  => $permission->id,
                'model_type'     => 'App\Models\CompanyAdmin',
                'model_id'       => $admin->id,
            ]);
        }
    }
}
