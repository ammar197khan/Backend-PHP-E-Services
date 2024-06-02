<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privacy extends Model
{
    protected $fillable = [
        'ar_text', 'en_text'
    ];
}
