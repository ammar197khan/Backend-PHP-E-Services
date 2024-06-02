<?php

namespace App\Http\Controllers\Company;

use App\Models\Address;
use App\Models\Company;
use App\Models\OrderProcessType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index()
    {
        $company = Company::find(company()->company_id);
        return view('company.profile.index', compact('company'));
    }

    public function info()
    {
        $addresses = Address::where('parent_id', NULL)->get();
        $company = Company::where('id', company()->company_id)->with('orderProcessType')->first();
        // dd($company);
        $orderProcessTypes = OrderProcessType::all();
        return view('company.profile.info', compact('company','addresses', 'orderProcessTypes'));
    }

    public function update(Request $request)
    {
        $this->validate($request,
            [
                'address_id' => 'sometimes|exists:addresses,id',
                'ar_name' => 'required|unique:companies,ar_name,'.company()->company_id,
                'en_name' => 'required|unique:companies,en_name,'.company()->company_id,
                'ar_desc' => 'required',
                'en_desc' => 'required',
                'email'   => 'required|email|unique:companies,email,'.company()->company_id,
                'phones'  => 'required|array',
                'logo'    => 'sometimes|image',
                'order_process_id' => 'required',
            ]
        );

        $company = Company::find(company()->company_id);
        //-----------update image-----------------//
        $name = "";
        if($request->logo)
        {
            if($company->logo){
                if(file_exists(base_path().'/public/companies/logos/'.$company->cr_upload)){
                unlink(base_path().'/public/companies/logos/'.$company->logo);
            }
        }

            $name = unique_file($request->logo->getClientOriginalName());
            $request->logo->move(base_path().'/public/companies/logos/',$name);
        }
        if($request->cr_upload)
        {
         if($company->cr_upload){
            if(file_exists(base_path().'/public/companies/logos/'.$company->cr_upload)){
                unlink(base_path().'/public/companies/logos/'.$company->cr_upload);
            }
        }


            $name = unique_file($request->cr_upload->getClientOriginalName());
            $request->cr_upload->move(base_path().'/public/companies/logos/',$name);
            $company->cr_upload = $name;

        }
        if($request->vat_certificate_upload)
        {
            if($company->vat_upload){
            if(file_exists(base_path().'/public/companies/logos/'.$company->vat_upload)){
                unlink(base_path().'/public/companies/logos/'.$company->vat_upload);
            }
        }


            $name = unique_file($request->vat_certificate_upload->getClientOriginalName());
            $request->vat_certificate_upload->move(base_path().'/public/companies/logos/',$name);
            $company->vat_upload = $name;

        }
        if($request->agreement_upload)
        {
            if($company->agreement_upload){
            if(file_exists(base_path().'/public/companies/logos/'.$company->agreement_upload)){
                unlink(base_path().'/public/companies/logos/'.$company->agreement_upload);
            }
        }

            $name = unique_file($request->agreement_upload->getClientOriginalName());
            $request->agreement_upload->move(base_path().'/public/companies/logos/',$name);
            $company->agreement_upload = $name;

        }
        $company->update([
            'address_id' => $request->address_id ? $request->address_id : $company->address_id,
            'en_name' => $request->en_name,
            'ar_name' => $request->ar_name,
            'en_desc' => $request->en_desc,
            'ar_desc' => $request->ar_desc,
            'phones'  => serialize(array_filter($request->phones)),
            'logo'    => $name,
            'vat_registration' => !empty($request->vat_registration)? $request->vat_registration : '',
            'vat' => $request->vat,
            'po_box' => $request->po_box,
            'en_organization_name' => $request->en_organization_name,
            'ar_organization_name' => $request->ar_organization_name,
            'order_process_id' => (int)$request->order_process_id
            // 'logo' => $request->logo ? $company->setLogo($company->logo,$request->logo) : $company->logo
        ]);

        return back()->with('success', 'Info changed successfully !');
    }
}
