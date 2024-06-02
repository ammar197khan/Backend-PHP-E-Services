<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Migrations\WareHouse;
use App\Migrations\WareHouseRequest;
use App\Models\Address;
use App\Models\Provider;
use App\Models\ProviderAdmin;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $sorter     = $request->sort ? explode('.', $request->sort)[0] : 'id';
        $direction  = $request->sort ? explode('.', $request->sort)[1] : 'asc';
        $providers =
        DB::table('providers')->select([
          'providers.id',
          'providers.en_name',
          'providers.ar_name',
          'providers.email',
          'providers.phones',
          'providers.logo',
          'providers.active',
          DB::raw("COALESCE(COUNT(DISTINCT collaborations.company_id), 0) AS collaborations_count"),
          DB::raw("COALESCE(COUNT(DISTINCT technicians.id), 0) AS technicians_count"),
          DB::raw("COALESCE(COUNT(DISTINCT orders.id), 0) AS orders_count"),
          DB::raw("COALESCE(COUNT(DISTINCT orders.user_id), 0) AS customers_count"),
          DB::raw("COALESCE(SUM(orders.order_total), 0) AS services_sales"),
          DB::raw("COALESCE(SUM(orders.item_total), 0) AS items_sales"),
          DB::raw("COALESCE(SUM(orders.total_amount), 0) AS total_sales"),
          DB::raw("COALESCE(COUNT(DISTINCT order_rates.id), 0) AS rate_count"),
          DB::raw("COALESCE(AVG(order_rates.average), 0) AS rate_average"),
          'providers.created_at',
          'providers.updated_at',
        ])
        ->leftJoin('technicians', 'providers.id', '=', 'technicians.provider_id')
        ->leftJoin('collaborations', 'providers.id', '=', 'collaborations.provider_id')
        ->leftJoin('orders', 'providers.id', '=', 'orders.provider_id')
        ->leftJoin('order_rates', 'orders.id', '=', 'order_rates.order_id')
        ->orderBy($sorter, $direction)
        ->groupBy('providers.id');

        if($request->has('active')){
          $providers->where('providers.active', $request->active);
        }

        if($request->has('search')){
            $providers->where('providers.en_name','like', "%{$request->search}%")
            ->orWhere('providers.ar_name','like', "%{$request->search}%")
            ->orWhere('providers.email','like', "%{$request->search}%");
        }

        $providers = $providers->paginate(50);

        return view('admin.providers.index', compact('providers'));
    }

    public function show($id)
    {
        $provider = Provider::find($id);
        return view('admin.providers.show', compact('provider'));
    }

    public function create()
    {
        $addresses = Address::where('parent_id', NULL)->get();
        return view('admin.providers.single', compact('addresses'));
    }

    public function store(Request $request)
    {
          $rules = [
            'address_id'    => 'required',
            'interest_fee'  => 'required|integer',
            'warehouse_fee' => 'required|integer',
            'ar_name'       => 'required|unique:providers,ar_name',
            'en_name'       => 'required|unique:providers,en_name',
            'ar_desc'       => 'required',
            'en_desc'       => 'required',
            'email'         => 'required|email|unique:providers,email',
            'phones'        => 'required|array',
            'logo'          => 'required|image',
            'username'      => 'required|unique:provider_admins,username',
            'password'      => 'required|min:6|confirmed',
            'mobile'        => 'required|unique:provider_admins,phone',
            'badge_id'      => 'required'
        ];

        $messages = [
          'address_id.required'    => 'Address is required',
          'interest_fee.required'  => 'Interest Fee is required',
          'warehouse_fee.required' => 'Warehouse Fee is required',
          'ar_name.required'       => 'Arabic name is required',
          'ar_name.unique'         => 'Arabic name already exists',
          'en_name.required'       => 'English name is required',
          'en_name.unique'         => 'English name already exists',
          'ar_desc.required'       => 'Arabic description is required',
          'en_desc.required'       => 'English description is required',
          'email.required'         => 'Email is required',
          'email.unique'           => 'Email already exists',
          'phones.required'        => 'Phones are required',
          'logo.required'          => 'Logo is required',
          'logo.image'             => 'Logo is not valid',
          'username.required'      => 'Username is required',
          'username.unique'        => 'Username already exists',
          'password.required'      => 'Password is required',
          'password.min'           => 'Password must be 6 digits at minimum',
          'password.confirmed'     => 'Password and its confirmation does not match',
          'mobile.required'        => 'Admin Mobile is required',
          'mobile.unique'          => 'Admin Mobile already exists',
          'badge_id.required'      => 'Admin Badge ID is required',
      ];

        $this->validate($request, $rules, $messages);

        $commissions = NULL;
        if($request->type == 'categorized'){
            $commissions =
            Provider::parseCommissionCategories(
                $request->commission_from,
                $request->commission_to,
                $request->commission_value
            );
        }
        //-------upload provide image ------//
        $image = "";
        if($request->logo){
            $image = unique_file($request->logo->getClientOriginalName());
            $request->logo->move(base_path().'/public/providers/logos/', $image);
        }
        $provider = Provider::create([
            'address_id'            => $request->address_id,
            'interest_fee'          => $request->interest_fee,
            'warehouse_fee'         => $request->warehouse_fee,
            'ar_name'               => $request->ar_name,
            'en_name'               => $request->en_name,
            'ar_desc'               => $request->ar_desc,
            'en_desc'               => $request->en_desc,
            'email'                 => $request->email,
            'phones'                => serialize(array_filter($request->phones)),
            'logo'                  => $image,
            'type'                  => $request->type,
            'commission_categories' => json_decode($commissions)
        ]);

        $admin = ProviderAdmin::create([
            'provider_id' => $provider->id,
            'badge_id'    => $request->badge_id,
            'role'        => 'system_admin',
            'name'        => $provider->en_name,
            'email'       => $provider->email,
            'phone'       => $request->mobile,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'image'       => $image
        ]);

        $permission = Permission::where('guard_name','provider')->pluck('id')->toArray();
        $admin->syncPermissions($permission);

        WareHouse::Up($provider->id);
-       WareHouseRequest::Up($provider->id);

        return redirect('/admin/providers')->with('success', 'Provider added successfully !');
    }


    public function edit($id)
    {
        $provider  = Provider::where('id', $id)->with('address')->first();
        $addresses = Address::where('parent_id', NULL)->get();
        return view('admin.providers.single', compact('provider', 'addresses'));
    }


    public function update(Request $request)
    {
        $admin = ProviderAdmin::where('provider_id', $request->provider_id)->first();
        $request->merge(['admin_id' => $admin->id]);

        $rules = [
            'provider_id'   => 'required|exists:providers,id',
            'address_id'    => 'sometimes',
            'interest_fee'  => 'required|numeric',
            'warehouse_fee' => 'required|numeric',
            'ar_name'       => 'required|unique:providers,ar_name,'.$request->provider_id,
            'en_name'       => 'required|unique:providers,en_name,'.$request->provider_id,
            'ar_desc'       => 'required',
            'en_desc'       => 'required',
            'email'         => 'required|email|unique:providers,email,'.$request->provider_id,
            'phones'        => 'required|array',
            'logo'          => 'sometimes|image',
            'username'      => 'required|unique:provider_admins,username,'.$request->admin_id,
            'password'      => 'sometimes|confirmed',
        ];

        $messages = [
            'address_id.required'    => 'Address is required',
            'interest_fee.required'  => 'Interest Fee is required',
            'interest_fee.numeric'   => 'Interest Fee is not a number',
            'warehouse_fee.required' => 'Warehouse Fee is required',
            'warehouse_fee.numeric'  => 'Warehouse Fee is not a number',
            'ar_name.required'       => 'Arabic name is required',
            'ar_name.unique'         => 'Arabic name already exists',
            'en_name.required'       => 'English name is required',
            'en_name.unique'         => 'English name already exists',
            'ar_desc.required'       => 'Arabic description is required',
            'en_desc.required'       => 'English description is required',
            'email.required'         => 'Email is required',
            'email.unique'           => 'Email already exists',
            'phones.required'        => 'Phones are required',
            'logo.image'             => 'Logo is not valid',
            'username.required'      => 'Username is required',
            'username.unique'        => 'Username already exists',
            'password.required'      => 'Password is required',
            'password.confirmed'     => 'Password does not match',
        ];

        $this->validate($request, $rules, $messages);

        $commissions = NULL;
        if($request->type == 'categorized'){
            $commissions =
            Provider::parseCommissionCategories(
                $request->commission_from,
                $request->commission_to,
                $request->commission_value
            );
        }

        $provider                        = Provider::findOrFail($request->provider_id);
        $provider->address_id            = $request->address_id ?? $provider->address_id;
        $provider->type                  = $request->type;
        $provider->interest_fee          = $request->interest_fee;
        $provider->warehouse_fee         = $request->warehouse_fee;
        $provider->ar_name               = $request->ar_name;
        $provider->en_name               = $request->en_name;
        $provider->ar_desc               = $request->ar_desc;
        $provider->en_desc               = $request->en_desc;
        $provider->email                 = $request->email;
        $provider->po_box                = $request->po_box;
        $provider->ar_organization_name  = $request->ar_organization_name;
        $provider->en_organization_name  = $request->en_organization_name;
        $provider->vat                   = $request->vat;
        $provider->vat_registration      = $request->vat_registration;
        $provider->commission_categories = json_decode($commissions);
        $provider->phones                = serialize(array_filter($request->phones));
        if($request->logo){
            $image = unique_file($request->logo->getClientOriginalName());
            $request->logo->move(base_path().'/public/providers/logos/', $image);
            $provider->logo = $image;
        }
        $provider->save();
        @// BUG: Why admin change his password !!!
        $admin->username = $request->username;
        if($request->password) $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect('/admin/providers')->with('success', 'Provider added successfully !');
    }

    public function change_state(Request $request)
    {
        $this->validate($request, [
            'provider_id' => 'required|exists:providers,id',
            'state'       => 'required|in:0,1',
        ]);

        $provider = Provider::findOrFail($request->provider_id);
        $provider->active = $request->state;
        $provider->save();

        $msg =
        $provider->active == 1
        ? 'Provider activated successfully !'
        : 'Provider suspended successfully !';

        return back()->with('success', $msg);
    }

    public function destroy(Request $request)
    {
        $provider = Provider::findOrFail($request->provider_id);
        $provider->delete();
        return redirect('/admin/providers')->with('success', 'Provider deleted successfully !');
    }

    public function technicians($id, Request $request)
    {
        // Technicians index @ admin provider details
        $request->merge(['provider_id' => $id]);
        return (new TechnicianController)->index($request);
    }

}
