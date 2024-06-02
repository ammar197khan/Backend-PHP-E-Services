<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProcessType extends Model
{
    protected $table = 'order_process_types';

    protected $fillable = [
        'order_id','status' , 'technicain_id'
    ];
}
