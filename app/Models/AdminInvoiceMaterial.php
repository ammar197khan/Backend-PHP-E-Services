<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminInvoiceMaterial extends Model
{

    protected $table = 'admin_invoice_material';
    protected $fillable =
        [
            'qty','price','item_amount','invoice_head_id','order_id','date','provider_id','service_name'
        ];

}
