<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTeamAttachment extends Model
{
    protected $fillable =
        [
            'order_team_lead_report_id', 'image_path'
        ];
}
