<?php

use App\Models\OrderProcessType;
use App\Models\User;
use App\Models\Company;
use App\Models\Provider;
use Illuminate\Support\Carbon;
use App\Models\ProviderCategoryFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
function unique_file($fileName)
{
    $fileName = str_replace(' ','-',$fileName);
    return time() . uniqid().'-'.$fileName;
}


function admin()
{
    return Auth::guard('admin')->user();
}



function provider()
{
    return Auth::guard('provider')->user();
}


function company()
{
    return Auth::guard('company')->user();
}



function success()
{
    return 'success';
}


function not_active()
{
    return 'not_active';
}

function suspended()
{
    return 'suspended';
}


function error()
{
    return 'error';
}


function failed()
{
    return 'failed';
}



function msg($request,$status,$key)
{
    $msg['status'] = $status;
    $msg['msg'] = Config::get('response.'.$key.'.'.$request->header('lang'));

    return $msg;
}

function msg_data($request,$status,$key,$data)
{
    $msg['status'] = $status;
    $msg['msg'] = Config::get('response.'.$key.'.'.$request->header('lang'));
    $msg['data'] = $data;

    return $msg;
}

function api_response($request,$status,$key,$data = null)
{
    $msg['status'] = $status;
    $msg['msg'] = Config::get('response.'.$key.'.'.$request->header('lang'));
    if($date) {
        $msg['data'] = $data;
    }
    return $msg;
}

function get_auth_guard()
{
    $path = request()->route()->getPrefix();

    if($path == '/admin' xor $path == 'admin/settings') return Auth::guard('admin')->user();
    elseif($path == '/provider') return Auth::guard('provider')->user();
    elseif($path == '/company') return Auth::guard('company')->user();
}

function active_guard()
{
    $guard_list = array_keys(config('auth.guards'));
    $prefix = substr(request()->route()->getPrefix(), 1);
    return in_array($prefix, $guard_list) ? $prefix : null;
}
function billToProvider($providerId = null, $companyId = null, $month = null, $year = null ){
    // dd($month, $year);
    $provider = DB::table('providers')->where('id', $providerId)->first();
    $company  = DB::table('companies')->where('id', $companyId)->first();

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
    ->where('orders.company_id', $companyId)
    ->where('orders.provider_id', $providerId)
    ->where('orders.completed', 1)
    ->whereYear('orders.created_at', '=', $year)
    ->whereMonth('orders.created_at', '=', $month)
    ->groupBy('services.en_name')
    ->get();
    $data['company']             = $company;
    $data['orders_vat']          = $provider->vat;
    $data['vat_registration']       = $provider->vat_registration;
    $data['total_count']         = $data['elements']->sum('total_count');
    $data['total_orders_amount'] = $data['elements']->sum('total_orders_amount');
    $data['total_items_amount']  = $data['elements']->sum('total_items_amount');
    $data['total']               = $data['elements']->sum('total');
    $data['vat_total']           = $data['elements']->sum('total') +  $data['elements']->sum('total') * $provider->vat/ 100;
    $data['status']              = 'generate invoice';
    $data['is_paid']              = 'not-paid';
    // {{ $company->BillToProvider(provider()->provider_id, $from, $to)['total'] + $company->BillToProvider(provider()->provider_id, $from, $to)['total'] * $company->BillToProvider(provider()->provider_id, $from, $to)['orders_vat']/100 }}
    return $data;
}
function Order_Tracking_Statuses($search, $type){
    $result = null;
    if(in_array(strval($search) , config::get('enums.Order_Tracking_Statuses'))){
        if( $type == 'value'){
            $orderTrackingStatuses = config::get('enums.Order_Tracking_Statuses');
            $search = array_search(strval($search),$orderTrackingStatuses,true);
            $result = $orderTrackingStatuses[$search];
            return $result;
        }elseif($type == 'key'){
            $orderTrackingStatuses = config::get('enums.Order_Tracking_Statuses');
            $result = array_search($search,$orderTrackingStatuses,true);
        }
    }
    return $result;
}
function Order_Tracking_Statuses_Arabic($search, $type){
    $result = null;

    if(array_key_exists(strval($search) , config::get('enums.Order_Tracking_Statuses_Arabic'))){
        if( $type == 'value'){
            $orderTrackingStatuses = config::get('enums.Order_Tracking_Statuses_Arabic');
            $result = $orderTrackingStatuses[$search];
        }elseif($type == 'key'){
            $orderTrackingStatuses = config::get('enums.Order_Tracking_Statuses_Arabic');
            $result = $orderTrackingStatuses[$search];
        }
    }

    return $result;
}
function getAllTracks($processType){
            $show_all = [];
          $orderProcessSuper =  OrderProcessType::where('id', 1)->pluck('name')->first();
          $orderProcessTech =  OrderProcessType::where('id', 2)->pluck('name')->first();
                if($processType == $orderProcessTech ){
                $show_all = ['Service request','Technician selected','Technician On the Way',
                'Maintenance In Progress', 'Spare Parts Ordered','Spare Parts Approved',
                'Reschedule the Visit','Job Completed'];


            }elseif($processType ==  $orderProcessSuper){
                $show_all = ['Assessor Supervisor selected','Assigned to Team Lead','Assessor On the Way',
                'Assessment in Progress','Assessment Report Submitted','Assigned to Technician','Service Request Rejected','Technician On the Way',
                'Maintenance In Progress', 'Spare Parts Ordered',  'Spare Parts Approved','Reschedule the Visit','Job Completed'
            ];
            }
            return $show_all;
}
function getUserDetailById($userID){
    $user = null;
    if(!empty($userID)){
        $user =
        User::where('id', $userID)
        ->select('id', 'active', 'jwt', 'company_id','type' ,  'email', 'password', 'lat', 'lng')->with(['company'=> function($q){
            $q->with('orderProcessType');
        }])
        ->first();
    }
    return $user;

}
function FileDelete($paths = NULL){
    $image_paths = $paths;
   foreach($image_paths as $path){
    if(File::exists($path)){
        File::delete($path);
    }
   }

}
function getWorkingHours(Request $request, $details){
    $workingHours = 0;
    $data = array();
    foreach($details as $value){
        $data[] = [
            'order_id' => $value['order_id'],
            'id'       => $value['id'],
         ];
         $workingHours += $value['working_hours'];
    }
       return $workingHours;
}
function getItemsOrder(Request $request, $details){
    $qty = 0;
    $price = 0;
    $itemAmount = 0;
    $data = array();
    foreach($details as $value){
        $item = DB::table($value['provider_id'].'_warehouse_parts')->where('id', $value['item_id'])->first();
        $data[] = [
            'order_id' => $value['order_id'],
            'id'       => $value['id'],
         ];
         $price = !empty($item)? $item->price : 0 ;
         $qty += $value['taken'];
    }
       $itemAmount = (!empty($qty)? $qty: 0) * (!empty($price)? $price: 0);
       return ['qty' => $qty, 'price' => $price, 'item_amount' => $itemAmount];
}


function itemInvoiceCollection(Request $request , $items, $companyId){
    $orderSumTotal = '';
    $totalHrs = '';
    $itemSumTotal  = 0;
    $totalRateFees = '';
   return getItemsOrder($request, $items);

}


function orderInvoiceCollection(Request $request , $orders, $companyId = NULL){

    $data = array();
    $total = 0;
    $company = '';
    $provider = '';
    $totalHrs  = 0;
    $totalRateFees = 0;
    $itemSumTotal = 0;
    $orderSumTotal = 0;
    $getItemsOrder = '';
    $itemAmountSumTotal = 0;
    $provider = '';

    if(!empty($orders)){

        foreach($orders as $order){

            $rateFee = 0;
            $workingHrs = 0;

            if(!empty($order['items']) && count($order['items']) > 0){

                $getItemsOrder = itemInvoiceCollection($request, $order['items'], $companyId);

            }else{

                $getItemsOrder = ['qty' => 0, 'price' => 0, 'item_amount' => 0];

            }

            $workingHrs =  getWorkingHours($request, $order['details']);
            $fee = ProviderCategoryFee::where('provider_id', $order['provider_id'])->where('company_id', $order['company_id'])
            ->where('cat_id', $order['cat_id'])->first();

            if($order['type'] == 'urgent'){

                $rateFee = !empty($fee->urgent_fee)? $fee->urgent_fee : 0;

            }elseif($order['type'] == 'scheduled' || $order['type'] ==  're_scheduled'){

                $rateFee = !empty($fee->scheduled_fee)? $fee->scheduled_fee : 0;

            }elseif($order['type'] == 'emergency'){

                $rateFee = !empty($fee->emergency_fee)? $fee->emergency_fee : 0;

            }else{

                $rateFee = !empty($fee->third_fee)? $fee->third_fee: 0;

            }

            $qtyRateTotal =  (!empty($rateFee)? $rateFee: 1)*(!empty($workingHrs)? $workingHrs : 1) ;
            $data['invoiceDetail'][] = [
                'id' => $order['id'],
                'order_id' => $order['id'],
                'created_at' => Carbon::parse($order['created_at'])->format('d-M-Y h:i:s'),
                'item_total' => $order['item_total'],
                'hrs' => $workingHrs,
                'service_per_hr' => $rateFee,
                'qty_rate_total'  => $qtyRateTotal,
                'item_total' => $order['item_total'],
                'qty' => !empty( $getItemsOrder['qty'] )?  $getItemsOrder['qty'] : 0,
                'price' => !empty($getItemsOrder['price'])? $getItemsOrder['price'] : 0,
                'item_amount'  => $getItemsOrder['item_amount'],
                'service_name' => !empty($order['category'])? $order['category']['parent']['en_name']: '',

            ];

            $orderSumTotal += $qtyRateTotal;
            $totalHrs += $workingHrs;
            $itemSumTotal  += $order['item_total'];
            $totalRateFees += $rateFee;
            $itemAmountSumTotal  += $getItemsOrder['item_amount'];
            $provider = Provider::where('id', $order['provider']['id'])->first();

        }

    }

    $total =  $orderSumTotal + $itemAmountSumTotal;
    $total_count_orders  = isset($data['invoiceDetail'])? count($data['invoiceDetail']) : 0;
    $data['vat_total']  = $total * $provider->vat/ 100;
    $data['order_vat_total']  = $orderSumTotal * $provider->vat/ 100;
    $data['material_vat_total']  = $itemAmountSumTotal * $provider->vat/ 100;
    $data['qr_code_image']  = null;
    $data['vat']  = $provider->vat;
    $data['vat_registration']  = $provider->vat_registration;
    $data['total_hrs'] = $totalHrs;
    $data['total_rate_fees'] = $totalRateFees;
    $data['total_count_orders'] = $total_count_orders;
    $data['order_sum_total'] = $orderSumTotal;
    $data['item_sum_total'] = $itemSumTotal;
    $data['item_amount_sum_total'] = $itemAmountSumTotal;
    $data['total'] = $total;
    $data['invoice_no'] = 1;
    $data['invoice_date'] = 'jan';
    $data['company'] = !empty($companyId)?  Company::where('id', $companyId)->where('active', true)->first() : '';
    $provider_id = $provider->id;
    $provider  = Provider::where('id', $provider_id)->first();
    $data['provider'] = $provider;
    $data['status'] = 'generate-invoice';

    return $data;
}
