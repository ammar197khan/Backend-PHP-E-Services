<?php

namespace App\Http\Controllers\Company;

use App\Models\HouseType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class HouseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $house_types = HouseType::where('company_id', company()->company_id)->paginate(50);
        return view('company.house_type.index', compact('house_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company.house_type.single');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        HouseType::create($this->validateHouseType($request));

        return redirect(route('company.house_types.index'))->with('success','House Type added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $house_type = HouseType::where('id',$id)->first();
        return view('company.house_type.single', compact('house_type'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        HouseType::where('id',$id)->update($this->validateHouseType($request));

        return redirect(route('company.house_types.index'))->with('success','House Type Edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        HouseType::where('id',$id)->delete();

        return redirect(route('company.house_types.index'))->with('success','House Type Deleted successfully');
    }

    protected function validateHouseType($request)
    {
        return request()->validate([
            'company_id' => 'required|exists:companies,id',
            'ar_name' => [
                'required',
                Rule::unique('house_types')->where(function($query) use($request){
                    return $query->where('ar_name', $request->ar_name)->where('company_id', company()->company_id)
                        ->where('id', '!=', isset($request->house_type_id));
                })
            ],
            'en_name' => [
                'required',
                Rule::unique('house_types')->where(function($query) use($request){
                    return $query->where('ar_name', $request->ar_name)->where('company_id', company()->company_id)
                        ->where('id', '!=', isset($request->house_type_id));
                })
            ],
        ]);
    }
}
