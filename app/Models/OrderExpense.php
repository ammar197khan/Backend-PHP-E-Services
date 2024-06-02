<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderExpense extends Model
{
    protected $fillable = [
        'order_id','name','cost'
    ];
}
