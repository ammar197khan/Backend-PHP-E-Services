<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminInvoiceDetail extends Model
{

    protected $table = 'admin_invoice_details';
    protected $fillable =
        [
            'order_id','qty','rate','service_name','admin_invoice_head_id','qty_rate_total','date','provider_id'
        ];

}
