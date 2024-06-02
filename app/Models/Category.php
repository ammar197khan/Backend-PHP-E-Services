<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['parent_id', 'ar_name', 'en_name', 'active'];

    public function sub_cats()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function sub_cat_third_cat($id)
    {
        return Category::where('parent_id',$id)->get();
    }

    public function third_cats($id)
    {
        return Category::where('parent_id', $id)->get();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')->withTrashed();
    }

    public static function get_cat($id)
    {
        return Category::find($id)->en_name;
    }

    public static function get_cat_all($id)
    {
        return Category::find($id);
    }

    public function items()
    {
        return $this->hasMany(Warehouse::class, 'cat_id');
    }

    public function cat_fee($company_id,$provider_id)
    {
        return $this->hasOne(ProviderCategoryFee::class, 'cat_id')->where('provider_id', $provider_id)->where('company_id', $company_id)->first();
    }

    public function cat_fee_company($provider_id)
    {
        return $this->hasOne(ProviderCategoryFee::class, 'cat_id')->where('provider_id', $provider_id)->where('company_id', company()->company_id)->first();
    }

    public function Orders()
    {
        if($this->type == 1) {
            return
            DB::table('orders')
              ->leftJoin('categories as subCats', 'orders.cat_id', '=', 'subCats.id')
              ->leftJoin('categories as services', 'subCats.parent_id', '=', 'services.id')
              ->where('services.id', $this->id);
        } elseif ($this->type == 2) {
            return
            DB::table('orders')->where('cat_id', $this->id);
        }
    }

    // Parameter : orders_count
    public function getOrdersCountAttribute()
    {
        return $this->Orders()->count();
    }

    // Parameter : rate_average
    public function getRateAverageAttribute()
    {
        return $this->Orders()->join('order_rates', 'orders.id', '=', 'order_rates.order_id')->avg('average');
    }

    // Parameter : sales
    public function getSalesAttribute()
    {
        return $this->Orders()->sum('total_amount');
    }



}
