<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class Order extends Model
{
    protected $fillable = [
        'id', 'smo', 'company_id', 'provider_id', 'cat_id', 'user_id' , 'code', 'item_total', 'order_total', 'total_amount', 'check_price', 'completed','type','tech_id','scheduled_at','canceled','canceled_by','sub_cat_id'
    ];

    public $stages = ['Service request','Technician selected','Technician On the Way',
    'Maintenance In Progress', 'Spare Parts Ordered','Spare Parts Approved',
    'Reschedule the Visit','Job Completed'];

    public static function get_category($lang,$id)
    {
        $cat = Category::where('id', $id)->select($lang.'_name as name')->first();
        return $cat->name;
    }

    public function get_categories($lang,$ids)
    {
        $ids = explode(',',$ids);
        $categories = Category::whereIn('id',$ids)->select($lang.'_name as name')->get();

        return $categories;
    }


    public static function get_user($lang,$id)
    {
        $user = User::where('id', $id)->select($lang.'_name as name','phone','image','lat','lng')->first();
        return $user;
    }


    public static function get_tech($lang,$id)
    {
        $tech = Technician::where('id', $id)->select('provider_id',$lang.'_name as name','phone','image','lat','lng')->first();
        return $tech;
    }


    public static function get_tech_lang($lang,$id,$cat_id)
    {
        $tech = Technician::where('id', $id)->select($lang.'_name as name','image')->first();
        $tech['image'] = 'http://'.$_SERVER['SERVER_NAME'].'/providers/technicians/'.$tech->image;
        $tech['category']= $tech->get_category($lang,$cat_id);
        $tech['rate'] = $tech->get_all_rate($id);

        return $tech;
    }


    public static function get_tech_all($id,$cat_id)
    {
        $tech = Technician::where('id', $id)->select('en_name','ar_name','image')->first();
        $tech['image'] = 'http://'.$_SERVER['SERVER_NAME'].'/providers/technicians/'.$tech->image;

        $category= $tech->get_category_all($cat_id);
        $tech['ar_category'] = $category->ar_name;
        $tech['en_category'] = $category->en_name;
        $tech['rate'] = $tech->get_all_rate($id);

        return $tech;
    }


    public function get_type($lang,$type)
    {
        if($lang == 'ar')
        {
            if($type == 'urgent') $text = 'طلب عاجل';
            elseif($type == 're_scheduled') $text = 'إعادة زيارة';
            else $text = 'طلب مؤجل';
        }
        else
        {
            if($type == 'urgent') $text = 'Urgent Request';
            elseif($type == 're_scheduled') $text = 'Re-scheduled Request';
            elseif($type == 'emergency') $text = 'Emergency Request';
            else $text = 'Scheduled Request';
        }

        return $text;
    }


    public function get_details($order_id)
    {
        $details = OrderUserDetail::where('order_id', $order_id)->select('place', 'part', 'desc', 'images')->first();
        $desc = OrderTechDetail::where('order_id',$order_id)->select('desc')->first();
        $new_arr = [];

        if ($details != NULL)
        {
            if ($details->images != NULL) {
                $details['images'] = unserialize($details->images);

                foreach ($details->images as $image) {
                    array_push($new_arr, 'http://' . $_SERVER['SERVER_NAME'] . '/orders/' . $image);
                }
            }
        }

        $details['place'] = isset($details->place)?$details->place:'';
        $details['part'] = isset($details->part)?$details->part:'';
        $details['desc'] = isset($details->desc)?$details->desc:'';
        $details['images'] = $new_arr;
        $details['client_desc'] = isset($desc)?$desc->desc:"";

        return $details;
    }

    public function get_teamLead_report_attachment($orderId){

        // $orderTeamAttachment = OrderTeamAttachment::where('order_team_lead_report_id', $orderTeamLeadReportId)->select('image_path')->latest('created_at')->limit(1)->first();
         $orderTeamLeadReport = orderTeamLeadReport::where('order_id', $orderId)->with('orderTeamAttachment')->whereHas('orderTeamAttachment')->get();

        // $orderTeamAttachment = OrderTeamAttachment::where('order_team_lead_report_id', $orderTeamLeadReport)->select('image_path')->get();
        $new_arr = [];
        $orderImageArray = [];
        if(!empty(collect($orderTeamLeadReport)->toArray())){
            foreach(collect($orderTeamLeadReport)->toArray() as $orderTeamLeadReportDta){
                $orderdata =  !empty($orderTeamLeadReportDta['order_team_attachment']) && !empty($orderTeamLeadReportDta['order_team_attachment']['image_path'])? $orderTeamLeadReportDta['order_team_attachment']['image_path'] : NULL;
                  if ($orderdata != NULL)
            {
                     $orderImageArray['images'] = unserialize($orderdata);
                    foreach ($orderImageArray['images'] as $image) {
                        array_push($new_arr, 'http://' . $_SERVER['SERVER_NAME'] . '/orders/' . $image);
                    }
            }
            }
        }


        $orderImageArray['images'] = $new_arr;
        return collect($orderImageArray)->toArray();

        }


    public function get_items_awaiting($lang,$order_id)
    {
        $items = OrderItemUser::where('order_id', $order_id)->select('id','provider_id','item_id','status','taken')->get();
        foreach($items as $item)
        {
            $data = $item->get_item($lang,$item->provider_id,$item->item_id);
            $item['name'] = $data->name;
            $item['desc'] = $data->desc;
            $item['price'] = $data->price;
            $item['count'] = $data->count;
            $item['code'] = $data->code;
            $item['taken'] = $item->taken;
            $item['image'] = 'http://'.$_SERVER['SERVER_NAME'].'/warehouses/'.$item->get_item($lang,$item->provider_id,$item->item_id)->image;
        }

        return $items;
    }


    public function get_items($lang,$order_id)
    {
        $items = OrderTechRequest::where('order_id', $order_id)->select('provider_id','item_id','status','taken')->get();
        foreach($items as $item)
        {
            if(isset($item->item_id))
            {
                $data = $item->get_item($lang,$item->provider_id,$item->item_id);
                $item['name'] =  !empty($data) && !empty($data->name) ? $data->name : '';
                $item['desc'] =  !empty($data) && !empty($data->desc) ? $data->desc : '';
                $item['price'] = !empty($data) && !empty($data->price) ? $data->price : '';
                $item['count'] = !empty($data) && !empty($data->count) ? $data->count : '';
                $item['code'] =  !empty($data) && !empty($data->code) ? $data->code : '';
                $item['taken'] = $item->taken;
                // $item['image'] = 'http://'.$_SERVER['SERVER_NAME'].'/warehouses/'.$item->get_item($lang,$item->provider_id,$item->item_id)->image;
            }
        }
        return $items;
    }


    public function category()
    {
        return $this->belongsTo(Category::class,'cat_id')->withTrashed();
    }
    public function services()
    {
        return $this->belongsTo(Category::class,'cat_id')->withTrashed();
    }
    public function orderTeamLeadReport(){

        return $this->hasMany(OrderTeamLeadReport::class,'order_id');
    }

    public function getThirdCat($cat_id)
    {
        return Category::where('parent_id', $cat_id)->get();
    }


    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }


    public function tech()
    {
        return $this->belongsTo(Technician::class,'tech_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class,'provider_id');
    }

    public function order_expense()
    {
        return $this->hasOne(OrderExpense::class,'order_id');
    }


    public function items()
    {
        return $this->hasMany(OrderTechRequest::class,'order_id');
    }

    public function details()
    {
        return $this->hasMany(OrderTechDetail::class,'order_id');
    }

    public function track()
    {
        return $this->hasMany(OrderTracking::class,'order_id');
    }
    public function sla()
    {
        return $this->hasMany(Sla::class, 'sub_category_id', 'sub_cat_id');
    }
    public function scopeWithWhereHas($query, $relation, $constraint){
        return $query->whereHas($relation, $constraint)
        ->with([$relation => $constraint]);
       }

    public function rate()
    {
        return $this->hasOne(OrderRate::class,'order_id');
    }

    public function TechReportImages()
    {
        $report = DB::table('order_tech_details')->where('order_id', $this->id)->whereNotNull('before_images')->first();
        $data['before'] = unserialize($report->before_images);
        $data['after']  = unserialize($report->after_images);
        return $data;
    }


    public function get_fee($provider_id,$cat_id)
    {
        $fee = ProviderCategoryFee::where('provider_id', $provider_id)->where('cat_id', $cat_id)->select('fee')->first()->fee;
        return $fee;
    }

//    public function setTotalAmountAttribute()
//    {
//        return $this->attributes['order_total'] + $this->attributes['item_total'];
//    }


    public function user_details()
    {
        return $this->hasOne(OrderUserDetail::class,'order_id');
    }


    public function tech_details()
    {
        return $this->hasOne(OrderTechDetail::class,'order_id');
    }


    public function get_steps($lang,$id)
    {
        $order = Order::where('id', $id)->select('type','completed','tech_id','scheduled_at')->first();

        $arr = [];

        if($order->type == 'urgent')
        {
            if($lang == 'ar')
            {
                $arr[0]['text'] = 'طلب الخدمة';
                $arr[0]['flag'] = 1;

                $arr[1]['text'] = 'تم إختيار الفني';
                $arr[1]['flag'] = 1;

                $arr[2]['text'] = 'الفني في الطريق';
                $arr[2]['flag'] = 1;

                $arr[3]['text'] = 'تم الإنتهاء من العمل';
                $arr[3]['flag'] = $order->completed;

            }
            else
            {
                $arr[0]['text'] = 'Service Request';
                $arr[0]['flag'] = 1;

                $arr[1]['text'] = 'Technician selected';
                $arr[1]['flag'] = 1;

                $arr[2]['text'] = 'Technician on the way';
                $arr[2]['flag'] = 1;

                $arr[3]['text'] = 'Service Completed';
                $arr[3]['flag'] = $order->completed;
            }

        }
        elseif($order->type == 'scheduled')
        {
            if($order->tech_id != null) $tech = 1;
            else $tech = 0;

            if($lang == 'ar')
            {
                $arr[0]['text'] = 'طلب الخدمة';
                $arr[0]['flag'] = 1;

                $arr[1]['text'] = 'تم إختيار الفني';
                $arr[1]['flag'] = $tech;

                $arr[2]['text'] = 'الفني في الطريق';
                $arr[2]['flag'] = $tech;

                $arr[3]['text'] = 'تم الإنتهاء من العمل';
                $arr[3]['flag'] = $order->completed;

            }
            else
            {
                $arr[0]['text'] = 'Service Request';
                $arr[0]['flag'] = 1;

                $arr[1]['text'] = 'Technician selected';
                $arr[1]['flag'] = 0;

                $arr[2]['text'] = 'Technician on the way';
                $arr[2]['flag'] = 0;

                $arr[3]['text'] = 'Service Completed';
                $arr[3]['flag'] = 0;
            }
        }
        else
        {
            $items = OrderTechRequest::where('order_id', $order->id)->pluck('status');
            if(in_array('awaiting',$items->toArray()) == false) $status = 1;
            else $status = 0;

            if($order->scheduled_at != null) $date = 1;
            else $date = 0;

            if($order->tech_id != null) $tech = 1;
            else $tech = 0;

            if($lang == 'ar')
            {
                $arr[0]['text'] = 'طلب الخدمة';
                $arr[0]['flag'] = 1;

                $arr[1]['text'] = 'تم إختيار الفني';
                $arr[1]['flag'] = 1;

                $arr[2]['text'] = 'الفني في الطريق';
                $arr[2]['flag'] = 1;

                $arr[3]['text'] = 'القيام بالصيانة';
                $arr[3]['flag'] = 1;

                $arr[4]['text'] = 'تم طلب قطع للصيانة';
                $arr[4]['flag'] = 1;

                $arr[5]['text'] = 'الموافقة علي القطع';
                $arr[5]['flag'] = $status;

                $arr[6]['text'] = 'تحديد موعد إعادة الزيارة';
                $arr[6]['flag'] = $date;

                $arr[7]['text'] = 'تم إختيار الفني';
                $arr[7]['flag'] = $tech;

                $arr[8]['text'] = 'الفني في الطريق';
                $arr[8]['flag'] = $tech;

                $arr[9]['text'] = 'القيام بالصيانة';
                $arr[9]['flag'] = $tech;

                $arr[10]['text'] = 'تم الإنتهاء من العمل';
                $arr[10]['flag'] = $order->completed;
            }
            else
            {
                $arr[0]['text'] = 'Service Request';
                $arr[0]['flag'] = 1;

                $arr[1]['text'] = 'Technician selected';
                $arr[1]['flag'] = 1;

                $arr[2]['text'] = 'Technician on the way';
                $arr[2]['flag'] = 1;

                $arr[3]['text'] = 'Maintenance On going';
                $arr[3]['flag'] = 1;

                $arr[4]['text'] = 'Maintenance parts requested';
                $arr[4]['flag'] = 1;

                $arr[5]['text'] = 'Confirming parts requests';
                $arr[5]['flag'] = $status;

                $arr[6]['text'] = 'set a second visit date';
                $arr[6]['flag'] = $date;

                $arr[7]['text'] = 'Technician selected';
                $arr[7]['flag'] = $tech;

                $arr[8]['text'] = 'Technician on the way';
                $arr[8]['flag'] = $tech;

                $arr[9]['text'] = 'Maintenance On going';
                $arr[9]['flag'] = $tech;

                $arr[10]['text'] = 'Service Completed';
                $arr[10]['flag'] = $order->completed;
            }

        }

        return $arr;
    }


    public function get_user_location($user_id)
    {
        $user = UserLocation::where('user_id', $user_id)->first();

        $camp = isset($user->camp) ? $user->camp : '';
        $street = isset($user->street) ? $user->street : '';
        $plot_no = isset($user->plot_no) ? $user->plot_no : '';
        $block_no = isset($user->block_no) ?  $user->block_no : '';
        $building_no = isset($user->building_no) ? $user->building_no : '';
        $apartment_no = isset($user->apartment_no) ? $user->apartment_no : '';

//        $location = $camp . $street . $plot_no . $block_no . $building_no . $apartment_no;

        $arr['camp'] = $camp;
        $arr['street'] = $street;
        $arr['plot_no'] = $plot_no;
        $arr['block_no'] = $block_no;
        $arr['building_no'] = $building_no;
        $arr['apartment_no'] = $apartment_no;
        $arr['lat'] = isset($user->lat) ? $user->lat : '0';
        $arr['lng'] = isset($user->lng) ? $user->lng : '0';

        return $arr;
    }


    public function get_user_location_admin($user_id)
    {
        $user = User::find($user_id);

        $camp = isset($user->camp) ? $user->camp : '';
        $street = isset($user->street) ? $user->street : '';
        $plot_no = isset($user->plot_no) ? $user->plot_no : '';
        $block_no = isset($user->block_no) ?  $user->block_no : '';
        $building_no = isset($user->building_no) ? $user->building_no : '';
        $apartment_no = isset($user->apartment_no) ? $user->apartment_no : '';
        $house_type = isset($user->house_type) ? $user->house_type : '';

//        $location = $camp . $street . $plot_no . $block_no . $building_no . $apartment_no;

        $arr['Camp'] = $camp;
        $arr['Street'] = $street;
        $arr['Plot No.'] = $plot_no;
        $arr['Block No.'] = $block_no;
        $arr['Building No.'] = $building_no;
        $arr['Apartment No.'] = $apartment_no;
        $arr['House Type.'] = $house_type;

        return $arr;
    }

    public function order_tech_request(){
        return $this->hasMany(OrderTechRequest::class,'order_id');
    }
    public function get_items_total($id)
    {
        $ids = OrderTechRequest::where('order_id', $id)->where('status','confirmed')->pluck('item_id');
        $items = Warehouse::whereIn('id', $ids)->pluck('price');

        return $items->sum();
    }

    public function get_items_total2($provider_id,$id)
    {
        $ids = OrderTechRequest::where('order_id', $id)->where('status','confirmed')->pluck('item_id');
        $items = DB::table($provider_id.'_warehouse_parts')->whereIn('id', $ids)->pluck('price');

        return $items->sum();
    }


    public function get_cat_fee($order_id)
    {
        $order = Order::find($order_id);
        if(isset($order->provider_id) && isset($order->cat_id))
        {
            if($order->service_type == 1)
            {
                if($order->type == 'urgent')
                {

                    $fee = ProviderCategoryFee::where('provider_id', $order->provider_id)->where('cat_id', $order->cat_id)->select('urgent_fee')->first()->urgent_fee;
                    return $fee;

                }else{

                    $fee = ProviderCategoryFee::where('provider_id', $order->provider_id)->where('cat_id', $order->cat_id)->select('scheduled_fee')->first()->scheduled_fee;
                    return $fee;
                }

            }else{

                $explode = explode(',',$order->cat_id);

                if($order->type == 'urgent')
                {

                    $fee = ProviderCategoryFee::where('provider_id', $order->provider_id)->whereIn('cat_id', $explode)->select('urgent_fee')->first()->urgent_fee;
                    return $fee;

                }else{

                    $fee = ProviderCategoryFee::where('provider_id', $order->provider_id)->whereIn('cat_id', $explode)->select('scheduled_fee')->first()->scheduled_fee;
                    return $fee;
                }
            }
        }
        else{
            $fee = 0;
            return $fee;
        }
    }

    public function check_search($type,$monthly_orders = null, $yearly_orders = null,$all= null)
    {
        if($type == 'monthly_orders_count')
        {
            $show_orders = $monthly_orders;
        }
        elseif($type == 'yearly_orders_count')
        {
            $show_orders = $yearly_orders;
        }
        elseif($type == 'monthly_open')
        {
            $show_orders = $monthly_orders->where('completed', 0)->where('canceled', 0);
        }
        elseif($type == 'yearly_open')
        {
            $show_orders = $yearly_orders->where('completed', 0)->where('canceled', 0);
        }
        elseif($type == 'monthly_closed')
        {
            $show_orders = $monthly_orders->where('completed', 1);
        }
        elseif($type == 'yearly_closed')
        {
            $show_orders = $yearly_orders->where('completed', 1);
        }
        elseif($type == 'monthly_canceled')
        {
            $show_orders = $monthly_orders->where('canceled', 1);
        }
        elseif($type == 'yearly_canceled')
        {
            $show_orders = $yearly_orders->where('canceled', 1);
        }elseif($type == 'monthly_parts_orders_count')
        {
            $show_orders = $monthly_orders->where('type','re_scheduled');
        }elseif($type == 'yearly_parts_orders_count')
        {
            $show_orders = $yearly_orders->where('type','re_scheduled');
        }elseif($type == 'all')
        {
            $show_orders = $all;
        }

        return $show_orders;
    }

public function search(
    $orders,
    $search=null,
    $company_id,
    $provider_id,
    $select_company=null,
    $select_sub_company=null,
    $select_from=null,
    $select_to=null,
    $select_main_cats=null,
    $select_sub_cats=null,
    $select_price_range=null,
    $select_service_type=null,
    $select_third_cats=null,
    $select_order_type=null,
    $select_provider_name=null,
    $select_order_status=null,
    $items_status=null
)
{
    if ($search != '') {
        $user = User::where('company_id', $company_id)->where(function ($q) use ($search) {
            $q->where('en_name', 'like', '%' . $search . '%');
            $q->orWhere('ar_name', 'like', '%' . $search . '%');
        }
        )->first();

        if (is_int($provider_id) == false && is_string($provider_id) == false) {
            $tech = Technician::whereIn('provider_id', $provider_id)->where(function ($q) use ($search) {
                $q->where('en_name', 'like', '%' . $search . '%');
                $q->orWhere('ar_name', 'like', '%' . $search . '%');
            }
            )->first();
        } else {
            $tech = Technician::where('provider_id', $provider_id)->where(function ($q) use ($search) {
                $q->where('en_name', 'like', '%' . $search . '%');
                $q->orWhere('ar_name', 'like', '%' . $search . '%');
            }
            )->first();
        }

        if (is_numeric($search)) {
            $orders = $orders->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%');
                $q->orWhere('smo', 'like', '%' . $search . '%');
            }
            );
        } else {
            if ($user) {
                $orders = $orders->where('user_id', $user->id);
            }
            if ($tech) {
                $orders = $orders->where('tech_id', $tech->id);
            }
        }

        if ($select_company) {
            $orders = $orders->whereIn('company_id', $select_company);
        }

        if ($select_sub_company) {
            $get_sub = User::where('company_id', $company_id)->whereIn('sub_company_id', $select_sub_company)->pluck('id');
            $orders = $orders->whereIn('user_id', $get_sub);
        }

        if ($select_from) {
            $orders = $orders->where('created_at', '>=', $select_from);
        }

        if ($select_to) {
            $orders = $orders->where('created_at', '<=', Carbon::parse($select_to)->addDays(1));
        }

        if ($select_main_cats) {
            $sub_cat = Category::whereIn('parent_id', $select_main_cats)->pluck('id');
            $orders = $orders->whereIn('cat_id', $sub_cat);
        }

        if ($select_sub_cats) {
            $orders = $orders->whereIn('cat_id', $select_sub_cats);
        }

        if ($select_third_cats) {
            $cat = Category::whereIn('id', $select_third_cats)->pluck('parent_id');
            $orders = $orders->whereIn('cat_id', $cat);
        }

        if ($select_price_range) {
            $price_range = explode(';', $select_price_range);
            $orders = $orders->where('total_amount', '>=', $price_range[0])->where('total_amount', '<=', $price_range[1]);
        }
        if ($select_service_type) {
            $orders = $orders->whereIn('service_type', $select_service_type);
        }
        if ($select_order_type) {
            $orders = $orders->whereIn('type', $select_order_type);
        }
        if ($select_provider_name) {
            $orders = $orders->whereIn('provider_id', $select_provider_name);
        }
        if ($select_order_status) {
            if ($select_order_status == 'open') {
                $orders = $orders->where('completed', 0)->where('canceled', 0);
            } elseif ($select_order_status == 'complete') {
                $orders = $orders->where('completed', 1);
            } else {
                $orders = $orders->where('canceled', 1);
            }
        }
        if($items_status){
            if($items_status == 'no'){
              $orders = $orders->whereIn('id', self::ordersRequireUserApproval());
            }elseif ($items_status == 'user') {
              $orders = $orders->whereIn('id', self::ordersRequireAdminApproval());
            }elseif ($items_status == 'admin') {
              $orders = $orders->whereNotIn('id', self::ordersRequireApproval());
            }
        }

        $bills_export = $orders->get();
        $orders = $orders->latest()->paginate(50);

    } else {
        if ($select_company) {
            $orders = $orders->whereIn('company_id', $select_company);
        }

        if ($select_sub_company) {
            $get_sub = User::where('company_id', $company_id)->whereIn('sub_company_id', $select_sub_company)->pluck('id');
            $orders = $orders->whereIn('user_id', $get_sub);
        }

        if ($select_from) {
            $orders = $orders->where('created_at', '>=', $select_from);
        }

        if ($select_to) {
            $orders = $orders->where('created_at', '<=', Carbon::parse($select_to)->addDays(1));
        }

        if ($select_main_cats) {
            $sub_cat = Category::whereIn('parent_id', $select_main_cats)->pluck('id');
            $orders = $orders->whereIn('cat_id', $sub_cat);
        }

        if ($select_sub_cats) {
            $orders = $orders->whereIn('cat_id', $select_sub_cats);
        }

        if ($select_third_cats) {
            $cat = Category::whereIn('id', $select_third_cats)->pluck('parent_id');
            $orders = $orders->whereIn('cat_id', $cat);
        }
        if ($select_price_range) {
            $price_range = explode(';', $select_price_range);
            $orders = $orders->where('total_amount', '>=', $price_range[0])->where('total_amount', '<=', $price_range[1]);
        }
        if ($select_service_type) {
            $orders = $orders->whereIn('service_type', $select_service_type);
        }
        if ($select_order_type) {
            $orders = $orders->whereIn('type', $select_order_type);
        }
        if ($select_provider_name) {
            $orders = $orders->whereIn('provider_id', $select_provider_name);
        }
        if ($select_order_status) {
            if ($select_order_status == 'open') {
                $orders = $orders->where('completed', 0)->where('canceled', 0);
            } elseif ($select_order_status == 'complete') {
                $orders = $orders->where('completed', 1);
            } else {
                $orders = $orders->where('completed', 0)->where('canceled', 1);
            }
        }
        if($items_status){
            if($items_status == 'user'){
              $orders = $orders->whereIn('id', self::ordersRequireUserApproval());
            }elseif ($items_status == 'admin') {
              $orders = $orders->whereIn('id', self::ordersRequireAdminApproval());
            }elseif ($items_status == 'no') {
              $orders = $orders->whereNotIn('id', self::ordersRequireApproval());
            }
        }

        $bills_export = $orders->get();
        $orders = $orders->latest()->paginate(50);

    }

    return ['orders' => $orders, 'bills_export' => $bills_export];
    }

    // ============================================================

    public static function ordersRequireAdminApproval()
    {
        return
        DB::table('item_request_states')
        ->rightJoin('order_tech_requests', 'item_request_states.request_id', '=', 'order_tech_requests.id')
        ->where('item_request_states.status', 'awaiting')
        ->distinct('order_tech_requests.order_id')
        ->pluck('order_tech_requests.order_id')
        ->toArray();
    }

    public static function ordersRequireUserApproval()
    {
        return
        DB::table('order_item_users')
        ->where('status', 'awaiting')
        ->distinct('order_id')
        ->pluck('order_id')
        ->toArray();
    }

    public static function ordersRequireApproval()
    {
        $a1 = self::ordersRequireAdminApproval();
        $a2 = self::ordersRequireUserApproval();
        return array_merge($a1, $a2);
    }

    public function isUserApprovalRequired()
    {
        $requests =
        DB::table('order_item_users')
        ->where('order_id', $this->id)
        ->where('status', 'awaiting')
        ->count();
        return ($requests > 0) ? true : false;
    }

    public function isAdminApprovalRequired()
    {
        $requests =
        DB::table('item_request_states')
        ->rightJoin('order_tech_requests', 'item_request_states.request_id', '=', 'order_tech_requests.id')
        ->where('order_tech_requests.order_id', $this->id)
        ->where('item_request_states.status', 'awaiting')
        ->count();
        return ($requests > 0) ? true : false;
    }

    public function isApprovalRequired()
    {
        return
        $this->isUserApprovalRequired() || $this->isAdminApprovalRequired();
    }

    public function ItemsRequireUserApproval()
    {
        $items_table = $this->provider_id ? $this->provider_id . '_warehouse_parts' : '1_warehouse_parts';
        return
        OrderItemUser::select([
          'order_item_users.id',
          $items_table . '.en_name',
          'order_item_users.taken',
          'order_item_users.status',
          $items_table . '.image',
          $items_table . '.price',
          $items_table . '.en_desc'
        ])
        ->leftJoin($items_table, 'order_item_users.item_id', '=', $items_table.'.id')
        ->where('order_item_users.order_id', $this->id)
        ->get();



    }

    public static function approveItemForUser($request_id)
    {
    }

    public static function approveItemForAdmin($request_id)
    {
    }

    public static function approveItemForUserAndAdmin($request_id){
    }

    public function track_order($lang)
    {
        if($lang == 'ar'){
            $descriptions = [0 => 'تم إستلام الطلب.', 1 => 'تم إختيار الفني سيقوم بحل المشكلة.',
                2 => 'الفني في الطريق.', 3 => 'بدأ الفني في حل مشكلتك.',
                4 => 'يريد الفني بعض قطع الغيار لإكمال إصلاح مشكلتك.', 5 => 'وافقت الشركة على قطع الغيار المطلوبة.',
                6 => 'يريد الفني إعادة الزيارة ليتمكن من وضع قطع غيار جديدة.', 7 => 'تم الانتهاء من المهمة.'];
        }else{
            $descriptions = [0 => 'We have received your order', 1 => 'You have chosen the technician that will solve your problem',
                2 => 'The technician made the move and on his way to you', 3=> 'The technician has started working on your problem',
                4 => 'The technician wants some spare parts to complete the repair of your problem', 5 => 'The company has agreed to the required spare parts',
                6 => 'The technician wants to return the visit so that he can put new spare parts', 7 => 'Job has been completed'];
        }
        $classes = [
            0 => 'http://admin.qreebs.com/tracking/Service_request.jfif',
            1 => 'http://admin.qreebs.com/tracking/Technician_selected.jfif',
            2 => 'http://admin.qreebs.com/tracking/Technician_on_the_way.jfif',
            3 => 'http://admin.qreebs.com/tracking/Maintenance_on_progress.jfif',
            4 => 'http://admin.qreebs.com/tracking/Spare_parts_ordered.jfif',
            5 => 'http://admin.qreebs.com/tracking/Spare_parts_approved.jfif',
            6 => 'http://admin.qreebs.com/tracking/Reschedule_the_visit.jfif',
            7 => 'http://admin.qreebs.com/tracking/Job_completed.jfif'
        ];
        $merged = collect($descriptions)->zip($classes)->transform(function ($values) {
            return [
                'description' => $values[0],
                'image' => $values[1],
            ];
        });

        return $merged->all();
    }
}
