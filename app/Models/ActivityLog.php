<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public function causer()
    {
        return $this->morphTo();
    }
}
