<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QInvoiceHead extends Model
{
    public $table = 'q_invoice_heads';
    protected $fillable = [
        'bill_to', 'bill_from', 'sub_total', 'vat', 'total_due_commission', 'created_at',
        'updated_at', 'provider_id', 'company_id', 'company_name', 'provider_name', 'orders_vat',
        'materials_vat', 'total_count', 'total_orders_amount', 'total_items_amount', 'total', 'date', 'status', 'vat_registration'
    ];

    public function invoiceDetail(){
              return $this->hasMany(QInvoiceDetail::class,'invoice_head_id', 'id');
    }
    public function invoiceMaterial(){
        return $this->hasMany(QInvoiceMaterial::class,'invoice_head_id', 'id');
    }
    public function company(){
        return $this->belongsTo(Company::class,'company_id', 'id');

    }
}
