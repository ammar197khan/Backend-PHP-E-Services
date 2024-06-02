<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{

    protected $casts = ['expire_at' => 'datetime'];

    protected $fillable = [
        'type','role','email','phone','code','expire_at'
    ];

    public $timestamps = false;
}
