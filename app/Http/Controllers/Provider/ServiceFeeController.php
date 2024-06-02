<?php

namespace App\Http\Controllers\Provider;

use App\Models\Category;
use App\Models\ProviderSubscription;
use App\Models\ProviderCategoryFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ServiceFeeController extends Controller
{
    public function view($company_id)
    {
        $provider_id = provider()->provider_id;
        $subscriptions =
        ProviderSubscription::where('provider_id', $provider_id)->first();

        $subscriptions = $subscriptions ? unserialize($subscriptions->subs) : [];

        $categories =
        Category::select([
            'categories.id',
            'categories.en_name',
            'categories.type',
            'categories.parent_id',
            'parent.en_name AS parentName',
            DB::raw("(
                SELECT urgent_fee
                FROM provider_category_fees
                WHERE provider_id = $provider_id
                AND company_id = $company_id
                AND cat_id = categories.id
            ) as urgent_fee"),
            DB::raw("(
                SELECT scheduled_fee
                FROM provider_category_fees
                WHERE provider_id = $provider_id
                AND company_id = $company_id
                AND cat_id = categories.id
              ) as scheduled_fee"),
              DB::raw("(
                SELECT emergency_fee
                FROM provider_category_fees
                WHERE provider_id = $provider_id
                AND company_id = $company_id
                AND cat_id = categories.id
              ) as emergency_fee"),
        ])
        ->join('categories as parent', 'categories.parent_id', '=', 'parent.id')
        ->whereIn('categories.id', $subscriptions)
        ->get()
        ->groupBy('parentName');

        return view('provider.services.single', compact('categories','company_id'));
    }


    public function update(Request $request)
    {
        
        foreach($request->urgent_fees as $category_id => $fees) {
            ProviderCategoryFee::updateOrcreate(
                [
                    'provider_id' => provider()->provider_id,
                    'company_id' => $request->company_id,
                    'cat_id' => $category_id
                ],
                ['urgent_fee' => $fees]
            );
        }

        foreach($request->scheduled_fees as $category_id => $fees) {
            ProviderCategoryFee::updateOrcreate(
                [
                    'provider_id' => provider()->provider_id,
                    'company_id' => $request->company_id,
                    'cat_id' => $category_id
                ],
                ['scheduled_fee' => $fees]
            );
        }
        foreach($request->emergency_fees as $category_id => $fees) {
            ProviderCategoryFee::updateOrcreate(
                [
                    'provider_id' => provider()->provider_id,
                    'company_id' => $request->company_id,
                    'cat_id' => $category_id
                ],
                ['emergency_fee' => $fees]
            );
        }
        return back()->with('success', 'Services Fees updated successfully');
    }
}
