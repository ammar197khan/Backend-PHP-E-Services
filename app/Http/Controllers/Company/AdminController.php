<?php

namespace App\Http\Controllers\Company;

use App\Models\CompanyAdmin;
use App\Models\SubCompany;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = CompanyAdmin::where('company_id', company()->company_id)->paginate(50);

        return view('company.admins.index', compact('admins'));
    }

    public function profile()
    {
        $admin = company();
        return view('company.profile.admin.index', compact('admin'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $admins = CompanyAdmin::where('company_id', company()->company_id)->where(function ($q) use ($search)
        {
            $q->where('name','like','%'.$search.'%');
            $q->orWhere('email','like','%'.$search.'%');
            $q->orWhere('phone','like','%'.$search.'%');
        }
        )->paginate(50);

        return view('company.admins.index', compact('admins','search'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name','company')->get();
        $sub_companies = SubCompany::where('parent_id', company()->company_id)->get();
        $sub_comp_id = "";

        $data = [
            'Dashboard' => [$permissions[0]],
            'Admin' => [$permissions[25],$permissions[26],$permissions[27],$permissions[28],$permissions[29],$permissions[30]],
            'Info' => [$permissions[1],$permissions[2]],
            'Sub Company' => [$permissions[3],$permissions[4],$permissions[5],$permissions[6],$permissions[7]],
            'Collaboration' => [$permissions[8],$permissions[9],$permissions[10],$permissions[11]],
            'User' => [$permissions[12],$permissions[13],$permissions[14],$permissions[15],$permissions[16],$permissions[17],$permissions[18],$permissions[19]],
            'Order' => [$permissions[20],$permissions[21]],
            'Item Request' => [$permissions[22],$permissions[23],$permissions[24]],
        ];

        return view('company.admins.single', compact('data','permissions','sub_companies','sub_comp_id'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'sub_company_id' => 'required',
                'badge_id' => 'required|unique:company_admins',
                'name' => 'required',
                'email' => 'required|unique:company_admins,email',
                'phone' => 'required|unique:company_admins,phone',
                'image' => 'sometimes|image',
                'username' => 'required|unique:company_admins,username',
                'password' => 'required|confirmed'
            ],
            [
                'sub_company_id.required' => 'Sub Company ID is required',
                'badge_id.required' => 'Badge ID is required',
                'badge_id.unique' => 'Badge ID already exist',
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
        //--------------------------------------------//
        $admin_type = 'admin';
        if($request->sub_company_id != ""){
            $admin_type = 'subAdmin';
        }
        //----------upload logo image-------------//
        $image = "";
        if($request->image) {
            $image = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/companies/admins', $image);
        }
        $admin = CompanyAdmin::create([
            'company_id' => company()->company_id,
            'sub_company_id'  => $request->sub_company_id,
            'badge_id'   => $request->badge_id,
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'image'      => $image
        ]);

        $admin->syncPermissions($request->check_list);

        return redirect()->route('company.admins.index')->with('success', 'Admin created successfully!');
    }

    public function view($id, Request $request)
    {
        $request->merge(['admin_id' => $id]);

        $this->validate($request,
            [
                'admin_id' => 'required|exists:company_admins,id,company_id,'.company()->company_id
            ]
        );

        $admin = CompanyAdmin::find($id);

        return view('company.admins.show', compact('admin'));
    }

    public function edit($id, Request $request)
    {
        $request->merge(['admin_id' => $id]);
        $this->validate($request,
            [
                'admin_id' => 'required|exists:company_admins,id,company_id,'.company()->company_id
            ]
        );

        $admin = CompanyAdmin::find($id);

        $permissions = Permission::where('guard_name','company')->get();
        $sub_companies = SubCompany::where('parent_id', company()->company_id)->get();
        $selected_sub_comp = SubCompany::where('id', $admin->sub_company_id)->first();
        $sub_comp_name = "";
        $sub_comp_id = "";
        if($selected_sub_comp && $selected_sub_comp != ""){ $sub_comp_name = $selected_sub_comp->en_name; $sub_comp_id = $admin->sub_company_id; }
        $data = [
            'Dashboard' => [$permissions[0]],
            'Admin' => [$permissions[25],$permissions[26],$permissions[27],$permissions[28],$permissions[29],$permissions[30]],
            'Info' => [$permissions[1],$permissions[2]],
            'Sub Company' => [$permissions[3],$permissions[4],$permissions[5],$permissions[6],$permissions[7]],
            'Collaboration' => [$permissions[8],$permissions[9],$permissions[10],$permissions[11]],
            'User' => [$permissions[12],$permissions[13],$permissions[14],$permissions[15],$permissions[16],$permissions[17],$permissions[18],$permissions[19]],
            'Order' => [$permissions[20],$permissions[21]],
            'Item Request' => [$permissions[22],$permissions[23],$permissions[24]],
            'SLA' => [$permissions[50], $permissions[51]],
        ];

        return view('company.admins.single', compact('admin', 'data','permissions','sub_companies','sub_comp_name','sub_comp_id'));
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'admin_id' => 'required|exists:company_admins,id',
                'badge_id' => 'required',
                'name' => 'required',
                'email' => 'required|unique:company_admins,email,'.$request->admin_id,
                'phone' => 'required|unique:company_admins,phone,'.$request->admin_id,
                'image' => 'sometimes|image',
                'password' => 'sometimes|confirmed',
                'username' => [
                    'required',
                    Rule::unique('company_admins')->where(function($query) use($request){
                        return $query->where('username', $request->username)->where('company_id', company()->company_id)
                            ->where('id', '!=', $request->admin_id);
                    })
                ],
            ],
            [
                'badge_id.required' => 'Badge ID le is required',
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

        $badge_check = CompanyAdmin::where('badge_id', $request->badge_id)->where('company_id',company()->company_id)->where('id','!=',$request->admin_id)->first();

        if($badge_check)
        {
            return back()->with('error', 'Sorry,this Badge ID already exists');
        }
        $admin = CompanyAdmin::find($request->admin_id);
        $sub_company_id = null;
        $admin_type = 'admin';
        if($request->sub_company_id != ""){
            $admin_type = 'subAdmin';
            $sub_company_id = $request->sub_company_id;
        }
        $admin->update([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'type' => $admin_type,
            'sub_company_id' => $sub_company_id,
            'image' => $request->image
        ]);
        if($request->image) {
            $image = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/companies/admins', $image);
            $admin->image = $image;
            $admin->save();
        }
        if($request->password)
        {
            $admin->password = Hash::make($request->password);
            $admin->save();
        }

        $admin->syncPermissions($request->check_list);

        return back()->with('success', 'Admin updated successfully!');
    }

    public function update_profile(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required',
                'email' => 'required|unique:company_admins,email,'.company()->id,
                'phone' => 'required|unique:company_admins,phone,'.company()->id,
            ]
        );

        company()->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        return back()->with('success', 'Info changed successfully !');
    }

    public function update_profile_password(Request $request)
    {
        $this->validate($request,
            [
                'password' => 'required|min:6|confirmed',
            ]
        );

        company()->update([
            'password' => Hash::make($request->password)
        ]);;

        return back()->with('success', 'Password changed successfully !');
    }

    public function destroy($id, Request $request)
    {
        $this->validate($request,
            [
                'admin_id' => 'required|exists:company_admins,id'
            ]
        );

        CompanyAdmin::where('id', $id)->delete();

        return back()->with('success', 'Admin deleted successfully !');
    }

    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'admin_id' => 'required|exists:company_admins,id',
                'state' => 'required|in:0,1'
            ]
        );

        CompanyAdmin::where('id', $request->admin_id)->update(['active' => $request->state]);

        if($request->state == 1 ) return back()->with('success', 'Admin activated successfully !');
        else return back()->with('success', 'Admin suspended successfully !');
    }
}
