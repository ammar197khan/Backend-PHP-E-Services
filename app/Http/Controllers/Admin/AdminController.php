<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::where('id','!=',1)->paginate(50);
        return view('admin.admins.index', compact('admins'));
    }


    public function search()
    {
        $search = Input::get('search');
        $admins = Admin::where(function ($q) use ($search)
        {
            $q->where('name','like','%'.$search.'%');
            $q->orWhere('email','like','%'.$search.'%');
            $q->orWhere('phone','like','%'.$search.'%');
        }
        )->paginate(50);

        return view('admin.admins.search', compact('admins','search'));
    }


    public function create()
    {
        $permissions = Permission::where('guard_name','admin')->get();
        $data = [
            'Dashboard'     => [$permissions[0]],
            'Admin'         => [$permissions[36],$permissions[37],$permissions[38],$permissions[39],$permissions[40],$permissions[41]],
            'Address'       => [$permissions[2],$permissions[1],$permissions[3],$permissions[4]],
            'Categories'    => [$permissions[5],$permissions[6],$permissions[7],$permissions[8],$permissions[9],$permissions[10]],
            'Providers'     => [$permissions[11],$permissions[12],$permissions[13],$permissions[14],$permissions[15],$permissions[16],$permissions[17],$permissions[18]],
            'Companies'     => [$permissions[19],$permissions[20],$permissions[21],$permissions[22],$permissions[23],$permissions[24],$permissions[25],$permissions[26]],
            'Collaboration' => [$permissions[27],$permissions[28],$permissions[29],$permissions[30]],
            'Settings'      => [$permissions[31],$permissions[32],$permissions[33]],
            'Bills'         => [$permissions[34],$permissions[35]],
        ];

        return view('admin.admins.single', compact('data','permissions'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required',
                'email' => 'required|unique:admins,email',
                'phone' => 'required|unique:admins,phone',
                'image' => 'sometimes|image',
                'password' => 'required|confirmed'
            ],
            [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists,please choose another one',
                'phone.required' => 'Phone is required',
                'phone.unique' => 'Phone already exists,please choose another one',
                'image.image' => 'Image is invalid',
                'password.required' => 'Password is required',
                'password.confirmed' => 'Password does not match'
            ]
        );

        $admin = new Admin();
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            $admin->password = Hash::make($request->password);
            if($request->image)
            {
                $name = unique_file($request->image->getClientOriginalName());
                $request->image->move(base_path().'/public/qareeb_admins',$name);
                $admin->image = $name;
            }
        $admin->save();

        $admin->syncPermissions($request->check_list);

        return redirect('/admin/admins/index')->with('success', 'Admin created successfully!');
    }


    public function show($admin_id, Request $request)
    {
        $request->merge(['admin_id' => $admin_id]);

        $this->validate($request,
            [
                'admin_id' => 'required|exists:admins,id'
            ]
        );

        $admin = Admin::find($request->admin_id);

        $permissions = $admin->permissions;

        $get_permission_admin = [];
        foreach ($permissions as $permission)
        {
            $explode = explode(' ', $permission->name);
            if(strpos($permission->name,$explode[1]))
            {
                $key = ucfirst($explode[1]);
                $get_permission_admin[$key][] = ucfirst($explode[0]);
//                $get_permission_admin[$key][] = ucfirst($permission->name);
            }
        }

        return view('admin.admins.show', compact('admin','permissions','get_permission_admin'));
    }


    public function edit($admin_id, Request $request)
    {
        $request->merge(['admin_id' => $admin_id]);

        $this->validate($request,
            [
                'admin_id' => 'required|exists:admins,id'
            ]
        );

        $admin = Admin::find($request->admin_id);

        $permissions = Permission::where('guard_name','admin')->get();
        $data = [
            'Dashboard' => [$permissions[0]],
            'Admin' => [$permissions[36],$permissions[37],$permissions[38],$permissions[39],$permissions[40],$permissions[41]],
            'Address' => [$permissions[2],$permissions[1],$permissions[3],$permissions[4]],
            'Categories' => [$permissions[5],$permissions[6],$permissions[7],$permissions[8],$permissions[9],$permissions[10]],
            'Providers' => [$permissions[11],$permissions[12],$permissions[13],$permissions[14],$permissions[15],$permissions[16],$permissions[17],$permissions[18]],
            'Companies' => [$permissions[19],$permissions[20],$permissions[21],$permissions[22],$permissions[23],$permissions[24],$permissions[25],$permissions[26]],
            'Collaboration' => [$permissions[27],$permissions[28],$permissions[29],$permissions[30]],
            'Settings' => [$permissions[31],$permissions[32],$permissions[33]],
            'Bills' => [$permissions[34],$permissions[35]],
        ];

        return view('admin.admins.single', compact('admin','permissions','data'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'admin_id' => 'required|exists:admins,id',
                'name' => 'required',
                'email' => 'required|unique:admins,email,'.$request->admin_id,
                'phone' => 'required|unique:admins,phone,'.$request->admin_id,
                'image' => 'sometimes|image',
                'password' => 'sometimes|confirmed'
            ],
            [
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

        $admin = Admin::find($request->admin_id);
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            $admin->vat = $request->vat;
            $admin->vat_registration = $request->vat_registration;
            if($request->image)
            {
                $name = unique_file($request->image->getClientOriginalName());
                $request->image->move(base_path().'/public/qareeb_admins',$name);
                $admin->image = $name;
            }
            if($request->password)
            {
                $admin->password = Hash::make($request->password);
            }
        $admin->save();

        $admin->syncPermissions($request->check_list);

        return redirect('/admin/admins/index')->with('success', 'Admin updated successfully!');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'admin_id' => 'required|exists:admins,id'
            ]
        );

        Admin::where('id', $request->admin_id)->delete();

        return back()->with('success', 'Admin deleted successfully !');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'admin_id' => 'required|exists:admins,id',
                'state' => 'required|in:0,1'
            ]
        );

        Admin::where('id', $request->admin_id)->update(['active' => $request->state]);

        if($request->state == 1 ) return back()->with('success', 'Admin activated successfully !');
        else return back()->with('success', 'Admin suspended successfully !');
    }
}
