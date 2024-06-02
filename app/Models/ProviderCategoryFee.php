<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderCategoryFee extends Model
{
    protected $fillable =
        [
            'provider_id','company_id','cat_id','urgent_fee', 'scheduled_fee','third_fee', 'emergency_fee'
        ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }
}
