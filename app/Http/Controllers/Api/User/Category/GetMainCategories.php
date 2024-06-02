<?php

namespace App\Http\Controllers\Api\User\Category;

use Validator;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Collaboration;
use App\Models\CompanySubscription;
use App\Models\ProviderSubscription;
use App\Http\Controllers\Controller;

class GetMainCategories extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|exists:users,id',
            'jwt'        => 'required|exists:users,jwt,id,' . $request->user_id,
            'company_id' => 'required|exists:companies,id|exists:users,company_id,id,' . $request->user_id
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'msg' => $validator->messages()]);
        }

        $lang = $request->header('lang');

        $company_providers_ids = Collaboration::where('company_id', $request->company_id)->pluck('provider_id');
        $provider_subs = ProviderSubscription::whereIn('provider_id', $company_providers_ids)->pluck('subs');
        $companies_subs = CompanySubscription::where('company_id', $request->company_id)->pluck('subs');

        $subs_provider_array = [];
        $subs_company_array = [];

        foreach ($provider_subs as $sub) {
            $subs_provider_array = array_merge(unserialize($sub), $subs_provider_array);
        }

        foreach ($companies_subs as $sub) {
            $subs_company_array = array_merge(unserialize($sub), $subs_company_array);
        }

        $subs_array = array_intersect($subs_provider_array, $subs_company_array);

        $sub_categories = Category::whereIn('id', $subs_array)->pluck('parent_id');
        $categories = Category::whereIn('id', $sub_categories)->select('id', $lang.'_name as name', 'image', 'active')->get();

        foreach ($categories as $category) {
            $category['image'] = 'http://'.$_SERVER['SERVER_NAME'].'/categories/'.$category->image;
        }

        return response()->json($categories);
    }
}
