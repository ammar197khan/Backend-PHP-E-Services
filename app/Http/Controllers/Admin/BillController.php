<?php

namespace App\Http\Controllers\Admin;

use App\Models\Provider;
use App\Models\Category;
use App\Models\Order;
use App\Models\AdminInvoiceHead;
use App\Models\AdminInvoiceDetail;
use App\Models\AdminInvoiceMaterial;
use App\Models\Company;
use App\Models\ProviderCategoryFee;
use Prgayman\Zatca\Facades\Zatca;
use Prgayman\Zatca\Utilis\QrCodeOptions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\QInvoiceHead;
use Illuminate\Support\Carbon;
use Storage;
use Auth;
use DB;


class BillController extends Controller
{


   public function getWorkingHours(Request $request, $details){
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
   public function getItemsOrder(Request $request, $details){
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

    public function itemInvoiceCollection(Request $request , $items, $companyId){
       return $this->getItemsOrder($request, $items);
    }

    public function orderInvoiceCollection(Request $request , $orders, $companyId = NULL){
        $data = array();
        $total = 0;
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
                    //  dd($order);
                $rateFee = 0;
                $workingHrs = 0;

                if(!empty($order['items']) && count($order['items']) > 0){

                    $getItemsOrder = $this->itemInvoiceCollection($request, $order['items'], $companyId);

                }else{

                    $getItemsOrder = ['qty' => 0, 'price' => 0, 'item_amount' => 0];

                }

                $workingHrs =  $this->getWorkingHours($request, $order['details']);
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
        // $total_count_material_orders =  isset($data['invoiceDetail'])? count($data['invoiceDetail']) : 0;
        $data['vat_total']  = $total * (!empty(Auth::guard('admin')->user()['vat'])? Auth::guard('admin')->user()['vat']: 0)/ 100;
        $data['order_vat_total']  = $orderSumTotal * (!empty(Auth::guard('admin')->user()['vat'])? Auth::guard('admin')->user()['vat']: 0)/ 100;
        $data['material_vat_total']  = $itemAmountSumTotal * (!empty(Auth::guard('admin')->user()['vat'])? Auth::guard('admin')->user()['vat'] : 0)/ 100;
        $data['qr_code_image']  = null;
        $data['vat']  = !empty(Auth::guard('admin')->user()['vat'])? Auth::guard('admin')->user()['vat'] : '';
        $data['vat_registration']  = !empty(Auth::guard('admin')->user()['vat_registration'])? Auth::guard('admin')->user()['vat_registration']: '';
        $data['total_hrs'] = $totalHrs;
        $data['total_rate_fees'] = $totalRateFees;
        $data['total_count_orders'] = $total_count_orders;
        // $data['total_count_material_orders'] = $total_count_orders;
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
    public function index(Request $request)
    {
        // dd('working');
        // $month  = $request->month;
        // $year   = $request->year;
        // $date   =  date('Y-m-d');
        // $month   =  date('m');
        // $year   =  date('Y');
        // $all_providers = Provider::where('active', 1)->get();
        // $providers = Provider::where('active', 1)->get();
        // $cats = Category::where('parent_id', null)->select('id','en_name')->get();
        // $data = array();
        // foreach($providers as $provider){
        //     $orders = '';
        //     $orders   = Order::where('completed', 1)->where('provider_id', $provider->id)->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->with(['items' , 'details',  'category' => function($q){
        //      $q->with('parent');
        //  }, 'company' , 'provider'])->get();

        //  if(!empty($orders) && $orders->count() > 0){
        //     $data['data'][]   = $this->orderInvoiceCollection($request,collect($orders)->toArray());

        //  }
        //  $data['status'] = 'open';
        //  $data['invoiceGenerated']    = false;
        // }


        $month   =  date('m');
        $year   =  date('Y');
        $date   = $year."-".$month."-"."01" ;

        // $data['status'] = '';
        // $data = array();
        $all_providers = Provider::where('active', 1)->get();
        $providers = Provider::where('active', 1)->get();
        $cats = Category::where('parent_id', null)->select('id','en_name')->get();
        $data['status'] = '';
        $data = array();

         $adminInvoiceHead = AdminInvoiceHead::where('date' , $date)->with(['adminInvoiceDetail', 'provider'])->get();
         $invoiceStatus = '';

        if(!empty($adminInvoiceHead) && $adminInvoiceHead->count() > 0){
            $data['data'] = $adminInvoiceHead;
            // dd($data);
            $data['status']              =  $data['data']['0']['status'];
            $data['invoiceGenerated']    = true;
        }else{

            foreach($providers as $provider){

                $orders = '';

                $orders   = Order::where('completed', 1)->where('provider_id', $provider->id)->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->with(['items','details',  'category' => function($q){
                    $q->with('parent');
                }, 'company', 'provider' ])->get();

                if(!empty($orders) && $orders->count() > 0){

                    $data['data'][]   = $this->orderInvoiceCollection($request,collect($orders)->toArray());

                }

            }

            $data['status'] = 'open';

            $data['invoiceGenerated']    = false;

        }

        // //   dd($data);
        // $month = $request->month;
        // $year = $request->year;
        return view('admin.bills.index',compact('data','cats', 'month', 'year'));
    }

    public function search(Request $request)
    {


        // $all_providers = Provider::where('active', 1)->get();

        // $providers = Provider::where('active', 1);

        // if($request->provider_name != null)
        // {
        //     $providers->whereIn('id', $request->provider_name);
        // }

        // if($request->main_cats != null)
        // {
        //     $cat = Category::whereIn('parent_id', $request->main_cats)->pluck('id');
        //     $provider_ids = Order::whereIn('cat_id', $cat)->groupby('provider_id')->pluck('provider_id');
        //     $providers->whereIn('id', $provider_ids);
        // }
        // $providers = $providers->get();

        // $cats = Category::where('parent_id', null)->select('id','en_name')->get();
        $month = $request->month;
        $year  = $request->year;
        $date   = $year."-".$month."-"."01" ;
        // $data['status'] = '';
        // $data = array();
        $all_providers = Provider::where('active', 1)->get();
        $providers = Provider::where('active', 1)->get();
        $cats = Category::where('parent_id', null)->select('id','en_name')->get();
        $data['status'] = '';
        $data = array();

         $adminInvoiceHead = AdminInvoiceHead::where('date' , $date)->with(['adminInvoiceDetail', 'provider'])->get();
         $invoiceStatus = '';
        if(!empty($adminInvoiceHead) && $adminInvoiceHead->count() > 0){
            $data['data'] = $adminInvoiceHead;
            $data['status']              =  $data['data']['0']['status'];
            $data['invoiceGenerated']    = true;
        }else{

            foreach($providers as $provider){

                $orders = '';

                $orders   = Order::where('completed', 1)->where('provider_id', $provider->id)->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->with(['items','details',  'category' => function($q){
                    $q->with('parent');
                }, 'company', 'provider' ])->get();

                if(!empty($orders) && $orders->count() > 0){

                    $data['data'][]   = $this->orderInvoiceCollection($request,collect($orders)->toArray());

                }

            }

            $data['status'] = 'open';

            $data['invoiceGenerated']    = false;

        }

        //   dd($data);
        $month = $request->month;
        $year = $request->year;

        return view('admin.bills.index',compact('data','month','year')) ;
    }

    public function servicesInvoiceDetails(Request $request, $id)
    {
        $month = $request->month;
        $year   = $request->year;
        $date   = (!empty($year)? $year : date("Y")) ."-". (!empty($month)? $month : date("m") ). "-01";
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');
        $provider       = \App\Models\Provider::where('id',  $request->provider_id)->with('address')->first();
        $data = AdminInvoiceHead::where('date' , $date)->where('provider_id', $provider->id)->with(['adminInvoiceDetail', 'provider'])->first();
        return view('admin.bills.invoice', compact('data', 'provider', 'month', 'year'));
    }

    public function materialsInvoiceDetails(Request $request, $id)
    {
        // $from = $request->from;
        // $to   = $request->to;
        // $provider = Provider::find($id);
        // $data = $provider->BillToQreeb($from, $to);
        // dd($request->all());
        $month = $request->month;
        $year   = $request->year;
        $date   = (!empty($year)? $year : date("Y")) ."-". (!empty($month)? $month : date("m") ). "-01";
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');
        $provider       = \App\Models\Provider::where('id',  $request->provider_id)->with('address')->first();
        $data = AdminInvoiceHead::where('date' , $date)->where('provider_id', $provider->id)->with(['adminInvoiceMaterial', 'provider'])->first();
//    dd($data);
        return view('admin.bills.material', compact('data', 'provider', 'month', 'year'));
    }


    public function generateMonthlyInvoice(Request $request){
        $data = array();
        $month = $request->month;
        $year   = $request->year;
        $date = $year."-".$month."-01";
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');
        $providers = Provider::where('active', 1)->get();
        $data = array();

        foreach($providers as $provider){

           $orders = '';
           $orders   = Order::where('completed', 1)->where('provider_id', $provider->id)->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->with(['items', 'details',  'category' => function($q){
            $q->with('parent');
        }, 'company' , 'provider'])->get();


        if(!empty($orders) && $orders->count() > 0){

            $data[] = $this->orderInvoiceCollection($request,collect($orders)->toArray());
        }


    }

        // dd($itemData);



        // $monthlyInvoices = array();

        $adminInvoiceHead =  AdminInvoiceHead::whereYear('date', '=', $year)->whereMonth('date', '=', $month);
        if(!empty($adminInvoiceHead->get()) && count($adminInvoiceHead->get()) > 0){
           $adminInvoiceHead->delete();
           $adminInvoiceDetail  = AdminInvoiceDetail::whereYear('date', '=', $year)->whereMonth('date', '=', $month);
           if(!empty($adminInvoiceDetail->get()) && count($adminInvoiceDetail->get()) > 1){
            $adminInvoiceDetail->delete();
           }
           $adminInvoiceMaterial  = AdminInvoiceMaterial::whereYear('date', '=', $year)->whereMonth('date', '=', $month);
           if(!empty($adminInvoiceMaterial->get()) && count($adminInvoiceMaterial->get()) > 1){
            $adminInvoiceMaterial->delete();
           }
        }

        $adminInvoiceHeadIds = array();
        // dd($data );
        foreach($data as $monthlyInvoice){

            $adminInvoiceHead                       =  new AdminInvoiceHead();

            $totalWithVat =  $monthlyInvoice['vat_total'];
            $qrCodeOptions = new QrCodeOptions;
            // Format (png,svg,eps)
            $qrCodeOptions->format("svg");
            // Color
            $qrCodeOptions->color(255,255,255);
            // Background Color
            $qrCodeOptions->backgroundColor(0,0,0);
            // Size
            $qrCodeOptions->size(100);
            // Margin
            $qrCodeOptions->margin(0);
            // Style (square,dot,round)
            $qrCodeOptions->style('square',0.5);
            // Eye (square,circle)
            $qrCodeOptions->eye('square');

            $qrCode = zatca()
            ->sellerName(!empty($monthlyInvoice['provider']['en_name'])? $monthlyInvoice['provider']['en_name']: '')
            ->vatRegistrationNumber(!empty(Auth::guard('admin')->user()['vat_registration'])? Auth::guard('admin')->user()['vat_registration']: '')
            ->timestamp(date('Y-m-d H:i:s'))
            ->totalWithVat(!empty($totalWithVat)? $totalWithVat: '')
            ->vatTotal(!empty(Auth::guard('admin')->user()['vat'])? Auth::guard('admin')->user()['vat']: '')
            ->toQrCode($qrCodeOptions);
            $filename = time() . uniqid().'-'.'zatca.svg';
            $filepath =  'public/qrcode/'.$filename;
            Storage::disk('local')->put($filepath , $qrCode);
            $adminInvoiceHead->bill_to              = $monthlyInvoice['provider']['en_name'];
            $adminInvoiceHead->vat                  = Auth::guard('admin')->user()['vat'];
            $adminInvoiceHead->vat_total            = $monthlyInvoice['vat_total'];
            $adminInvoiceHead->provider_id          = $monthlyInvoice['provider']['id'];
            $adminInvoiceHead->provider_name        = $monthlyInvoice['provider']['en_name'];
            $adminInvoiceHead->total                = $monthlyInvoice['total'];
            $adminInvoiceHead->total_qty                     = $monthlyInvoice['total_hrs'];
            $adminInvoiceHead->total_rate                    = $monthlyInvoice['total_rate_fees'];
            $adminInvoiceHead->total_count_orders            = $monthlyInvoice['total_count_orders'];
            $adminInvoiceHead->order_sum_total               = $monthlyInvoice['order_sum_total'];
            $adminInvoiceHead->item_sum_total                = $monthlyInvoice['item_sum_total'];
            $adminInvoiceHead->item_amount_sum_total         =  $monthlyInvoice['item_amount_sum_total'];
            $adminInvoiceHead->order_vat_total               =  $monthlyInvoice['order_vat_total'];
            $adminInvoiceHead->material_vat_total            =  $monthlyInvoice['material_vat_total'];
            $adminInvoiceHead->date                 = $year."-".$month."-01";
            $adminInvoiceHead->status               = 'open';
            $adminInvoiceHead->img_qr_code          = $filename;
            $adminInvoiceHead->is_paid              = 'not-paid';
            $adminInvoiceHead->vat_registration     = $monthlyInvoice['provider']['vat_registration'];
            $adminInvoiceHead->save();
            $adminInvoiceHeadIds[] = $adminInvoiceHead->id;
           if(isset($monthlyInvoice['invoiceDetail'])){
            foreach($monthlyInvoice['invoiceDetail'] as $element){


                        $adminInvoiceDetail = new AdminInvoiceDetail();
                        $adminInvoiceDetail->order_id = $element['order_id'];
                        $adminInvoiceDetail->qty = $element['hrs'];
                        $adminInvoiceDetail->rate =  $element['service_per_hr'];
                        $adminInvoiceDetail->service_name = $element['service_name'];
                        $adminInvoiceDetail->provider_id = $monthlyInvoice['provider']['id'];
                        $adminInvoiceDetail->created_at = carbon::parse($element['created_at'])->format('Y-m-d h:i:s');
                        $adminInvoiceDetail->date = 	carbon::parse($element['created_at'])->format('Y-m-d h:i:s');
                        $adminInvoiceDetail->qty_rate_total = $element['qty_rate_total'];
                        $adminInvoiceDetail->admin_invoice_head_id = $adminInvoiceHead->id;
                        $adminInvoiceDetail->save();
                      if(!empty($element['qty'])){

                        $adminInvoiceMaterial = new AdminInvoiceMaterial();
                        $adminInvoiceMaterial->order_id = $element['order_id'];
                        $adminInvoiceMaterial->qty = $element['qty'];
                        $adminInvoiceMaterial->provider_id = $monthlyInvoice['provider']['id'];
                        $adminInvoiceMaterial->price =  $element['price'];
                        $adminInvoiceMaterial->item_amount = $element['item_amount'];
                        $adminInvoiceMaterial->created_at = carbon::parse($element['created_at'])->format('Y-m-d h:i:s');
                        $adminInvoiceMaterial->date = carbon::parse($element['created_at'])->format('Y-m-d h:i:s');
                        $adminInvoiceMaterial->admin_invoice_head_id = $adminInvoiceHead->id;
                        $adminInvoiceMaterial->save();

                        }

            }

        }


        }


        $data['response'] =  'done';
        $data['success']  = true;
        $data['code']     = 200;

        return response()->json($data);


    }

    public function isPaidStore(Request $request){
        $data = array();
        $month = $request->month;
        $year   = $request->year;
        $date = $year ."-". $month . "-01";
        $isPaid = $request->is_paid;
        $provider_id    = $request->providerId;
        $adminInvoiceHead = AdminInvoiceHead::where('provider_id', $provider_id )->where('date' , $date);
        if(!empty($adminInvoiceHead->first())){
            $adminInvoiceHead->update(['is_paid' => $isPaid]);
            $data['success']  = true;
            $data['code']     = 200;
        }else{
            $data['success']  = false;
            $data['code']     = 200;
        }


        return response()->json($data);

    }
    public function closeInvoice(Request $request){
        $data = array();
        $month = $request->month;
        $year   = $request->year;
        $date = $year."-".$month."-01";
        $adminInvoiceHeads =  AdminInvoiceHead::where('date' , $date)->get();
        foreach($adminInvoiceHeads as $adminInvoiceHead){

            $invoiceDetail =  AdminInvoiceHead::where('id', $adminInvoiceHead->id);
            $invoiceDetail->update(['status' => 'close']);

        }
        $data['success']  = true;
        $data['code']     = 200;

        return response()->json($data);
    }

}
