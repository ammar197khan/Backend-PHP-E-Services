<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login_view()
    {
        if(Auth::guard('company')->user())
        {
            return redirect()->route('company.home');
        }

        return view('company.login.index');
    }


    public function login(Request $request)
    {
        $this->validate($request,
            [
                'username' => 'required|exists:company_admins,username,active,1',
                'password' => 'required'
            ],
            [
                'username.required' => 'Username is required',
                'username.exists' => 'Username is invalid',
                'password.required' => 'Password is required'
            ]
        );


        if(Auth::guard('company')->attempt(['username' => $request->username, 'password' => $request->password], false))
        {
            return redirect()->route('company.home');
        }

        return back()->with('error','Invalid Credentials');
    }


    public function logout()
    {
        Auth::guard('company')->logout();
        return redirect()->route('company.view_login');
    }

}
