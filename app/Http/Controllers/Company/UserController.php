<?php

namespace App\Http\Controllers\Company;

use DB;
use Storage;
use ZipArchive;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\TechNot;
use App\Models\Category;
use App\Models\TechToken;
use App\Models\HouseType;
use App\Models\Technician;
use App\Models\SubCompany;
use App\Models\PushNotify;
use Illuminate\Http\Request;
use App\Imports\UsersImport;
use App\Models\OrderTracking;
use Illuminate\Validation\Rule;
use App\Models\OrderUserDetail;
use App\Models\ProviderCategoryFee;
use App\Models\CompanySubscription;
use App\Models\Company;
use App\Models\OrderAddress;
use App\Models\Collaboration;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Database\Eloquent\Collection;

class UserController extends Controller
{
    public function index($state,Request $request)
    {
        $sorter     = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';
        $users =
            DB::table('users')->select([
                'users.id',
                'users.en_name',
                'users.ar_name',
                'users.email',
                'users.phone',
                'users.badge_id',
                'users.active',
                'users.image',
                DB::raw("COALESCE(COUNT(DISTINCT orders.id), 0) AS orders_count"),
                DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
                DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
                DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
                'users.created_at',
                'users.updated_at',
            ])
                ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
                ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
                ->where('users.company_id', company()->company_id)
                ->orderBy($sorter, $direction)
                ->groupBy('users.id');

        if($state == 'active')
        {
            $users->where('users.active', 1);
        }elseif($state == 'suspended')
        {
            $users->where('users.active', 0);
        }

        $users = $users->paginate(5);

        return view('company.users.index', compact('users'));
    }


    public function search($state,Request $request)
    {
        $sorter     = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';
        $users =
            DB::table('users')->select([
                'users.id',
                'users.en_name',
                'users.ar_name',
                'users.email',
                'users.phone',
                'users.badge_id',
                'users.active',
                'users.image',
                DB::raw("COALESCE(COUNT(DISTINCT orders.id), 0) AS orders_count"),
                DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
                DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
                DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
                'users.created_at',
                'users.updated_at',
            ])
                ->leftJoin('companies', 'users.company_id', '=', 'companies.id')
                ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
                ->where('users.company_id', company()->company_id)
                ->orderBy($sorter, $direction)
                ->groupBy('users.id');

        $search = Input::get('search');

        $active = $state == 'active' ? 1 : 0;

        $users = $users->where('users.active', $active)->where(function($q) use($search) {
            $q->where('users.en_name','like','%'.$search.'%');
            $q->orWhere('users.ar_name','like','%'.$search.'%');
            $q->orWhere('users.email','like','%'.$search.'%');
            $q->orWhere('users.phone','like','%'.$search.'%');
            $q->orWhere('users.badge_id','like','%'.$search.'%');
        })->paginate(5);

        return view('company.users.index', compact('users','search'));
    }


    public function create()
    {
        $subs = SubCompany::where('parent_id', company()->company_id)->get();
        $house_types = HouseType::where('company_id', company()->company_id)->get();
        return view('company.users.single', compact('subs','house_types'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'sub_company_id' => 'required|exists:sub_companies,id,parent_id,'.company()->company_id,
            'badge_id' => [
                'required',
                Rule::unique('users')->where(function ($query) use($request) {
                    return $query->where('badge_id', $request->badge_id)
                        ->where('company_id', company()->company_id);
                }),
            ],
            'en_name' => 'required',
            'ar_name' => 'required',
            'email' => 'required|unique:users,email,NULL,id,company_id,'.company()->company_id,
            'phone' => 'required|unique:users,phone,NULL,id,company_id,'.company()->company_id,
            'password' => 'required|confirmed',
            'image' => 'sometimes|image',
            'camp' => 'required',
            'street' => 'required',
            'plot_no' => 'required',
            'block_no' => 'required',
            'building_no' => 'required',
            'apartment_no' => 'required',
            'house_type' => 'required'
        ]);

        $badge_check = User::where('badge_id', $request->badge_id)->where('company_id',company()->company_id)->first();

        if($badge_check) {
            return back()->with('error', 'Sorry,this Badge ID already exists');
        }

        $user = new User();
        $user->jwt            = str_random(25);
        $user->company_id     = company()->company_id;
        $user->sub_company_id = $request->sub_company_id;
        $user->badge_id       = $request->badge_id;
        $user->en_name        = $request->en_name;
        $user->ar_name        = $request->ar_name;
        $user->email          = $request->email;
        $user->phone          = $request->phone;
        $user->camp           = $request->camp;
        $user->street         = $request->street;
        $user->plot_no        = $request->plot_no;
        $user->block_no       = $request->block_no;
        $user->building_no    = $request->building_no;
        $user->apartment_no   = $request->apartment_no;
        $user->house_type     = $request->house_type;
        $user->lat            = 0;
        $user->lng            = 0;
        if($request->password) $user->password = Hash::make($request->password);
        if($request->image)
        {
            $name = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/companies/users/',$name);
            $user->image = $name;
        }
        $user->save();

        return redirect('/company/users/active')->with('success', 'User created successfully');
    }


    public function show($id, Request $request)
    {
        $request->merge(['user_id' => $id]);
        $this->validate($request, [
            'user_id' => 'required|exists:users,id,company_id,'.company()->company_id
        ]);

        $user = User::find($id);

        return view('company.users.show', compact('user'));
    }


    public function edit($id, Request $request)
    {
        $request->merge(['user_id' => $id]);
        $this->validate($request, [
            'user_id' => 'required|exists:users,id,company_id,'.company()->company_id
        ]);

        $subs = SubCompany::where('parent_id', company()->company_id)->get();
        $user = User::find($id);
        $house_types = HouseType::where('company_id', company()->company_id)->get();

        return view('company.users.single', compact('user','subs','house_types'));
    }


    public function update(Request $request)
    {
        $this->validate($request, [
            'sub_company_id' => 'required|exists:sub_companies,id,parent_id,'.company()->company_id,
            'user_id' => 'required|exists:users,id,company_id,'.company()->company_id,
            'en_name' => 'required',
            'ar_name' => 'required',
            'email' => 'required|unique:users,email,'.$request->user_id,
            'phone' => 'required|unique:users,phone,'.$request->user_id,
            'password' => 'sometimes|confirmed',
            'image' => 'sometimes|image',
            'camp' => 'required',
            'street' => 'required',
            'plot_no' => 'required',
            'block_no' => 'required',
            'building_no' => 'required',
            'apartment_no' => 'required',
            'house_type' => 'required'
        ]);

        $badge_check = User::where('badge_id', $request->badge_id)->where('company_id',company()->company_id)->where('id','!=',$request->user_id)->first();

        if($badge_check)
        {
            return back()->with('error', 'Sorry,this Badge ID already exists');
        }

        $user = User::where('id', $request->user_id)->first();
        $user->company_id = company()->company_id;
        $user->sub_company_id = $request->sub_company_id;
	$user->badge_id = $request->badge_id;
        $user->en_name = $request->en_name;
        $user->ar_name = $request->ar_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->camp = $request->camp;
        $user->street = $request->street;
        $user->plot_no = $request->plot_no;
        $user->block_no = $request->block_no;
        $user->building_no = $request->building_no;
        $user->apartment_no = $request->apartment_no;
        $user->house_type = $request->house_type;
        if($request->password) $user->password = Hash::make($request->password);
        if($request->image)
        {
            if($user->image != 'default_user.png') unlink(base_path().'/public/companies/users/'.$user->image);
            $name = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/companies/users/',$name);
            $user->image = $name;
        }
        $user->save();

        if($user->active == 1) return redirect('/company/users/active')->with('success', 'User updated successfully');
        else return redirect('/company/users/suspended')->with('success', 'User updated successfully');
    }


    public function change_state(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id,company_id,'.company()->company_id
        ]);

        $user = User::where('id', $request->user_id)->first();
            if($user->active == 1) $user->active = 0;
            else $user->active = 1;
        $user->save();

        return back()->with('success', 'User deleted successfully');
    }


    public function change_password(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id,company_id,'.company()->company_id,
            'password' => 'required|confirmed'
        ]);

        $user = User::where('id', $request->user_id)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'User password updated successfully');
    }


//    public function destroy(Request $request)
//    {
//        $this->validate($request,
//            [
//                'user_id' => 'required|exists:users,id,company_id,'.company()->company_id
//            ]
//        );
//
//        $user = User::where('id', $request->user_id)->first();
//        if($user->image != 'default_user.png') unlink(base_path().'/public/companies/users/'.$user->image);
//        $user->delete();
//
//        return back()->with('success', 'User deleted successfully');
//    }


    public function excel_view()
    {
        return view('company.users.upload');
    }


    public function excel_upload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xlsx,xls,csv,tsv,ods,slk,xml'
        ]);

        $file = $request->file('file')->store('company/users/excels');

        $headings = (new HeadingRowImport)->toArray($file);

        $headingsReference = [
            "sub_company_id",
            "badge_id",
            "en_name",
            "ar_name",
            "email",
            "phone",
            "password",
            "camp",
            "street",
            "plot_no",
            "block_no",
            "building_no",
            "apartment_no",
            "house_type"
        ];

        if($headings[0][0] !== $headingsReference) {
            Storage::delete($file);
            return back()->with('error', 'Empty File, Or Invalid File Headings');
        }

        $import = new UsersImport();
        $import->import($file);

        Storage::delete($file);

        if(count($import->failures()) == 0) {
            return redirect('/company/users/active')->with('success', 'Users uploaded successfully');
        }

        $rows = [];
        foreach ($import->failures() as $failure) {
          $rows[] = $failure->row();
        }
        return back()->with('failures', $import->failures()->toArray())->with('rows', $rows);
    }


    public function images_view()
    {
        return view('company.users.upload_images');
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
            $tmp_dir = base_path('/public/companies/'.company()->company_id.'_tmp_images');

            try
            {
                $zip->open($request->file);
                $zip->extractTo($tmp_dir);

                $images = array_diff(scandir($tmp_dir),['.','..']);

                foreach($images as $image)
                {
                    $explode = explode('.',$image);

                    $user = User::where('company_id', company()->company_id)->where('badge_id', $explode[0])->first();

                    if($user)
                    {
                        $name = unique_file($image);

                        File::copy($tmp_dir.'/'.$image,base_path().'/public/companies/users/'.$name);
                        File::delete($tmp_dir.'/'.$image);

                        if($user->image != 'default_user.png') $old_image = $user->image;
                        $user->image = $name;
                        $user->save();

                        if(isset($old_image)) unlink(base_path().'/public/companies/users/'.$old_image);
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

        return redirect('/company/users/active')->with('success', 'Images uploaded & set successfully');
    }


    public function orders_request($user_id, Request $request)
    {
        $request->merge(['user_id' => $user_id]);
        $this->validate($request,
            [
                'user_id' => 'required|exists:users,id,company_id,'.company()->company_id
            ]
        );

        $user = User::where('company_id', company()->company_id)->where('id', $user_id)->select('id','en_name')->first();
        return view('company.users.orders_info_request', compact('user_id','user'));
    }


    public function orders_show(Request $request)
    {

        if($request->from && $request->to)
        {
            $this->validate($request,
                [
                    'from' => 'required|date',
                    'to' => 'required|date'
                ],
                [
                    'from.required' => 'Please choose a date to start from',
                    'from.date' => 'Please choose a valid date to start from',
                    'to.required' => 'Please choose a date to end with',
                    'to.date' => 'Please choose a valid date to end with',
                ]
            );
            $orders = Order::where('company_id', company()->company_id)->where('user_id', $request->user_id)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->get();
            // $orders[] = collect(['total' => $orders->sum('order_total')]);

            $user = User::where('id', $request->user_id)->select('id','en_name as name')->first();
            $from = $request->from;
            $to = $request->to;

            return view('company.users.orders_info_show', compact('orders','user','from','to'));
        }else{
            $orders = Order::where('company_id', company()->company_id)->where('user_id', $request->user_id)->get();
//            $orders[] = collect(['total' => $orders->sum('order_total')]);
            $user = User::where('id', $request->user_id)->select('id','en_name as name')->first();

            return view('company.users.orders_info_show', compact('orders','user'));
        }
    }


    public function orders_export(Request $request)
    {
        $this->validate($request,
            [
                'user_id' => 'required|exists:users,id,company_id,'.company()->company_id,
                'from' => 'required|date',
                'to' => 'required|date'
            ]
        );


        $orders = new Collection();
        $get_orders = Order::where('company_id', company()->company_id)->where('user_id', $request->user_id)->where('created_at','>=',$request->from)->where('created_at','<=',$request->to)->get();

        foreach($get_orders as $order)
        {
            if($order->type == 'urgent') $type = 'Urgent';
            elseif($order->type == 'scheduled') $type = 'Scheduled';
            else $type = 'Re-Scheduled';

            $collect['Category'] = $order->category->parent->en_name . ' - ' . $order->category->en_name;
            $collect['Date'] = $order->created_at->toDateTimeString();
            $collect['Type'] = $type;
            $collect['Cost'] = $order->order_total;

            $orders = $orders->push($collect);
        }


        $orders[] = collect(['Category' => '-','Date' => '-','Type' => '-','Cost' => '-','Total' => $orders->sum('Cost')]);

        $orders = $orders->toArray();
        $user = User::where('id', $request->user_id)->select('en_name')->first();
        $from = $request->from;
        $to = $request->to;
        $p_name = str_replace(' ','-',$user->en_name);

        $filename = 'qareeb_user_'.$p_name.'_'.$from.'_'.$to.'_orders_invoice.xls';


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


    public function order_create($user_id, Request $request)
    {

        $request->merge(['user_id' => $user_id]);

        $this->validate($request,
            [
                'user_id' => 'required|exists:users,id,company_id,'.company()->company_id
            ]
        );

        $subs = CompanySubscription::where('company_id', company()->company_id)->first();
        if($subs == NULL) return back()->with('error','You have not assigned to categories subscriptions yet,contact customer support for more info !');

        $types['urgent'] = 'Urgent';
        $types['scheduled'] = 'Scheduled';
        $types['emergency'] = 'Emergency';
        $company = User::where('id', $user_id)->with(['company' => function($q){
         $q->with(['orderProcessType']);
        }])->first();
        $companyProcessType = [];
        $companyProcessType ['order_process_id'] = !empty(collect($company)->toArray()) && !empty(collect($company)->toArray()['company']) && !empty(collect($company)->toArray()['company']['order_process_id'])? collect($company)->toArray()['company']['order_process_id']: '';
        $companyProcessType ['orderProcessName'] = !empty(collect($company)->toArray())&& !empty(collect($company)->toArray()['company']) && !empty(collect($company)->toArray()['company']['order_process_type']) ? collect($company)->toArray()['company']['order_process_type']['name']: '' ;
        $subs = CompanySubscription::where('company_id', company()->company_id)->first()->subs;
        $cat_ids = Category::whereIn('id', unserialize($subs))->pluck('parent_id');
        $cats = Category::whereIn('id', $cat_ids)->select('id','en_name')->get();
        $techRole = 1;
        if( !empty($companyProcessType ['order_process_id']) && $companyProcessType ['order_process_id'] == 2 ){
             $techRole = 1;
         }else if( !empty($companyProcessType ['order_process_id']) && $companyProcessType ['order_process_id'] == 1 ){
            $techRole  = 2;
         }
        $user = User::where('id', $request->user_id)->select('id','en_name', 'company_id')->first();

        $providers = Collaboration::where('company_id', $user->company_id)->pluck('provider_id');
        $technicians = Technician::whereIn('provider_id', $providers)->where('technician_role_id', $techRole)->get();

        return view('company.users.order_single', compact('user_id','types','cats','user', 'techRole', 'technicians', 'companyProcessType'));
    }


    public function order_store(Request $request)
    {
        if(!empty($request->type) && $request->type != 'scheduled'){
           $valid =  [
                'tech_id' => 'required',
            ];
            $message = [
                'tech_id.required' => 'Technician is required',
            ];
        }else{
            $valid =  [];
            $message = [];
        }

        $this->validate($request,
        $valid,
            $message
        );
        $status = '';
        if($request->roleName == 1){
            $status   =  'Assessor Supervisor selected';
        }else{
            $status   = 'Technician selected';
        }
        $user = User::where('id', $request->user_id)->first();
        $order = new Order();
            if($request->smo) $order->smo = $request->smo;
            $order->user_id = $request->user_id;
            $order->company_id = company()->company_id;
            $order->type = $request->type;
            $order->code = rand(1000, 9999);
            $order->cat_id = $request->cat_id;
            $order->sub_cat_id = $request->cat_id;
            $order->service_type = 3;
            $order->tech_id = $request->tech_id;
            $order->provider_id = !empty($request->tech_id)? Technician::where('id', $request->tech_id)->first()->provider_id: null;
            if ($request->type == 'urgent')
            {
                $order->order_total = !empty(ProviderCategoryFee::where('provider_id', $order->provider_id)->where('cat_id', $request->cat_id)->select('urgent_fee')->first()->urgent_fee)? ProviderCategoryFee::where('provider_id', $order->provider_id)->where('cat_id', $request->cat_id)->select('urgent_fee')->first()->urgent_fee : 0;
                if($request->roleName != 1){
                Technician::where('id', $order->tech_id)->update(['busy' => 1]);
                }

            } else
            {
                $order->order_total = !empty($request->tech_id)?ProviderCategoryFee::where('provider_id', $order->provider_id)->where('cat_id', $request->cat_id)->select('scheduled_fee')->first()->scheduled_fee: 0;
                $order->scheduled_at = $request->date .' '. $request->time;
            }
        $order->save();

        OrderAddress::create([
            'order_id'      => $order->id,
            'lat'           => $user->lat,
            'lng'           => $user->lng,
            'name'          => 'name',
            'is_default '   => $user->is_default ,
            'city'          => $user->city,
            'camp'          => $user->camp,
            'street'        => $user->street,
            'plot_no'       => $user->plot_no,
            'block_no'      => $user->block_no,
            'building_no'   => $user->building_no,
            'apartment_no'  => $user->apartment_no,
            'house_type'    => $user->house_type,
        ]);

        OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'Service request',
            'date' => Carbon::now(),
            'technicain_id' => $request->tech_id
        ]);


        OrderTracking::create([
            'order_id' => $order->id,
            'status' => $status,
            'date' => Carbon::now(),
            'technicain_id' => $request->tech_id
        ]);

        if(!empty($request->tech_id)){

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
            PushNotify::tech_send($token, $ar_text, $en_text, 'order', $order->id , 'en');
        }



        $order_details = new OrderUserDetail();
        $order_details->order_id = $order->id;
        $order_details->place = $request->place;
        $order_details->part = $request->part;
        $order_details->desc = $request->desc;
        if ($request->images)
        {
            $names = [];
            foreach ($request->images as $image)
            {
                $name = unique_file($image->getClientOriginalName());
                $image->move(base_path() . '/public/orders/', $name);

                $names[] = $name;
            }
            $order_details->images = serialize($names);
        }
        $order_details->save();

        event(new \App\Events\Order\NewOrderEvent($order));

        return redirect('/company/users/active')->with('success', 'Order has been scheduled successfully !');
    }

    public function excel_export($status)
    {
        if($status == 'active'){
            $status = 1;
        }else{
            $status = 0;
        }

        $users = User::where('company_id', company()->company_id)->where('active', $status)
            ->select('badge_id as Badge ID', 'sub_company_id', 'en_name as English Name', 'ar_name as Arabic Name',
                'email as Email', 'phone as Phone')->get();

        foreach($users as $user){
            $user['Sub Company'] = $user->sub_company->en_name;

            unset($user->sub_company_id, $user->sub_company);
        }

        $users = $users->toArray();
        $filename = 'qareeb_users_data.xls';

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $heads = false;
        foreach($users as $user)
        {
            if($heads == false)
            {
                echo implode("\t", array_keys($user)) . "\n";
                $heads = true;
            }
            {
                echo implode("\t", array_values($user)) . "\n";
            }
        }

        die();

    }
}
