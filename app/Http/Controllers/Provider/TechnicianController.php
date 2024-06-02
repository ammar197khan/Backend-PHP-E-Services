<?php

namespace App\Http\Controllers\Provider;


use App\Models\Category;
use App\Models\Collaboration;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderRate;
use App\Models\ProviderSubscription;
use App\Models\PushNotify;
use App\Models\Rotation;
use App\Models\SubCompany;
use App\Models\Technician;
use App\Models\TechNot;
use App\Models\TechToken;
use App\Models\TechnicainRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TechsImport;
use ZipArchive;
use DB;


class TechnicianController extends Controller
{
    public function index(Request $request, $state)
    {
        $sorter     = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';
        $techs =
            DB::table('technicians')->select([
                'technicians.id',
                'technicians.en_name',
                'technicians.ar_name',
                'technicians.email',
                'technicians.phone',
                'technicians.type',
                'technicians.badge_id',
                'technicians.active',
                'technicians.busy',
                'technicians.online',
                'technicians.cat_ids',
                'technicians.image',
                'technicians.company_id',
                'technicians.technician_role_id',
                'technicians.provider_id as tech_provider_id',
                'providers.en_name AS provider',
                'rotations.en_name AS rotation',
                DB::raw("COALESCE(COUNT(DISTINCT orders.id), 0) AS orders_count"),
                DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
                DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
                DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
                DB::raw("COALESCE(COUNT(DISTINCT order_rates.id), 0) AS rate_count"),
                DB::raw("COALESCE(AVG(order_rates.average), 0) AS rate_average"),
                'technicians.created_at',
                'technicians.updated_at',
                'technicain_roles.name As techRole'
            ])
                ->leftJoin('technicain_roles', 'technicians.technician_role_id', '=', 'technicain_roles.id')
                ->leftJoin('providers', 'technicians.provider_id', '=', 'providers.id')
                ->leftJoin('rotations', 'technicians.rotation_id', '=', 'rotations.id')
                ->leftJoin('orders', 'technicians.id', '=', 'orders.tech_id')
                ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')

                ->where('technicians.provider_id', provider()->provider_id)
                ->orderBy($sorter, $direction)
                ->groupBy('technicians.id');

        if(isset($request->tech_status) && $request->tech_status != 'all')
        {
            $techs->where('technicians.busy', $request->tech_status);
        }
        if($state == 'active')
        {
            $techs->where('technicians.active', 1);
        }elseif($state == 'suspended')
        {
            $techs->where('technicians.active', 0);
        }
        $techs = $techs->paginate(50);

        return view('provider.technicians.index', compact('techs','state'));
    }


    public function search($state, Request $request)
    {
        $sorter     = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';
        $techs =
            DB::table('technicians')->select([
                'technicians.id',
                'technicians.en_name',
                'technicians.ar_name',
                'technicians.email',
                'technicians.phone',
                'technicians.type',
                'technicians.badge_id',
                'technicians.active',
                'technicians.busy',
                'technicians.online',
                'technicians.cat_ids',
                'technicians.image',
                'technicians.technician_role_id',
                'technicians.provider_id as tech_provider_id',
                'providers.en_name AS provider',
                'rotations.en_name AS rotation',
                DB::raw("COALESCE(COUNT(DISTINCT orders.id), 0) AS orders_count"),
                DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
                DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
                DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
                DB::raw("COALESCE(COUNT(DISTINCT order_rates.id), 0) AS rate_count"),
                DB::raw("COALESCE(AVG(order_rates.average), 0) AS rate_average"),
                'technicians.created_at',
                'technicians.updated_at',
                'technicain_roles.name As techRole'
            ])

                ->leftJoin('technicain_roles', 'technicians.technician_role_id', '=', 'technicain_roles.id')
                ->leftJoin('providers', 'technicians.provider_id', '=', 'providers.id')
                ->leftJoin('rotations', 'technicians.rotation_id', '=', 'rotations.id')
                ->leftJoin('orders', 'technicians.id', '=', 'orders.tech_id')
                ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
                ->where('technicians.provider_id', provider()->provider_id)
                ->orderBy($sorter, $direction)
                ->groupBy('technicians.id');

        $active = $state == 'active' ? 1 : 0;

        $search = Input::get('search');
        $techs = $techs->where('technicians.active', $active)->where(function($q) use($search)
        {
            $q->where('technicians.en_name','like','%'.$search.'%');
            $q->orWhere('technicians.ar_name','like','%'.$search.'%');
            $q->orWhere('technicians.email','like','%'.$search.'%');
            $q->orWhere('technicians.phone','like','%'.$search.'%');
            $q->orWhere('technicians.badge_id', $search);
        }
        )->paginate(50);


        return view('provider.technicians.index', compact('techs','search','state'));
    }

    public function tech_status($state,Request $request)
    {
        if($state == 'active')
        {
            if ($request->tech_status == 'all') {
                $techs = Technician::where('active',1)->paginate(50);
            } else {
                $techs = Technician::where('active',1)->where('busy', $request->tech_status)->paginate(50);
            }
        }elseif($state == 'suspended'){
            if ($request->tech_status == 'all') {
                $techs = Technician::where('active',0)->paginate(50);
            } else {
                $techs = Technician::where('active',0)->where('busy', $request->tech_status)->paginate(50);
            }
        }
        return view('provider.technicians.index', compact('techs','state'));
    }

    public function statistics_search()
    {
        $search = Input::get('search');
        if($search != ''){

            $techs = Technician::where('provider_id', provider()->provider_id)->where(function($q) use($search)
            {
                $q->where('en_name','like','%'.$search.'%');
                $q->orWhere('ar_name','like','%'.$search.'%');
                $q->orWhere('email','like','%'.$search.'%');
                $q->orWhere('phone','like','%'.$search.'%');
                $q->orWhere('badge_id', $search);
            }
            )->paginate(50);
        }else{
            $techs = Technician::where('provider_id', provider()->provider_id)->paginate(50);
        }
        $techs_count = Technician::where('provider_id', provider()->provider_id)->count();
        $a_techs = Technician::where('provider_id', provider()->provider_id)->where('active', 1)->count();
        $s_techs = Technician::where('provider_id', provider()->provider_id)->where('active', 0)->count();
        $b_techs = Technician::where('provider_id', provider()->provider_id)->where('busy', 1)->count();

        $this_month = new Carbon('first day of this month');
        $this_year = new Carbon('first day of this year');

        $monthly_techs = Technician::where('created_at','>=',$this_month)->count();
        $yearly_techs = Technician::where('created_at','>=',$this_year)->count();

        $orders_ids = Order::Where('provider_id', provider()->provider_id)->pluck('id');
        $orders = OrderRate::whereIn('order_id', $orders_ids)->get();
        $rate_commitment = (string)round($orders->pluck('commitment')->avg(),0);
        $rate_cleanliness = (string)round($orders->pluck('cleanliness')->avg(),0);
        $rate_performance = (string)round($orders->pluck('performance')->avg(),0);
        $rate_appearance = (string)round($orders->pluck('appearance')->avg(),0);

        $orders = Order::raw('table orders')->where('provider_id', provider()->provider_id);
        $monthly_orders_all = $orders->where('created_at', '>', new Carbon('first day of this month'));
        $monthly_orders = $monthly_orders_all->count();
        $yearly_orders_all = $orders->where('created_at', '>', new Carbon('first day of this month'));
        $yearly_orders = $yearly_orders_all->count();
        $monthly_canceled_orders = $monthly_orders_all->where('canceled', 1)->count();
        $yearly_canceled_orders = $yearly_orders_all->where('canceled', 1)->count();


        return view('provider.technicians.statistics', compact('techs','techs_count','a_techs','s_techs','b_techs','monthly_techs','yearly_techs','rate_appearance','rate_cleanliness','rate_commitment','rate_performance','monthly_orders','yearly_orders','monthly_canceled_orders','yearly_canceled_orders'));
    }


    public function create()
    {
        $subs = ProviderSubscription::where('provider_id', provider()->provider_id)->first();
        if($subs == NULL) return back()->with('error','You have not assigned to categories subscriptions yet,contact customer support for more info !');

        $sub_cats = Category::whereIn('id', unserialize($subs->subs))->get();
        $cats = Category::whereIn('id', $sub_cats->pluck('parent_id'))->get();

        $rotations = Rotation::where('provider_id', provider()->provider_id)->get();

        $provider = Collaboration::where('provider_id', provider()->provider_id)->pluck('company_id');
        $companies = Company::whereIn('id', $provider)->select('id', 'en_name')->get();
        $technicianRoles =  TechnicainRole::all();
        return view('provider.technicians.single', compact('cats','rotations', 'companies', 'technicianRoles'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
               'cat_ids' => 'required',
//                'cat_ids.*' => 'required|exists:categories,id',
                'badge_id'=> [
                    'required',
                    Rule::unique('technicians')->where(function ($query) use($request){
                        $query->where('badge_id', $request->badge_id)
                            ->where('provider_id', provider()->provider_id);
                    }),
                ],
                'password' => 'required|min:6|confirmed',
                'ar_name' => 'required',
                'en_name' => 'required',
                'company_id' => 'required',
//                'rotation_id' => 'sometimes|nullable|exists:rotations,id,provider_id,',provider()->provider_id,
                'email' => 'required|unique:technicians,email',
                'phone' => 'required|unique:technicians,phone',
                'image' => 'sometimes|image',
                'technician_role_id' => 'required'
            ],
            [
               'cat_ids.required' => 'Category is required',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be 6 digits at least',
                'password.confirmed' => 'Password and its confirmation does not match',
                'ar_name.required' => 'Arabic Name is required',
                'en_name.required' => 'English Name is required',
//                'rotation_id.exists' => 'Rotation is invalid',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists,please choose another one',
                'phone.required' => 'Phone is required',
                'phone.unique' => 'Phone already exists,please choose another one',
                'image.image' => 'Image is invalid',
            ]
        );

        $technician = Technician::create(
            [
                'provider_id' => provider()->provider_id,
                'company_id' => $request->company_id,
                'sub_company_id' => isset($request->sub_company)?implode(',', $request->sub_company):null,
                'jwt' => str_random(25),
                'type' => 'employee',
                'badge_id' => $request->badge_id,
                'cat_ids' => implode(',',$request->cat_ids),
                'password' => Hash::make($request->password),
                'en_name' => $request->en_name,
                'ar_name' => $request->ar_name,
                'rotation_id' => $request->rotation_id ?? NULL,
                'email' => $request->email,
                'phone' => $request->phone,
                'technician_role_id' => (int)$request->technician_role_id,
            ]
        );

        if($request->image)
        {
            $image = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/providers/technicians/', $image);
            $technician->image = $image;
            $technician->save();
        }


        return redirect('/provider/technicians/active')->with('success', 'Technician added successfully !');
    }


    public function show($id,Request $request)
    {
//        $request->merge(['tech_id' => $id]);
//        $this->validate($request,
//            [
//                'tech_id' => 'required|exists:technicians,id,provider_id,'.provider()->provider_id
//            ]
//        );

        $technician = Technician::find($id);
        $technician['rate'] = $technician->get_all_rate($id);
        $technician['all_rates'] = $technician->get_rates($id);

        return view('provider.technicians.show', compact('technician'));
    }


    public function edit($tech_id)
    {
        $technician = Technician::where('id', $tech_id)->first();
        $cats = Category::where('parent_id', NULL)->get();
        $rotations = Rotation::where('provider_id', provider()->provider_id)->get();

        $provider = Collaboration::where('provider_id', provider()->provider_id)->pluck('company_id');
        $companies = Company::whereIn('id', $provider)->select('id', 'en_name')->get();
        $technicianRoles =  TechnicainRole::all();
        return view('provider.technicians.single', compact('cats','technician', 'rotations', 'companies', 'technicianRoles'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'tech_id' => 'required|exists:technicians,id',
                'cat_ids' => 'sometimes',
                'password' => 'sometimes|confirmed',
                'ar_name' => 'required',
                'en_name' => 'required',
                'rotation_id' => 'sometimes|nullable|exists:rotations,id',
                'email' => 'required|unique:technicians,email,'.$request->tech_id,
                'phone' => 'required|unique:technicians,phone,'.$request->tech_id,
                'image' => 'sometimes|image',
                'technician_role_id' => 'required'
            ],
            [
                'cat_ids.required' => 'Category is required',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be 6 digits at least',
                'password.confirmed' => 'Password and its confirmation does not match',
                'ar_name.required' => 'Arabic Name is required',
                'en_name.required' => 'English Name is required',
                'rotation_id.exists' => 'Rotation is invalid',
                'email.required' => 'Email is required',
                'phone.required' => 'Phone is required',
                'image.image' => 'Image is invalid',
            ]
        );

        $email_check = Technician::where('id','!=',$request->tech_id)->where('provider_id', provider()->provider_id)->where('email', $request->email)->first();
        $phone_check = Technician::where('id','!=',$request->tech_id)->where('provider_id', provider()->provider_id)->where('phone', $request->phone)->first();
        $badge_check = Technician::where('badge_id', $request->badge_id)->where('provider_id',provider()->provider_id)->where('id','!=',$request->tech_id)->first();

        if($badge_check) return back()->with('error', 'Sorry,this Badge ID already exists');
        if($email_check) return back()->with('error', 'Sorry,email already exists,please change to another one');
        if($phone_check) return back()->with('error', 'Sorry,phone already exists,please change to another one');

        $technician = Technician::where('id', $request->tech_id)->first();
            $technician->badge_id = $request->badge_id;
            $technician->company_id = $request->company_id;
            if($request->sub_company) $technician->sub_company_id = implode(',',$request->sub_company);
            if($request->cat_ids) $technician->cat_ids = implode(',',$request->cat_ids);
            $technician->en_name = $request->en_name;
            $technician->ar_name = $request->ar_name;
            $technician->technician_role_id = (int)$request->technician_role_id;
            if($request->rotation_id != $technician->rotation_id)
            {
                $ar_text = 'تم تغير وقت المناوبة';
                $en_text = 'There is a new rotation shift !';

                TechNot::create
                (
                    [
                        'type' => 'rotation',
                        'tech_id' => $technician->id,
                        'ar_text' => $ar_text,
                        'en_text' => $en_text
                    ]
                );

                $token = TechToken::where('tech_id', $technician->id)->pluck('token');
                PushNotify::tech_send($token,$ar_text,$en_text,'rotation');
            }
            if($request->rotation_id) $technician->rotation_id = $request->rotation_id;
            $technician->email = $request->email;
            $technician->phone = $request->phone;
            $technician->badge_id = $request->badge_id;
            if($request->image)
            {
                $image = unique_file($request->image->getClientOriginalName());
                $request->image->move(base_path().'/public/providers/technicians/', $image);
                $technician->image = $image;
            }
            if($request->password) $technician->password = Hash::make($request->password);
        $technician->save();

        return redirect('/provider/technicians/active')->with('success', 'Technician updated successfully !');
    }


    public function change_state(Request $request)
    {
        $this->validate($request,
            [
                'tech_id' => 'required|exists:technicians,id',
                'state' => 'required|in:0,1',
            ]
        );

        $technician = Technician::find($request->tech_id);
            $technician->active = $request->state;
        $technician->save();

        if($technician->active == 1)
        {
            return back()->with('success', 'Technician activated successfully !');
        }
        else
        {
            return back()->with('success', 'Technician suspended successfully !');
        }
    }


    public function change_password(Request $request)
    {
        $this->validate($request,
            [
                'tech_id' => 'required|exists:technicians,id',
                'password' => 'required|min:6|confirmed',
            ]
        );

        $technician = Technician::find($request->tech_id);
            $technician->password = Hash::make($request->password);
        $technician->save();

        return back()->with('success', 'Technician password changed successfully !');
    }


//    public function destroy(Request $request)
//    {
//        $this->validate($request,
//            [
//                'tech_id' => 'required|exists:technicians,id,provider_id,'.provider()->provider_id,
//            ]
//        );
//
//        Technician::where('id', $request->tech_id)->delete();
//
//        return back()->with('success', 'Technician deleted successfully !');
//    }


    public function statistics()
    {
        $techs = Technician::where('provider_id', provider()->provider_id)->paginate(50);
        $techs_count = Technician::where('provider_id', provider()->provider_id)->count();
        $a_techs = Technician::where('provider_id', provider()->provider_id)->where('active', 1)->count();
        $s_techs = Technician::where('provider_id', provider()->provider_id)->where('active', 0)->count();
        $b_techs = Technician::where('provider_id', provider()->provider_id)->where('busy', 1)->count();

        $this_month = new Carbon('first day of this month');
        $this_year = new Carbon('first day of this year');

        $monthly_techs = Technician::where('created_at','>=',$this_month)->count();
        $yearly_techs = Technician::where('created_at','>=',$this_year)->count();

        $orders_ids = Order::Where('provider_id', provider()->provider_id)->pluck('id');
        $orders = OrderRate::whereIn('order_id', $orders_ids)->get();
        $rate_commitment = (string)round($orders->pluck('commitment')->avg(),0);
        $rate_cleanliness = (string)round($orders->pluck('cleanliness')->avg(),0);
        $rate_performance = (string)round($orders->pluck('performance')->avg(),0);
        $rate_appearance = (string)round($orders->pluck('appearance')->avg(),0);

        $orders = Order::raw('table orders')->where('provider_id', provider()->provider_id);
        $monthly_orders_all = $orders->where('created_at', '>', new Carbon('first day of this month'));
        $monthly_orders = $monthly_orders_all->count();
        $yearly_orders_all = $orders->where('created_at', '>', new Carbon('first day of this month'));
        $yearly_orders = $yearly_orders_all->count();
        $monthly_canceled_orders = $monthly_orders_all->where('canceled', 1)->count();
        $yearly_canceled_orders = $yearly_orders_all->where('canceled', 1)->count();

        return view('provider.technicians.statistics', compact('techs','techs_count','a_techs','s_techs','b_techs','monthly_techs','yearly_techs','rate_appearance','rate_cleanliness','rate_commitment','rate_performance','monthly_orders','yearly_orders','monthly_canceled_orders','yearly_canceled_orders'));
    }


    public function excel_view()
    {
        return view('provider.technicians.upload');
    }


    public function excel_upload(Request $request)
    {
        $this->validate($request,
            [
                'file' => 'required|file'
            ]
        );

        $array = Excel::toArray(new TechsImport(),$request->file('file'));

        foreach($array[0] as $data)
        {
            $data = array_filter($data);
            if(count($data) > 0)
            {
                try
                {
                    $request->merge(['badge_id' => $data[0],'cat_ids' => $data[2], 'email' => $data[5],'phone' => $data[6],'status' => $data[1],'en_name' => $data[3], 'ar_name' => $data[4], 'password' => $data[7], 'company_id' => $data[8] ]);

                    $cats = explode(',', $data[2]);

                    foreach($cats as $cat)
                    {
                        $this_cat = Category::where('id', $cat)->where('type', 2)->first();

                        if($this_cat == NULL)
                        {
                            return back()->with('error', 'Sorry,a technician with badge_id ' . (integer)$data[0] . ' have an invalid category id,which is '.$cat);
                        }
                    }

                    if(isset($data[9]))
                    {
                        $sub_companies = explode(',', $data[9]);

                        foreach($sub_companies as $sub_company)
                        {
                            $this_sub = SubCompany::where('id', $sub_company)->first();

                            if($this_sub == NULL)
                            {
                                return back()->with('error', 'Sorry,a technician with badge_id ' . (integer)$data[0] . ' have an invalid sub company id,which is '.$sub_company);
                            }
                        }
                    }else{
                        $data[9] = 0;
                    }
                }
                catch (\Exception $e)
                {
                    return back()->with('error','Missing Column | '.$e->getMessage().',Offsets start from 0');
                }


                $this->validate($request,
                    [
                        'badge_id' => 'required',
                        'cat_ids' => 'required',
                        'email' => 'required|email|unique:technicians,email,'.$request->badge_id.',badge_id',
                        'phone' => 'required|unique:technicians,phone,'.$request->badge_id.',badge_id',
                        'status' => 'required|in:active,suspended',
                        'en_name' => 'required',
                        'ar_name' => 'required',
                        'password' => 'required',
                        'company_id' => 'required',
                    ],
                    [
                        'badge_id.required' => 'Missing data in Badge ID column.',
                        'cat_ids.required' => 'Missing data in Categories column.',
                        'email.required' => 'Missing data in Email column.',
                        'email.email' => 'Invalid data in Email column which is '.$request->email.'.',
                        'email.unique' => 'Email already exists,which is '.$request->email.'.',
                        'phone.required' => 'Missing data in Phone column.',
                        'phone.unique' => 'Phone already exists,which is '.$request->phone.'.',
                        'status.required' => 'Missing data in Status column.',
                        'status.in' => 'Invalid data in Status column,which is '.$request->active.',only active & suspended are allowed.',
                        'en_name.required' => 'Missing data in English Name column.',
                        'ar_name.required' => 'Missing data in Arabic Name column.',
                        'password.required' => 'Missing data in password column.',
                        'company_id.required' => 'Missing data in Company ID column.',
                        'sub_company_id.required' => 'Missing data in Company ID column.',
                    ]
                );

                if($request->status == 'active' ) $status = 1;
                else $status = 0;

                $provider = Collaboration::where('provider_id', provider()->provider_id)->pluck('company_id');

                if(!in_array($data[8], $provider->toArray()))
                {
                    return back()->with('error', 'Sorry,company id is invalid ');
                }

//                $sub_company = SubCompany::whereIn('parent_id', $provider)->pluck('id');
//                if(!in_array($data[9], $sub_company->toArray()))
//                {
//                    return back()->with('error', 'Sorry,sub company id is invalid ');
//                }

                Technician::updateOrCreate
                (
                    [
                        'provider_id' => provider()->provider_id,
                        'badge_id' => $data[0],
                    ],
                    [
                        'jwt' => str_random(25),
                        'active' => $status,
                        'cat_ids' => $data[2],
                        'en_name' => $data[3],
                        'ar_name' => $data[4],
                        'email' => $data[5],
                        'phone' => $data[6],
                        'password' => Hash::make($data[7]),
                        'company_id' => $data[8],
                        'sub_company_id' => $data[9],

                    ]
                );
            }
        }

        return redirect('/provider/technicians/active')->with('success', 'Technicians uploaded successfully');
    }


    public function images_view()
    {
        return view('provider.technicians.upload_images');
    }


    public function images_upload(Request $request)
    {
        $this->validate($request,
            [
                'file' => 'required|mimes:zip'
            ],
            [
                'file.required' => 'Compressed file is required',
                'file.mimes' => 'Compressed file must be a .zip',
            ]
        );

        try
        {
            $zip = new ZipArchive();
            $tmp_dir = base_path('/public/providers/'.provider()->provider_id.'_tmp_images');

            try
            {
                $zip->open($request->file);
                $zip->extractTo($tmp_dir);

                $images = array_diff(scandir($tmp_dir),['.','..']);

                foreach($images as $image)
                {
                    $explode = explode('.',$image);

                    $tech = Technician::where('provider_id', provider()->provider_id)->where('badge_id', $explode[0])->first();

                    if($tech)
                    {
                        $name = unique_file($image);

                        File::copy($tmp_dir.'/'.$image,base_path().'/public/providers/technicians/'.$name);
                        File::delete($tmp_dir.'/'.$image);

                        if($tech->image != 'default_technician.png') $old_image = $tech->image;
                        $tech->image = $name;
                        $tech->save();

                        if(isset($old_image)) unlink(base_path().'/public/providers/technicians/'.$old_image);
                    }
                    else
                    {
                        return back()->with('error', 'Invalid Badge ID for the image named '. $image);
                    }
                }

                rmdir($tmp_dir);
            }
            catch(\Exception $e)
            {
                rmdir($tmp_dir);
                return back()->with('error', 'Error has occurred while unzipping the file | '. $e);
            }

        }
        catch (\Exception $e)
        {
            return back()->with('error', 'Error has occurred while uploading the zip file| '.$e->getMessage());
        }

        return redirect('/provider/technicians/active')->with('success', 'Images uploaded & set successfully');
    }


    public function orders_request($tech_id, Request $request)
    {
//        $request->merge(['tech_id' => $tech_id]);
//        $this->validate($request,
//            [
//                'tech_id' => 'required|exists:technicians,id,provider_id,'.provider()->provider_id
//            ]
//        );

        $techs = Technician::where('provider_id', provider()->provider_id)->select('id','en_name')->get();
        return view('provider.technicians.orders_info_request', compact('tech_id','techs'));
    }


    public function orders_show(Request $request)
    {
        if($request->from && $request->to)
        {
            $this->validate($request,
                [
                    'tech_id' => 'exists:technicians,id,provider_id,'.provider()->provider_id,
                    'from' => 'required|date',
                    'to' => 'required|date'
                ],
                [
                    'tech_id.required' => 'Please choose a technician',
                    'tech_id.exists' => 'Invalid Technician',
                    'from.required' => 'Please choose a date to start from',
                    'from.date' => 'Please choose a valid date to start from',
                    'to.required' => 'Please choose a date to end with',
                    'to.date' => 'Please choose a valid date to end with',
                ]
            );

            $orders = Order::where('provider_id', provider()->provider_id)->where('tech_id', $request->tech_id)->where('completed',1)
                ->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->get();
            $orders[] = collect(['total' => $orders->sum('order_total')]);

            $tech = Technician::where('id', $request->tech_id)->select('id','en_name as name')->first();
            $from = $request->from;
            $to = $request->to;

            return view('provider.technicians.orders_info_show', compact('orders','tech','from','to'));
        }else{
            $orders = Order::where('provider_id', provider()->provider_id)->where('tech_id', $request->tech_id)->get();
            $orders[] = collect(['total' => $orders->sum('order_total')]);

            $tech = Technician::where('id', $request->tech_id)->select('id','en_name as name')->first();

            return view('provider.technicians.orders_info_show', compact('orders','tech'));
        }
    }


    public function orders_export(Request $request)
    {
        $this->validate($request,
            [
                'tech_id' => 'exists:technicians,id,provider_id,'.provider()->provider_id,
                'from' => 'required|date',
                'to' => 'required|date'
            ]
        );


        $orders = new Collection();
        $get_orders = Order::where('provider_id', provider()->provider_id)->where('tech_id', $request->tech_id)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->get();

        foreach($get_orders as $order)
        {
            if($order->type == 'urgent') $type = 'Urgent';
            elseif($order->type == 'scheduled') $type = 'Scheduled';
            else $type = 'Re-Scheduled';

            $collect['Category'] = $order->category->parent->en_name . ' - ' . $order->category->en_name;
            $collect['Date'] = $order->created_at->toDateTimeString();
            $collect['Type'] = $type;
            $collect['Revenue'] = $order->order_total;

            $orders = $orders->push($collect);
        }


        $orders[] = collect(['Category' => '-','Date' => '-','Type' => '-','Cost' => '-','Total' => $orders->sum('Revenue')]);

        $orders = $orders->toArray();
        $tech = Technician::where('id', $request->tech_id)->select('en_name')->first();
        $from = $request->from;
        $to = $request->to;
        $p_name = str_replace(' ','-',$tech->en_name);

        $filename = 'qareeb_tech_'.$p_name.'_'.$from.'_'.$to.'_orders_invoice.xls';


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

    public function excel_export($status)
    {
        if($status == 'active'){
            $status = 1;
        }else{
            $status = 0;
        }
        $techs = Technician::where('provider_id', provider()->provider_id)->where('active', $status)->select('badge_id as Badge ID', 'en_name as Name',
            'email as Email', 'phone as Phone', 'busy', 'online', 'cat_ids', 'company_id', 'sub_company_id')->get();

        foreach($techs as $tech){
            $tech['Categories'] = implode(" , ", $tech->get_category_list($tech->cat_ids));
            $tech['Busy'] = ($tech->busy == 1) ? 'Busy' : 'Not busy';
            $tech['Availability'] = ($tech->online == 1) ? 'Online' : 'Offline';
            $tech['Company'] = isset($tech->company->en_name)?$tech->company->en_name: '-';
            $tech['Sub Company'] = isset($tech->sub_company->en_name)?$tech->sub_company->en_name: '-';

            unset($tech->cat_ids, $tech->busy, $tech->online, $tech->company_id, $tech->sub_company_id, $tech->company, $tech->sub_company);
        }

        $techs = $techs->toArray();
        $filename = 'qareeb_technician_data.xls';

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $heads = false;
        foreach($techs as $tech)
        {
            if($heads == false)
            {
                echo implode("\t", array_keys($tech)) . "\n";
                $heads = true;
            }
            {
                echo implode("\t", array_values($tech)) . "\n";
            }
        }

        die();
    }
}
