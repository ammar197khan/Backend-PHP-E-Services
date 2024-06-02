<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QInvoiceDetail extends Model
{
    public $table = 'q_invoice_details';
    protected $fillable = [
        'order_id', 'qty', 'rate', 'service_name'
    ];
}
