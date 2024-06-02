<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $fillable = [
        'user_id','lat','lng','name','address','is_default','approved_by_employer','city','camp','street','plot_no','block_no',
        'building_no','apartment_no','house_type'
    ];
}
