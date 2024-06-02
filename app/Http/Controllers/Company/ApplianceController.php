<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appliance;

class ApplianceController extends Controller
{
    public function index(Request $request)
    {
        $data = Appliance::query();

        if($request->search){
          $q = $request->search;
          $data = $data->where('id', 'like', "%$q%")
                        ->orWhere('name', 'like', "%$q%")
                        ->orWhere('munufucturer', 'like', "%$q%")
                        ->orWhere('model', 'like', "%$q%")
                        ->orWhere('quantity', 'like', "%$q%")
                        ->orWhere('serial_number', 'like', "%$q%")
                        ->orWhere('description', 'like', "%$q%");
        }

        $data = $data->paginate(25);
        return view('company.appliances.index', compact('data'));
    }

    public function create()
    {
        return view('company.appliances.single');
    }

    public function store(Request $request)
    {
        $inputs = $request->all();
        if($request->photo) {
            $name = unique_file($request->photo->getClientOriginalName());
            $request->photo->move(base_path().'/public/images/appliances/',$name);
            $inputs['photo'] = 'images/appliances/' . $name;
        }
        $item = Appliance::create($inputs);
        return redirect('/company/appliances')->with('success', 'Appliance created successfully!');
    }

    public function show($id)
    {
        $item = Appliance::findOrFail($id);
        return view('company.appliances.show', compact('item'));
    }

    public function edit($id)
    {
        $item = Appliance::findOrFail($id);
        return view('company.appliances.single', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Appliance::findOrFail($id);
        if($request->photo) {
            $name = unique_file($request->photo->getClientOriginalName());
            $request->photo->move(base_path().'/public/images/appliances',$name);
            $request->merge(['photo' => $name]);
        }
        $item->update($request->all());
        return redirect('/company/appliances')->with('success', 'Appliance updated successfully!');
    }

    public function destroy($id)
    {
        $item = Appliance::findOrFail($id);
        $item->delete();
        return redirect('/company/appliances')->with('success', 'Appliance deleted successfully!');
    }

    public function search(Request $request)
    {
    }
}
