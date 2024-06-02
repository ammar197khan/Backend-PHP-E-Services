<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Company;
use App\Models\Provider;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class IndividualController extends Controller
{

    public function user_index($state)
    {
        if($state == 'active')
        {
            $users = User::where('type', 'individual')->where('active' , 1)->paginate(50);
        }
        elseif($state == 'suspended')
        {
            $users = User::where('type', 'individual')->where('active' , 0)->paginate(50);
        }

        return view('admin.individuals.user_index', compact('users'));
    }

    public function user_create()
    {
        return view('admin.individuals.user_single');
    }

    public function user_store(Request $request)
    {
        $this->validate($request,
            [
                'ar_name'   => 'required',
                'en_name'   => 'required',
                'password'  => 'required|min:6|confirmed',
                'email'     => 'required|unique:users,email',
                'phone'     => 'required|unique:users,phone',
                'image'     => 'sometimes|image',
            ],
            [
                'password.required'  => 'Password is required',
                'password.min'       => 'Password must be 6 digits at least',
                'password.confirmed' => 'Password and its confirmation does not match',
                'ar_name.required'   => 'Arabic Name is required',
                'en_name.required'   => 'English Name is required',
                'email.required'     => 'Email is required',
                'email.unique'       => 'Email already exists,please choose another one',
                'phone.required'     => 'Phone is required',
                'phone.unique'       => 'Phone already exists,please choose another one',
                'image.image'        => 'Image is invalid',
            ]
        );

        $user = User::create(
            [
                'type' => 'individual',
                'jwt' => str_random(20),
                'active' => 1,
                'en_name' => $request->en_name,
                'ar_name' => $request->ar_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'city' => $request->city,
                'street' => $request->district,
                'building_no' => $request->home_no
            ]
        );

        if($request->image)
        {
            $image = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/individuals/', $image);
            $user->image = $image;
            $user->save();
        }

        return redirect('/admin/individuals/user/active')->with('success', 'User added successfully !');
    }

    public function user_update(Request $request)
    {
        $this->validate($request,
            [
                'ar_name'   => 'required',
                'en_name'   => 'required',
                'password'  => 'sometimes|confirmed',
                'email'     => 'required|unique:users,email,'.$request->user_id,
                'phone'     => 'required|unique:users,phone,'.$request->user_id,
                'image'     => 'sometimes|image',
            ],
            [
                'password.confirmed' => 'Password and its confirmation does not match',
                'ar_name.required'   => 'Arabic Name is required',
                'en_name.required'   => 'English Name is required',
                'email.required'     => 'Email is required',
                'email.unique'       => 'Email already exists,please choose another one',
                'phone.required'     => 'Phone is required',
                'phone.unique'       => 'Phone already exists,please choose another one',
                'image.image'        => 'Image is invalid',
            ]
        );

        $user = User::find($request->user_id)->update(
            [
                'en_name' => $request->en_name,
                'ar_name' => $request->ar_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'city' => $request->city,
                'street' => $request->district,
                'building_no' => $request->home_no
            ]
        );

        if($request->password){
            $user->password = Hash::make($request->password);
            $user->save();
        }

        if($request->image)
        {
            $image = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/individuals/', $image);
            $user->image = $image;
            $user->save();
        }

        return back()->with('success', 'User updated successfully !');
    }

    public function user_edit($id)
    {
        $user = User::find($id);
        return view('admin.individuals.user_single', compact('user'));
    }

    public function user_change_status(Request $request)
    {
        $this->validate($request,
            [
                'user_id' => 'required|exists:users,id',
                'state' => 'required|in:0,1',
            ]
        );

        $user = User::find($request->user_id);
        $user->active = $request->state;
        $user->save();

        if($user->active == 1)
        {
            return back()->with('success', 'User activated successfully !');
        }
        else
        {
            return back()->with('success', 'User suspended successfully !');
        }
    }

    public function user_show($id)
    {
        $user = User::find($id);
        return view('admin.individuals.user_show', compact('user'));
    }

    public function user_change_password(Request $request)
    {
        $this->validate($request,
            [
                'password' => 'required|confirmed|min:6',
            ],
            [
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
                'password.confirmed' => 'Password must be same in re-type password',
            ]
        );

        $user = User::where('id', $request->user_id)->first();
        $user->password = $request->password;
        $user->save();

        return back()->with('success', 'Password updated successfully !');
    }

    public function index($state)
    {
        if($state == 'active')
        {
            $techs = Technician::where('type', 'individual')->where('active' , 1)->paginate(50);
        }
        elseif($state == 'suspended')
        {
            $techs = Technician::where('type', 'individual')->where('active' , 0)->paginate(50);
        }

        return view('admin.individuals.index', compact('techs'));
    }

    public function create()
    {
        $cats = Category::where('parent_id', NULL)->get();
        return view('admin.individuals.single', compact('cats'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
            [
                'cat_ids' => 'required',
                'password' => 'required|min:6|confirmed',
                'ar_name' => 'required',
                'en_name' => 'required',
                'email' => 'required|unique:technicians,email',
                'phone' => 'required|unique:technicians,phone',
                'image' => 'sometimes|image',
            ],
            [
                'cat_ids.required' => 'Category is required',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be 6 digits at least',
                'password.confirmed' => 'Password and its confirmation does not match',
                'ar_name.required' => 'Arabic Name is required',
                'en_name.required' => 'English Name is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists,please choose another one',
                'phone.required' => 'Phone is required',
                'phone.unique' => 'Phone already exists,please choose another one',
                'image.image' => 'Image is invalid',
            ]
        );

        $company = Company::where('en_name', 'individuals')->select('id')->first();
        $provider = Provider::where('en_name', 'individuals')->select('id')->first();

        $technician = Technician::create(
            [
                'provider_id' => $provider ? $provider->id : 1,
                'company_id' => $company ? $company->id : 1,
                'jwt' => str_random(25),
                'type' => 'individual',
                'cat_ids' => implode(',',$request->cat_ids),
                'password' => Hash::make($request->password),
                'en_name' => $request->en_name,
                'ar_name' => $request->ar_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'rotation_id' => null,
            ]
        );

        if($request->image)
        {
            $image = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/individuals/', $image);
            $technician->image = $image;
            $technician->save();
        }


        return redirect('/admin/individuals/technician/active')->with('success', 'Technician added successfully !');
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'ar_name'   => 'required',
                'en_name'   => 'required',
                'password'  => 'sometimes|confirmed',
                'email'     => 'required|unique:technicians,email,'.$request->tech_id,
                'phone'     => 'required|unique:technicians,phone,'.$request->tech_id,
                'image'     => 'sometimes|image',
            ],
            [
                'password.confirmed' => 'Password and its confirmation does not match',
                'ar_name.required'   => 'Arabic Name is required',
                'en_name.required'   => 'English Name is required',
                'email.required'     => 'Email is required',
                'email.unique'       => 'Email already exists,please choose another one',
                'phone.required'     => 'Phone is required',
                'phone.unique'       => 'Phone already exists,please choose another one',
                'image.image'        => 'Image is invalid',
            ]
        );

        $technician = Technician::find($request->tech_id)->update(
            [
                'en_name' => $request->en_name,
                'ar_name' => $request->ar_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]
        );

        if($request->password){
            $technician->password = Hash::make($request->password);
            $technician->save();
        }

        if($request->cat_ids){
            $technician->cat_ids = implode(',',$request->cat_ids);
            $technician->save();
        }

        if($request->image)
        {
            $image = unique_file($request->image->getClientOriginalName());
            $request->image->move(base_path().'/public/individuals/', $image);
            $technician->image = $image;
            $technician->save();
        }

        return back()->with('success', 'Technician updated successfully !');
    }


    public function edit($id)
    {
        $technician = Technician::find($id);
        $cats = Category::where('parent_id', NULL)->get();
        return view('admin.individuals.single', compact('technician','cats'));
    }

    public function change_status(Request $request)
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
                'password' => 'required|confirmed|min:6',
            ],
            [
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
                'password.confirmed' => 'Password must be same in re-type password',
            ]
        );

        $tech = Technician::where('id', $request->tech_id)->first();
        $tech->password = $request->password;
        $tech->save();

        return back()->with('success', 'Password updated successfully !');

    }

    public function show($id)
    {
        $technician = Technician::find($id);
        $cats = Category::where('parent_id', NULL)->get();
        return view('admin.individuals.show', compact('technician','cats'));
    }
}
