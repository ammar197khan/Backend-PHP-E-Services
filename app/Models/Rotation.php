<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Rotation extends Model
{
    protected $fillable =
        [
            'provider_id','en_name','ar_name','from','to'
        ];


    public function technicians()
    {
        return $this->hasMany(Technician::class, 'rotation_id');
    }

    public function isOnline($current = null)
    {
        $now = Carbon::now()->format('H:i:s');
        $current = $current ?? $now;
        $isOnline = false;

        if ($this->from == $this->to) {
            $isOnline = true;
        } elseif ($this->from < $this->to) {
            $isOnline = $this->from < $now && $now < $this->to;
        } elseif ($this->from > $this->to) {
            if ($now > Carbon::parse('00:00:00')->format('H:i:s')) {
                $isOnline = $now < $this->from && $now < $this->to;
            } else {
                $isOnline = $now > $this->from && $now > $this->to;
            }
        }
        return $isOnline;
    }
}
