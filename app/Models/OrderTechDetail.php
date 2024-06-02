<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTechDetail extends Model
{
    protected $fillable =
        [
            'order_id','type_id', 'working_hours', 'desc','before_images','after_images'
        ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'type_id');
    }

    public function cat_fee($provider_id,$company_id,$cat_id)
    {
       return ProviderCategoryFee::where('provider_id',$provider_id)->where('company_id',$company_id)->
        where('cat_id',$cat_id)->select('id','cat_id','third_fee')->first();
    }
}
