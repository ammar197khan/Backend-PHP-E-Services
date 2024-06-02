<?php


namespace App\Script;


use Illuminate\Support\Facades\DB;

class setMaterials
{
    public function __invoke()
    {
        $providers = DB::table('providers')->get();
        $suppliers = DB::table('suppliers')->pluck('en_name')->toArray();

        foreach ($providers as $provider)
        {
            if(!in_array($provider->en_name, $suppliers)){

                $providerItems = DB::table($provider->id. '_warehouse_parts')->get();
                if ($providerItems){
                    DB::table('suppliers')->insert([
                        'active'                => $provider->active,
                        'fees'                  => $provider->interest_fee,
                        'commission_categories' => $provider->commission_categories,
                        'ar_name'               => $provider->ar_name,
                        'en_name'               => $provider->en_name,
                        'ar_desc'               => $provider->ar_desc,
                        'en_desc'               => $provider->en_desc,
                        'email'                 => $provider->email,
                        'phones'                => $provider->phones,
                        'logo'                  => $provider->logo,
                        'address_id'            => $provider->address_id,
                        'created_at'            => $provider->created_at,
                    ]);

                    foreach ( $providerItems as $item) {
                        $material = DB::table('materials')->insertGetId([
                            'active'            => $item->active,
                            'code'              => $item->code,
                            'warehouse_owner'   => 'provider',
                            'owner_id'          => $provider->id,
                            'count'             => $item->count,
                            'requested_count'   => $item->requested_count,
                            'price'             => $item->price,
                            'en_name'           => $item->en_name,
                            'ar_name'           => $item->ar_name,
                            'en_desc'           => $item->en_desc,
                            'ar_desc'           => $item->ar_desc,
                            'image'             => $item->image,
                        ]);

                        $unSerializeCategoryIds = unserialize($item->cat_id);
                        $categoryIds = explode(',', $unSerializeCategoryIds);

                        foreach ($categoryIds as $categoryId) {
                            DB::table('category_material')->insert([
                                'material_id' => $material,
                                'category_id' => $categoryId == '#N/A' ? 2 : $categoryId,
                            ]);
                        }
                    }
                }
            }
        }

        return 'success';
    }
}
