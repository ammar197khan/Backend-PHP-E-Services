<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Technician extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'type','jwt','busy','online','provider_id','company_id' ,'sub_company_id','badge_id','cat_ids','active','rotation_id','password','en_name','ar_name','email','phone','image','id_card', 'technician_role_id'
    ];

    public function technicainRole(){
        return $this->belongsTo(TechnicainRole::class, 'technician_role_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }


    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }


    public function orders()
    {
        return $this->hasMany(Order::class, 'tech_id');
    }


    public function rotation()
    {
        return $this->belongsTo(Rotation::class, 'rotation_id')->select('id','en_name as en_rotation_name','ar_name as ar_rotation_name','from','to');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function sub_company()
    {
        return $this->belongsTo(SubCompany::class, 'sub_company_id');
    }

//    public function get_company($company_id)
//    {
//        $company = Company::where('id', $company_id)->first()->en_name;
//        return $company;
//    }
//
//    public function get_sub_company($sub_company_id)
//    {
//        $sub_company = Company::where('id', $sub_company_id)->first()->en_name;
//        return $sub_company;
//    }


    public function get_tech($lang,$id)
    {
        $tech = Technician::where('id', $id)->select($lang.'_name as name','cat_id','phone','image','lat','lng');
        return $tech;
    }


    public function get_parent_cat($ids)
    {
        $ids = explode(',', $ids);
        return Category::withTrashed()->whereIn('id',$ids)->groupBy('parent_id')->select('parent_id')->pluck('parent_id')->toArray();
    }

    public function get_company_id_from_sub_company($ids)
    {
        $ids = explode(',', $ids);
        return SubCompany::whereIn('id',$ids)->groupby('parent_id')->select('parent_id')->pluck('parent_id')->toArray();
    }

    public function get_categories($lang,$ids)
    {
        $ids = explode(',',$ids);
        $categories = Category::withTrashed()->whereIn('id',$ids)->select($lang.'_name as name')->get();

        return $categories;
    }


    public function get_category($lang,$id)
    {
        $category = Category::withTrashed()->where('id', $id)->select($lang.'_name as name')->first();
        return $category->name;
    }


    public function get_category_parent($lang,$name)
    {
        $parent_id = Category::withTrashed()->where('en_name',$name)->select('parent_id')->first()->parent_id;
        $category = Category::withTrashed()->where('id', $parent_id)->select($lang.'_name as name')->first();

        return $category->name;
    }


    public function get_category_all($id)
    {
        $category = Category::where('id', $id)->select('en_name','ar_name')->first();
        return $category;
    }

    public static function get_all_rate($id)
    {
        $orders = Order::where('tech_id', $id)->pluck('id');
        $rates = OrderRate::whereIn('order_id',$orders)->select('appearance','cleanliness','performance','commitment','average')->get();

        $arr = [];

        if($rates->pluck('appearance')->count() > 0)  $appearance = $rates->sum('appearance') / $rates->pluck('appearance')->count();
        else  $appearance = 0;

        if($rates->pluck('cleanliness')->count() > 0)  $cleanliness = $rates->sum('cleanliness') / $rates->pluck('cleanliness')->count();
        else  $cleanliness = 0;

        if($rates->pluck('performance')->count() > 0)  $performance = $rates->sum('performance') / $rates->pluck('performance')->count();
        else  $performance = 0;

        if($rates->pluck('commitment')->count() > 0)  $commitment = $rates->sum('commitment') / $rates->pluck('commitment')->count();
        else  $commitment = 0;

        $arr['appearance'] = $appearance;
        $arr['cleanliness'] = $cleanliness;
        $arr['performance'] = $performance;
        $arr['commitment'] = $commitment;

        $rate =  $arr['appearance'] +  $arr['cleanliness'] +  $arr['performance'] + $arr['commitment'];
        $average = $rate / 4;

        return (string)round($average,0);
    }


    public function get_rates($tech_id)
    {
        $orders = Order::where('tech_id', $tech_id)->pluck('id');
        $rates = OrderRate::whereIn('order_id',$orders)->select('appearance','cleanliness','performance','commitment')->get();

        $arr = [];
        if($rates->count() > 0)
        {
            $arr['appearance'] = $rates->sum('appearance') / $rates->pluck('appearance')->count();
            $arr['cleanliness'] = $rates->sum('appearance') / $rates->pluck('appearance')->count();
            $arr['performance'] = $rates->sum('performance') / $rates->pluck('performance')->count();
            $arr['commitment'] = $rates->sum('commitment') / $rates->pluck('commitment')->count();
        }
        else
        {
            $arr['appearance'] = 0;
            $arr['cleanliness'] = 0;
            $arr['performance'] = 0;
            $arr['commitment'] = 0;
        }

        return $arr;
    }

    public function get_rate_tech($tech_id)
    {
        $orders = Order::where('tech_id', $tech_id)->pluck('id');
        $rates = OrderRate::whereIn('order_id',$orders)->select('appearance','cleanliness','performance','commitment')->get();

        $arr = [];
        if($rates->count() > 0)
        {
            $arr['appearance'] = $rates->sum('appearance') / $rates->pluck('appearance')->count();
            $arr['cleanliness'] = $rates->sum('appearance') / $rates->pluck('appearance')->count();
            $arr['performance'] = $rates->sum('performance') / $rates->pluck('performance')->count();
            $arr['commitment'] = $rates->sum('commitment') / $rates->pluck('commitment')->count();
        }
        else
        {
            $arr['appearance'] = 0;
            $arr['cleanliness'] = 0;
            $arr['performance'] = 0;
            $arr['commitment'] = 0;
        }

        $rate =  $arr['appearance'] +  $arr['cleanliness'] +  $arr['performance'] + $arr['commitment'];
        $total = $rate / 4;

        return $total;
    }


    public function get_distance($tech,$user)
    {
            $theta = $user->lng - $tech->lng;
            $dist = sin(deg2rad($user->lat)) * sin(deg2rad($tech->lat)) +  cos(deg2rad($user->lat)) * cos(deg2rad($tech->lat)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper("K");
            $distance = $miles * 1.609344;

        return  round($distance, 2);
    }


    public function get_category_list($ids)
    {
        $cats = explode(',',$ids);
        $names = [];

        foreach($cats as $cat)
        {
            array_push($names,$this->get_category('en',$cat)) ;
        }

        return $names;
    }

    public function get_sub_company($id)
    {
        $sub = SubCompany::where('id', $id)->select('en_name as name')->first();
        return $sub->name;
    }

    public function get_sub_company_list($ids)
    {
        $subs = explode(',',$ids);
        $names = [];

        foreach($subs as $sub)
        {
            array_push($names,$this->get_sub_company($sub)) ;
        }

        return $names;
    }


    public function get_service_fee($provider_id,$cat_id)
    {
        $fee = ProviderCategoryFee::where('provider_id', $provider_id)->where('cat_id', $cat_id)->select('urgent_fee')->first()->urgent_fee;
        return $fee;
    }

}
