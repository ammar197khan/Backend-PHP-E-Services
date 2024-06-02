<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTeamLeadReport extends Model
{

    protected $fillable =
        [
            'order_id', 'is_approved_status', 'supervisor_remarks', 'tl_remarks', 'supervisor_sendback_date','tl_submit_date'
        ];
        public function orderTeamAttachment(){
            return $this->hasOne(OrderTeamAttachment::class,'order_team_lead_report_id');
        }
}
