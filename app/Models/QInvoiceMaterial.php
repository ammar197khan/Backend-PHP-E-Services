<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QInvoiceMaterial extends Model
{
    public $table = 'q_invoice_material';
    protected $fillable = [
        'qty', 'price', 'item_amount', 'invoice_heads_id','order_id', 'date'
    ];
}
