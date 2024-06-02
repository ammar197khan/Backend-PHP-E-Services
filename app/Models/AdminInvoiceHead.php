<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminInvoiceHead extends Model
{

    protected $table = 'admin_invoice_head';
    protected $fillable =
        [
            'bill_to','bill_from','provider_id','provider_name','vat','vat_registration','vat_total','order_vat_total','material_vat_total','date',
            'qr_code', 'img_qr_code', 'status', 'total', 'is_paid', 'total_qty', 'total_rate', 'total_count_orders',
            'order_sum_total', 'item_sum_total', 'item_amount_sum_total'
        ];

    public function adminInvoiceDetail(){
        return $this->hasMany(AdminInvoiceDetail::class,'admin_invoice_head_id', 'id');
    }

    public function adminInvoiceMaterial(){
        return $this->hasMany(AdminInvoiceMaterial::class,'admin_invoice_head_id', 'id');
    }

    public function provider(){
        return $this->belongsTo(Provider::class,'provider_id', 'id');
    }

}
