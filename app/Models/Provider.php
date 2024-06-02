<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Database\Eloquent\Model;
use DB;

class Provider extends Model implements Authenticatable
{
    use AuthenticableTrait;

    protected $fillable = [
        'address_id',
        'type',
        'interest_fee',
        'warehouse_fee',
        'ar_name',
        'en_name',
        'ar_desc',
        'en_desc',
        'email',
        'phones',
        'logo',
        'username',
        'password',
        'commission_categories',
        'cr_upload',
        'vat_upload',
        'agreement_upload',
        'vat_val',
        'vat_registration',
        'ar_organization_name',
        'en_organization_name',
        'po_box'

    ];

    protected $casts = ['commission_categories' => 'array'];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function technicians()
    {
        return $this->hasMany(Technician::class, 'provider_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'provider_id');
    }

    public function orders_with_date($from,$to)
    {
        return $this->hasMany(Order::class, 'provider_id')->where('created_at','>=',$from)
        ->where('created_at','<=',Carbon::parse($to)->addDays(1));
    }

    public function orders_urgent()
    {
        return $this->hasMany(Order::class, 'provider_id')->where('type', 'urgent');
    }

    public function orders_scheduled()
    {
        return $this->hasMany(Order::class, 'provider_id')->where('type', 'scheduled');
    }

    public function cat_fees()
    {
        return $this->hasMany(ProviderCategoryFee::class, 'provider_id');
    }

    public function admin()
    {
        return $this->hasOne(ProviderAdmin::class,'provider_id');
    }

    public function getCommissionCategoriesAttribute($value)
    {
        return json_decode($value, true);
    }

    public static function parseCommissionCategories($fromArray, $toArray, $valueArray)
    {
        $commissions = NULL;
        for ($i=0; $i < count($fromArray); $i++) {
          $key = (int)$fromArray[$i] . ':' . (int)$toArray[$i];
          $value = (int)$valueArray[$i];
          $commissions.= $i == 0 ? '{' : '';
          $commissions.= "\"$key\":$value";
          $commissions.= $i == (count($fromArray) -1) ? '}' : ',';
        }
        return ($commissions);
        return json_decode($commissions);
    }

    public function commissionSegmentArray($ordersCount)
    {
        if($this->type != 'categorized') return null;
        if(!empty($this->commission_categories)){
        foreach ($this->commission_categories as $segment => $commission) {
            $segmentArray = explode(':', $segment);
            $from = $segmentArray[0];
            $to   = $segmentArray[1];
            if($ordersCount > $from && $ordersCount < $to) {
              return [
                'segment'    => $segment ,
                'commission' => $commission,
              ];
            }
        }
    }
         return Null;
    }

    public function commissionValue($ordersCount = 0)
    {
        if($this->type == 'cash' || $this->type == 'percentage') {
            return $this->interest_fee;
        }
        return !empty($this->commissionSegmentArray($ordersCount))? $this->commissionSegmentArray($ordersCount)['commission']: 0;
    }




   public function BillToQreeb($from = null, $to = null)
   {
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
       ->where('orders.provider_id', $this->id)
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

    public function BillProviderToQreeb($provider_id, $from = null, $to = null,$main_cats = null, $provider_name = null)
    {

        // ORDERS INVOICE
        $data['elements'] = DB::table('orders')
            // ->select('categories.en_name', 'orders.scheduled_at', 'orders.item_total', 'orders.order_total', 'orders.total_amount')
            ->select(
                'services.en_name as service',
                DB::raw("sum(CASE WHEN orders.type = 'urgent' THEN '1' ELSE 0 END) as urgent_count"),
                DB::raw("sum(CASE WHEN orders.type = 'scheduled' THEN '1' ELSE 0 END) as scheduled_count"),
                DB::raw("sum(CASE WHEN orders.type = 're_scheduled' THEN '1' ELSE 0 END) as re_scheduled_count"),

                DB::raw("sum(CASE WHEN orders.type = 'urgent' THEN orders.order_total + order_expenses.cost ELSE 0 END) as urgent_orders_amount"),
                DB::raw("sum(CASE WHEN orders.type = 'scheduled' THEN orders.order_total + order_expenses.cost ELSE 0 END) as scheduled_orders_amount"),
                DB::raw("sum(CASE WHEN orders.type = 're_scheduled' THEN orders.order_total + order_expenses.cost ELSE 0 END) as re_scheduled_orders_amount"),

                DB::raw("sum(CASE WHEN orders.type = 'urgent' THEN orders.item_total ELSE 0 END) as urgent_item_amount"),
                DB::raw("sum(CASE WHEN orders.type = 'scheduled' THEN orders.item_total ELSE 0 END) as scheduled_item_amount"),
                DB::raw("sum(CASE WHEN orders.type = 're_scheduled' THEN orders.item_total ELSE 0 END) as re_scheduled_item_amount"),

                DB::raw("sum(CASE WHEN orders.type != 'canceled' THEN '1' ELSE 0 END) as total_count"),
                DB::raw("sum(CASE WHEN orders.type != 'canceled' THEN orders.item_total ELSE 0 END) as total_items_amount"),
                DB::raw("sum(CASE WHEN orders.type != 'canceled' THEN orders.order_total + order_expenses.cost ELSE 0 END) as total_orders_amount"),
                DB::raw("sum(CASE WHEN orders.type != 'canceled' THEN orders.total_amount ELSE 0 END) as total"),

                DB::raw("sum(orders.item_total) as total_item")
            )
            ->join('categories', 'orders.cat_id', '=', 'categories.id')
            ->join('categories as services', 'categories.parent_id', '=', 'services.id')
            ->join('order_expenses', 'order_expenses.order_id', '=', 'orders.id')
            ->where('orders.provider_id', $provider_id)
            ->where('orders.completed', 1)
            ->groupBy('services.en_name');

            if($from != null && $to != null)
            {
                $data['elements'] = $data['elements']->where('orders.created_at', '>=' ,$from)
                    ->where('orders.created_at','<=', Carbon::parse($to)->addDays(1));
            }
            if($main_cats != null)
            {
                $sub_cat = Category::whereIn('parent_id', $main_cats)->pluck('id');
                $data['elements'] = $data['elements']->whereIn('cat_id', $sub_cat);
            }
            if($provider_name != null)
            {
                $data['elements'] = $data['elements']->whereIn('provider_id', $provider_name);;
            }

            $data['elements'] = $data['elements']->get();

            $data['orders_vat']          = 5;
            $data['total_count']         = $data['elements']->sum('total_count');
            $data['total_orders_amount'] = $data['elements']->sum('total_orders_amount');
            $data['total_items_amount']  = $data['elements']->sum('total_items_amount');
        $data['total']               = $data['elements']->sum('total');
            return $data;
    }

    public function setLogo($value)
    {
        if ($value){
            $name = unique_file($value->getClientOriginalName());
            $value->move(base_path().'/public/providers/logo',$name);
            $this->attributes['logo'] = $name;
        }
    }
}
