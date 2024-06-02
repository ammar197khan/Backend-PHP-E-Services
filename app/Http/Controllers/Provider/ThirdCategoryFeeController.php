<?php

namespace App\Http\Controllers\Provider;

use App\Models\Category;
use App\Models\ProviderSubscription;
use App\Models\ProviderCategoryFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ThirdCategoryFeeController extends Controller
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
            'grandParent.en_name AS grandParentName',
            DB::raw("(
                SELECT third_fee
                FROM provider_category_fees
                WHERE provider_id = $provider_id
                AND company_id = $company_id
                AND cat_id = categories.id
            ) as third_fee")
        ])
        ->join('categories as parent', 'categories.parent_id', '=', 'parent.id')
        ->join('categories as grandParent', 'parent.parent_id', '=', 'grandParent.id')
        ->whereIn('categories.parent_id', $subscriptions)
        ->get()
        ->groupBy('grandParentName');

        // dd(collect($categories)->toArray());

        return view('provider.third_category.index', compact('categories','company_id'));
    }

    public function update(Request $request)
    {
        foreach ($request->fees as $category_id => $fees) {
            ProviderCategoryFee::updateOrcreate(
                [
                    'provider_id' => provider()->provider_id,
                    'company_id'  => $request->company_id,
                    'cat_id'      => $category_id
                ],
                ['third_fee' => $fees]
            );
        }

        return back()->with('success', 'Third Category Fees updated successfully');
    }
}
