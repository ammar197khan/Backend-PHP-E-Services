<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DB;

class Company extends Model
{

    use SoftDeletes;

    protected $fillable =
    [
        'address_id','interest_fee','ar_name','en_name','ar_desc','en_desc','email','phones','logo','item_limit','order_process_id', 'vat_registration', 'vat',
        'cr_upload', 'vat_upload', 'agreement_upload', 'po_box', 'en_organization_name', 'ar_organization_name'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'company_id');
    }

    public function orders_urgent()
    {
        return $this->hasMany(Order::class, 'company_id')->where('type', 'urgent');
    }

    public function orders_scheduled()
    {
        return $this->hasMany(Order::class, 'company_id')->where('type', 'scheduled');
    }


    public function subscriptions()
    {
        return $this->hasOne(CompanySubscription::class, 'company_id');
    }

    public function admin()
    {
        return $this->hasOne(CompanyAdmin::class,'company_id');
    }
    public function orderProcessType(){

        return $this->belongsTo(OrderProcessType::class,'order_process_id');
    }

    public function BillToProvider($provider_id, $from = null, $to = null)
    {

        // ORDERS INVOICE
        $data['elements'] =
        DB::table('orders')->select(
              'services.en_name as service',
              DB::raw("sum(CASE WHEN orders.type = 'urgent' THEN '1' ELSE 0 END) as urgent_count"),
              DB::raw("sum(CASE WHEN orders.type = 'scheduled' THEN '1' ELSE 0 END) as scheduled_count"),
              DB::raw("sum(CASE WHEN orders.type = 're_scheduled' THEN '1' ELSE 0 END) as rescheduled_count"),

              DB::raw("sum(CASE WHEN orders.type = 'urgent' THEN orders.order_total ELSE 0 END) as urgent_orders_amount"),
              DB::raw("sum(CASE WHEN orders.type = 'scheduled' THEN orders.order_total ELSE 0 END) as scheduled_orders_amount"),
              DB::raw("sum(CASE WHEN orders.type = 're_scheduled' THEN orders.order_total ELSE 0 END) as rescheduled_orders_amount"),

              DB::raw("sum(CASE WHEN orders.type = 'urgent' THEN orders.item_total ELSE 0 END) as urgent_items_amount"),
              DB::raw("sum(CASE WHEN orders.type = 'scheduled' THEN orders.item_total ELSE 0 END) as scheduled_items_amount"),
              DB::raw("sum(CASE WHEN orders.type = 're_scheduled' THEN orders.item_total ELSE 0 END) as rescheduled_items_amount"),

              DB::raw("sum(CASE WHEN orders.type != 'canceled' THEN '1' ELSE 0 END) as total_count"),
              DB::raw("sum(CASE WHEN orders.type != 'canceled' THEN orders.item_total ELSE 0 END) as total_items_amount"),
              DB::raw("sum(CASE WHEN orders.type != 'canceled' THEN orders.order_total ELSE 0 END) as total_orders_amount"),
              DB::raw("sum(CASE WHEN orders.type != 'canceled' THEN orders.total_amount ELSE 0 END) as total")
        )
        ->join('categories', 'orders.cat_id', '=', 'categories.id')
        ->join('categories as services', 'categories.parent_id', '=', 'services.id')
        ->where('orders.company_id', $this->id)
        ->where('orders.provider_id', $provider_id)
        ->where('orders.completed', 1)
        ->where('orders.created_at', '>=' ,isset($from) ? $from : Carbon::parse('2015-1-1'))
        ->where('orders.created_at','<=', isset($to) ? Carbon::parse($to)->addDays(1) : Carbon::now())
        ->groupBy('services.en_name')
        ->get();

        $data['orders_vat']          = 5;
        $data['materials_vat']       = 5;
        $data['total_count']         = $data['elements']->sum('total_count');
        $data['total_orders_amount'] = $data['elements']->sum('total_orders_amount');
        $data['total_items_amount']  = $data['elements']->sum('total_items_amount');
        $data['total']               = $data['elements']->sum('total');
        return $data;
    }

    public function setLogo($value)
    {
        if ($value){
            // $name = unique_file($value->getClientOriginalName());
            // $value->move(base_path().'/public/companies/logo',$name);
            // $this->attributes['logo'] = $name;
            $this->attributes['logo'] = $value;
        }
    }
}
