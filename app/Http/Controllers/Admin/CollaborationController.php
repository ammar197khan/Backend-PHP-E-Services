<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Collaboration;
use App\Models\Company;
use App\Models\Provider;
use App\Models\ProviderCategoryFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class CollaborationController extends Controller
{
    public function index(Request $request)
    {
        $partnerships = Collaboration::query();

        if($request->has('search')) {
          $partnerships->select('collaborations.*')
          ->join('providers', 'collaborations.provider_id', '=', 'providers.id')
          ->join('companies', 'collaborations.company_id', '=', 'companies.id')
          ->where('providers.en_name', 'like', "%{$request->search}%")
          ->orWhere('providers.ar_name', 'like', "%{$request->search}%")
          ->orWhere('providers.email', 'like', "%{$request->search}%")
          ->orWhere('companies.en_name', 'like', "%{$request->search}%")
          ->orWhere('companies.ar_name', 'like', "%{$request->search}%")
          ->orWhere('companies.email', 'like', "%{$request->search}%");
        }

        $colls = $partnerships->get()->groupBy('provider_id');
        return view('admin.collaborations.index',compact('colls'));
    }

    public function create()
    {
        $providers = Provider::get();
        $companies = Company::get();
        return view('admin.collaborations.single', compact('providers','companies'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'provider_id' => 'required|exists:providers,id',
            'companies'   => 'required|array',
            'companies.*' => 'exists:companies,id'
        ]);

        foreach($request->companies as $company_id) {
            Collaboration::updateOrcreate([
                'provider_id' => $request->provider_id,
                'company_id'  => $company_id
            ]);
        }

        return redirect('/admin/collaborations')->with('success', 'Collaborations created successfully');
    }

    public function edit($provider_id)
    {
        $provider  = Provider::find($provider_id);
        $companies = Company::get();
        $collaboration = Collaboration::where('provider_id', $provider_id)->get();

        return view('admin.collaborations.single', compact('collaboration','provider','companies'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'provider_id' => 'required|exists:providers,id',
            'companies' => 'required|array',
            'companies.*' => 'exists:companies,id'
        ]);

        foreach($request->companies as $company_id) {
            Collaboration::updateOrcreate([
                'provider_id' => $request->provider_id,
                'company_id' => $company_id
            ]);
        }

        Collaboration::where('provider_id', $request->provider_id)
        ->whereNotIn('company_id', $request->companies)
        ->delete();

        return redirect('/admin/collaborations')->with('success', 'Collaborations updated successfully');
    }

    public function destroy(Request $request)
    {
        $this->validate($request, [
            'provider_id' => 'required|exists:providers,id',
        ]);

        Collaboration::where('provider_id', $request->provider_id)->delete();

        return back()->with('success', 'Collaboration deleted successfully');
    }
}
