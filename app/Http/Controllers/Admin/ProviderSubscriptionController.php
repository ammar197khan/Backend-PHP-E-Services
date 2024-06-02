<?php

namespace App\Http\Controllers\Admin;

use App\Models\Provider;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ProviderSubscription;
use App\Http\Controllers\Controller;

class ProviderSubscriptionController extends Controller
{

    public function edit($provider_id)
    {
        $provider = Provider::find($provider_id);
        $subscriptions = ProviderSubscription::where('provider_id', $provider_id)->first();
        $subs = isset($subscriptions) ? unserialize($subscriptions->subs) : [];

        $cats = Category::where('parent_id', NULL)->get();

        return view('admin.providers.subscriptions', compact('provider','subs','cats'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'provider_id' => 'required|exists:providers,id',
            'subs'        => 'required|array',
            'subs.*'      => 'exists:categories,id,type,2'
        ]);

        ProviderSubscription::updateOrCreate(
            ['provider_id' => $request->provider_id],
            ['subs' => serialize($request->subs)]
        );

        return back()->with('success', 'Subscriptions have been set successfully !');
    }
}
