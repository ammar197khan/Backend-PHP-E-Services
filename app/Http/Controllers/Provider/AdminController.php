<?php

namespace App\Http\Controllers\Provider;

use App\Models\ProviderAdmin;
use App\Models\Technician;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $admins = ProviderAdmin::where('provider_id', provider()->provider_id)->paginate(50);

        return view('provider.admins.index', compact('admins'));
    }


    public function search()
    {
        $search = Input::get('search');
        $admins = ProviderAdmin::where('provider_id', provider()->provider_id)->where(function ($q) use ($search)
        {
            $q->where('name','like','%'.$search.'%');
            $q->orWhere('email','like','%'.$search.'%');
            $q->orWhere('phone','like','%'.$search.'%');
        }
        )->paginate(50);

        return view('provider.admins.search', compact('admins','search'));
    }


    public function create()
    {
        $permissions = Permission::where('guard_name','provider')->get();

        $data = [
            'Dashboard' => [$permissions[0]],
            'Admin' => [$permissions[33],$permissions[34],$permissions[35],$permissions[36],$permissions[37],$permissions[38]],
            'Info' => [$permissions[1],$permissions[2]],
            'Collaboration' => [$permissions[3],$permissions[4],$permissions[5],$permissions[6],$permissions[7]],
            'Orders' => [$permissions[8]],
            'Warehouse' => [$permissions[9],$permissions[10],$permissions[11],$permissions[12],$permissions[13],$permissions[14],$permissions[15],$permissions[16],$permissions[17]],
            'Warehouse Request' => [$permissions[18],$permissions[19]],
            'Technician' => [$permissions[20],$permissions[21],$permissions[22],$permissions[23],$permissions[24],$permissions[25],$permissions[26],$permissions[27],$permissions[28]],
            'rotation' => [$permissions[29],$permissions[30],$permissions[31],$permissions[32]],
            ];

        return view('provider.admins.single', compact('data','permissions'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'badge_id' => [
                    'required',
                    Rule::unique('provider_admins')->where(function($query) use($request){
                        return $query->where('badge_id', $request->badge_id)->where('provider_id', provider()->provider_id)
                            ->where('id', '!=', $request->admin_id);
                    })
                ],
                'name' => 'required',
                'email' => 'required|unique:provider_admins,email',
                'phone' => [
                    'required',
                    Rule::unique('provider_admins')->where(function($query) use($request){
                        return $query->where('phone', $request->phone)->where('provider_id', provider()->provider_id)
                            ->where('id', '!=', $request->admin_id);
                    })
                ],
                'image' => 'sometimes|image',
                'username' => 'required|unique:provider_admins,username',
                'password' => 'required|confirmed'
            ],
            [
                'badge_id.required' => 'Badge ID is required',
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists,please choose another one',
                'phone.required' => 'Phone is required',
                'phone.unique' => 'Phone already exists,please choose another one',
                'image.image' => 'Image is invalid',
                'username.required' => 'Username is required',
                'username.unique' => 'Username already exists,please choose another one',
                'password.required' => 'Password is required',
                'password.confirmed' => 'Password does not match'
            ]
        );

    //    $ProviderAdminPhoneExist =  ProviderAdmin::where('provider_id', provider()->provider_id)->where('phone', $request->phone)->first();
    //    if($ProviderAdminPhoneExist){
    //     return back()->with('error', 'Sorry,this Phone number already exists');
    //    }
    //     // $badge_check = Technician::where('badge_id', $request->badge_id)->where('provider_id',provider()->provider_id)->first();
    //     $badge_check = ProviderAdmin::where('badge_id', $request->badge_id)->where('provider_id',provider()->provider_id)->first();
    //     if($badge_check)
    //     {
    //         return back()->with('error', 'Sorry,this Badge ID already exists');
    //     }

        $admin = new ProviderAdmin();
            $admin->provider_id = provider()->provider_id;
            $admin->badge_id = $request->badge_id;
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            $admin->username = $request->username;
            $admin->password = Hash::make($request->password);
            if($request->image)
            {

		$image =$request->file('image');
		$name = unique_file($image->getClientOriginalName());
                $request->image->move(base_path().'/public/providers/admins',$name);
                $admin->image = $name;
            }
        $admin->save();

        $admin->syncPermissions($request->check_list);

        return redirect('/provider/admins/index')->with('success', 'Admin created successfully!');
    }


    public function show($admin_id, Request $request)
    {
        $request->merge(['admin_id' => $admin_id]);

        $this->validate($request,
            [
                'admin_id' => 'required|exists:provider_admins,id,provider_id,'.provider()->provider_id
            ]
        );

        $admin = ProviderAdmin::find($request->admin_id);

        return view('provider.admins.show', compact('admin'));
    }


    public function edit($admin_id, Request $request)
    {
        $request->merge(['admin_id' => $admin_id]);

        $this->validate($request,
            [
                'admin_id' => 'required|exists:provider_admins,id'
            ]
        );

        $admin = ProviderAdmin::find($admin_id);


        $permissions = Permission::where('guard_name','provider')->get();

        $data = [
            'Dashboard' => [$permissions[0]],
            'Admin' => [$permissions[33],$permissions[34],$permissions[35],$permissions[36],$permissions[37],$permissions[38]],
            'Info' => [$permissions[1],$permissions[2]],
            'Collaboration' => [$permissions[3],$permissions[4],$permissions[5],$permissions[6],$permissions[7]],
            'Orders' => [$permissions[8]],
            'Warehouse' => [$permissions[9],$permissions[10],$permissions[11],$permissions[12],$permissions[13],$permissions[14],$permissions[15],$permissions[16],$permissions[17]],
            'Warehouse Request' => [$permissions[18],$permissions[19]],
            'Technician' => [$permissions[20],$permissions[21],$permissions[22],$permissions[23],$permissions[24],$permissions[25],$permissions[26],$permissions[27],$permissions[28]],
            'rotation' => [$permissions[29],$permissions[30],$permissions[31],$permissions[32]],
        ];

        return view('provider.admins.single', compact('admin', 'data','permissions'));
    }


    public function update(Request $request)
    {

        $this->validate($request,
            [
                'admin_id' => 'required|exists:provider_admins,id',
                'badge_id' => 'required',
                'name' => 'required',
                'email' => 'required|unique:provider_admins,email,'.$request->admin_id,
                'phone' => [
                    'required',
                    Rule::unique('provider_admins')->where(function($query) use($request){
                        return $query->where('phone', $request->phone)->where('provider_id', provider()->provider_id)
                            ->where('id', '!=', $request->admin_id);
                    })
                ],
                'image' => 'sometimes|image',
                'password' => 'sometimes|confirmed',
                'username' => [
                    'required',
                    Rule::unique('provider_admins')->where(function($query) use($request){
                        return $query->where('username', $request->username)->where('provider_id', provider()->provider_id)
                            ->where('id', '!=', $request->admin_id);
                    })
                ],
            ],
            [
                'badge_id.required' => 'RoBadge _ID le is required',
                'en_name.required' => 'English Name is required',
                'ar_name.required' => 'Arabic Name is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists,please choose another one',
                'phone.required' => 'Phone is required',
                'phone.unique' => 'Phone already exists,please choose another one',
                'image.image' => 'Image is invalid',
                'password.confirmed' => 'Password does not match'
            ]
        );

        $badge_check = ProviderAdmin::where('badge_id', $request->badge_id)->where('provider_id',provider()->provider_id)->where('id','!=',$request->admin_id)->first();

        if($badge_check)
        {
            return back()->with('error', 'Sorry,this Badge ID already exists');
        }

        $admin = ProviderAdmin::find($request->admin_id);
            $admin->username = $request->username;
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            if($request->image)
            {
                $name = unique_file($request->image->getClientOriginalName());
                $request->image->move(base_path().'/public/providers/admins/',$name);
                $admin->image = $name;
            }
            if($request->password)
            {
                $admin->password = Hash::make($request->password);
            }
        $admin->save();

        $admin->syncPermissions($request->check_list);

        return redirect('/provider/admins/index')->with('success', 'Admin updated successfully!');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'admin_id' => 'required|exists:provider_admins,id'
            ]
        );

        ProviderAdmin::where('id', $request->admin_id)->delete();

        return back()->with('success', 'Admin deleted successfully !');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'admin_id' => 'required|exists:provider_admins,id',
                'state' => 'required|in:0,1'
            ]
        );

        ProviderAdmin::where('id', $request->admin_id)->update(['active' => $request->state]);

        if($request->state == 1 ) return back()->with('success', 'Admin activated successfully !');
        else return back()->with('success', 'Admin suspended successfully !');
    }

    public function test()
    {
//
//        $p = Permission::create
//        (
//            [
//                'guard_name' => 'provider',
//                'name' => 'services_fees'
//            ]
//        );
//
//        $roles = Role::whereIn('name', ['provider_system_admin','provider_app_manager'])->get();
//
//        foreach($roles as $role)
//        {
//            $role->givePermissionTo($p->name);
//        }
//        dd('sdf');
//        $admins = ProviderAdmin::get();
//
//        foreach($admins as $admin)
//        {
//            $admin->syncRoles(['provider_'.$admin->role]);
//        }
//        dd('dd');
//        $roles = Role::where('guard_name','provider')->get();
//
//        foreach($roles as $role)
//        {
//            if($role->name == 'provider_owner')
//            {
//                $ps = ['statistics_general','statistics_financial','providers_observe','techs_observe','orders_observe','warehouse_observe','warehouse_requests_observe','rotations_observe'];
//                $role->givePermissionTo($ps);
//            }
//            if($role->name == 'provider_system_admin')
//            {
//                $ps = ['admins','statistics_general','statistics_financial','providers_observe','providers_operate','collaborations_observe','collaborations_operate','techs_observe','techs_file_upload','techs_operate','orders_observe','warehouse_observe','warehouse_operate','warehouse_file_upload','warehouse_requests_observe','warehouse_requests_operate','rotations_observe','rotations_operate'];
//                $role->givePermissionTo($ps);
//            }
//            if($role->name == 'provider_app_manager')
//            {
//                $ps = ['statistics_general','statistics_financial','providers_observe','providers_operate','collaborations_observe','collaborations_operate','techs_observe','techs_file_upload','techs_operate','orders_observe','warehouse_observe','warehouse_operate','warehouse_file_upload','warehouse_requests_observe','warehouse_requests_operate','rotations_observe','rotations_operate'];
//                $role->givePermissionTo($ps);
//            }
//            if($role->name == 'provider_techs_manager')
//            {
//                $ps = ['statistics_general','providers_observe','collaborations_observe','collaborations_operate','techs_observe','techs_file_upload','techs_operate','orders_observe','warehouse_observe','warehouse_operate','warehouse_file_upload','warehouse_requests_observe','warehouse_requests_operate','rotations_observe','rotations_operate'];
//                $role->givePermissionTo($ps);
//            }
//            if($role->name == 'provider_service_desk')
//            {
//                $ps = ['statistics_general','providers_observe','collaborations_observe','collaborations_operate','techs_observe','orders_observe','warehouse_observe','warehouse_requests_observe','rotations_observe'];
//                $role->givePermissionTo($ps);
//            }
//            if($role->name == 'provider_warehouse_desk')
//            {
//                $ps = ['statistics_general','providers_observe','collaborations_observe','collaborations_operate','techs_observe','orders_observe','warehouse_observe','warehouse_operate','warehouse_file_upload','warehouse_requests_observe','warehouse_requests_operate','rotations_observe'];
//                $role->givePermissionTo($ps);
//            }
//            if($role->name == 'provider_user')
//            {
//                $ps = ['statistics_general','providers_observe','collaborations_observe','techs_observe','orders_observe','warehouse_observe','warehouse_requests_observe','rotations_observe'];
//                $role->givePermissionTo($ps);
//            }
//        }
//
//        dd('sdfds');
////        $permissions['company_owner'] = ['General Statistics','Financial Statistics','Company Observe','Sub Companies Observe','Users Observe','Orders Observe','Items Requests Observe'];
////        $permissions['company_system_admin'] = ['Admins','General Statistics','Financial Statistics','Company Observe','Company Operate','Sub Companies Observe','Sub Companies Operate','Collaborations Observe','Collaborations Operate','Users Observe','Users Files Upload','Users Operate','Orders Observe','Items Requests Observe','ItemsRequests Operate'];
////        $permissions['company_app_manager'] = ['General Statistics','Financial Statistics','Company Observe','Company Operate','Sub Companies Observe','Collaborations Observe','Collaborations Operate','Users Observe','Users Operate','Users Files Upload','Orders Observe','Items Requests Observe','ItemsRequests Operate'];
////        $permissions['company_users_manager'] = ['General Statistics','Financial Statistics','Company Observe','Sub Companies Observe','Sub Companies Operate','Collaborations Observe','Collaborations Operate','Users Observe','Users Files Upload','Orders Observe','Items Requests Observe'];
//        $permissions['company_service_desk'] = ['General Statistics','Company Observe','Sub Companies Observe','Collaborations Observe','Collaborations Operate','Users Observe','Orders Observe','Items Requests Observe'];
//        $permissions['company_user'] = ['General Statistics','Company Observe','Sub Companies Observe','Collaborations Observe','Users Observe','Orders Observe','Items Requests Observe'];

    }
}
