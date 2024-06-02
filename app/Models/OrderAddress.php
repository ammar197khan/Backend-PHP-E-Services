<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id','lat','lng','name','is_default','approved_by_employer','city','camp','street','plot_no','block_no',
        'building_no','apartment_no','house_type'
    ];
}
