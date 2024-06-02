<?php

namespace App\Http\Controllers\Company;

use App\Models\SubCompany;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;

class SubCompanyController extends Controller
{
    public function index($status, Request $request)
    {
        $sorter = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';

        $subs = DB::table('sub_companies')->select([
            'sub_companies.id',
            'sub_companies.parent_id',
            'sub_companies.status',
            'sub_companies.en_name',
            'sub_companies.ar_name',
            DB::raw("COALESCE(COUNT(DISTINCT users.id), 0) AS users_count"),
            DB::raw("COALESCE(COUNT(DISTINCT orders.id), 0) AS orders_count"),
            DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
            DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
            DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
        ])
            ->leftJoin('users', 'sub_companies.id', '=', 'users.sub_company_id')
            ->leftJoin('orders', 'users.id','=', 'orders.user_id')
            ->where('parent_id', company()->company_id)
            ->where('status', $status)
            ->orderBy($sorter, $direction)
            ->groupBy('sub_companies.id');


        $subs = $subs->paginate(50);


        return view('company.sub_companies.index', compact('subs','status'));
    }


    public function search($status,Request $request)
    {
        $sorter = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';

        $subs = DB::table('sub_companies')->select([
            'sub_companies.id',
            'sub_companies.parent_id',
            'sub_companies.status',
            'sub_companies.en_name',
            'sub_companies.ar_name',
            DB::raw("COALESCE(COUNT(DISTINCT users.id), 0) AS users_count"),
            DB::raw("COALESCE(COUNT(DISTINCT orders.id), 0) AS orders_count"),
            DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
            DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
            DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
        ])
            ->leftJoin('users', 'sub_companies.id', '=', 'users.sub_company_id')
            ->leftJoin('orders', 'users.id','=', 'orders.user_id')
            ->where('parent_id', company()->company_id)
            ->where('status', $status)
            ->orderBy($sorter, $direction)
            ->groupBy('sub_companies.id');

        $search = Input::get('search');

        $subs = $subs->where(function($q) use($search)
            {
                $q->where('sub_companies.en_name','like','%'.$search.'%');
                $q->orWhere('sub_companies.ar_name','like','%'.$search.'%');
            }
        )->paginate(50);

        return view('company.sub_companies.index', compact('subs','search'));
    }


    public function create()
    {
        return view('company.sub_companies.single');
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'en_name' => 'required|unique:sub_companies,parent_id,'.company()->company_id,
                'ar_name' => 'required|unique:sub_companies,parent_id,'.company()->company_id
            ]
        );


        SubCompany::create
        (
            [
                'parent_id' => company()->company_id,
                'en_name' => $request->en_name,
                'ar_name' => $request->ar_name
            ]
        );

        return redirect('/company/sub_companies/active')->with('success', 'Sub company created successfully !');
    }


    public function users($id, Request $request)
    {
        $request->merge(['sub_company_id' => $id]);

        $this->validate($request,
            [
                'sub_company_id' => 'required|exists:sub_companies,id,parent_id,'.company()->company_id,
            ]
        );

        $state =  $request->users_status== 'suspended' ? 'suspended' : 'active';
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
                ->where('sub_company_id', $id)
                ->orderBy($sorter, $direction)
                ->groupBy('users.id');

        if($state == 'active')
        {
            $users->where('users.active', 1);
        }elseif($state == 'suspended')
        {
            $users->where('users.active', 0);
        }

        $users = $users->paginate(50);

        return view('company.users.index', compact('users'));
    }


    public function edit($id,Request $request)
    {
        $request->merge(['sub_id' => $id]);

        $this->validate($request,
            [
                'sub_id' => 'required|exists:sub_companies,id,parent_id,'.company()->company_id
            ]
        );

        $sub = SubCompany::find($id);

        return view('company.sub_companies.single', compact('sub'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'sub_id' => 'required|exists:sub_companies,id,parent_id,'.company()->company_id,
                'en_name' => 'required',
                'ar_name' => 'required'
            ]
        );

        $en_check = SubCompany::where('en_name', $request->en_name)->where('parent_id', company()->company_id)->where('id','!=',$request->sub_id)->first();
        $ar_check = SubCompany::where('ar_name', $request->en_name)->where('parent_id', company()->company_id)->where('id','!=',$request->sub_id)->first();

        if($en_check) return back()->with('error', 'English name already exists,please try another one');
        if($ar_check) return back()->with('error', 'Arabic name already exists,please try another one');

        SubCompany::where('parent_id', company()->company_id)->where('id', $request->sub_id)->update
        (
            [
                'en_name' => $request->en_name,
                'ar_name' => $request->ar_name
            ]
        );

        return redirect('/company/sub_companies/active')->with('success', 'Sub company updated successfully !');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'sub_id' => 'required|exists:sub_companies,id,parent_id,'.company()->company_id
            ]
        );

        $sub = SubCompany::where('id', $request->sub_id)->first();
            if($sub->status == 'active') $sub->status = 'suspended';
            else $sub->status = 'active';
        $sub->save();

        if($sub->status == 'active') $status = 1;
        else $status = 0;

        User::where('sub_company_id', $sub->id)->update(['active' => $status]);

        return back()->with('success', 'Sub company status updated successfully');
    }



//    public function destroy(Request $request)
//    {
//        $this->validate($request,
//            [
//                'sub_company_id' => 'required|exists:sub_companies,id,parent_id,'.company()->company_id,
//            ]
//        );
//
//
//        SubCompany::where('id', $request->sub_company_id)->delete();
//
//        return redirect('/company/sub_companies/active')->with('success', 'Sub company deleted successfully !');
//    }
}
