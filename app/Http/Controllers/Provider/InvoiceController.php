<?php

namespace App\Http\Controllers\Provider;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Prgayman\Zatca\Facades\Zatca;
use Prgayman\Zatca\Utilis\QrCodeOptions;
use InvalidArgumentException;
use Prgayman\Zatca\Utilis\Tag;
use App\Models\Company;
use App\Models\Order;
use App\Models\QInvoiceMaterial;
use App\Models\ProviderCategoryFee;
use App\Models\QInvoiceDetail;
use App\Models\QInvoiceHead;
use App\Models\Provider;
use Illuminate\Support\Carbon;
use Storage;
use Auth;
use DB;

class InvoiceController extends Controller
{



    public function vatRegistrationNumber(string $value): self
    {
        // dd('working');
        if (strlen($value) != 15) {
            throw new InvalidArgumentException('Vat Registration Number must be 15 number');
        }

        $this->vatRegistrationNumber = new Tag(2, $value);
        return $this;
    }

    public function index(Request $request)
    {
        // dd('working');


        $data   = array();
        $month  = $request->month;
        $year   = $request->year;
        $date   =  date('Y-m-d') ;
        $month   =  date('m') ;
        $year   =  date('Y') ;
        $provider  = \App\Models\Provider::where('id',  Auth::guard('provider')->user()->provider_id)->with('address')->first();
        $provider_id    = Auth::guard('provider')->user()->provider_id;
        $companies_id   = DB::table('collaborations')->where('provider_id', $provider_id)->pluck('company_id')->toArray();
        $companies      = Company::whereIn('id', $companies_id)->where('active', true)->get();
        // $invoiceHead    = QInvoiceHead::where('provider_id', $provider_id )->where('date' , $date)->with(['invoiceDetail', 'company'])->get();
        // // $orders         = Order::where('completed', 1)->where('provider_id', Auth::guard('provider')->user()->provider_id)->where('company_id', $companies_id)->with(['items' => function($q){
        // //     $q->with(['this_item']);
        // // }])->get();
        // //       dd(collect($orders)->toArray());
        //  $invoiceStatus = '';
        // if(!empty($invoiceHead) && count(collect($invoiceHead)) > 0){
        //     foreach($invoiceHead as $invoiceDetail){

        //         $monthlyInvoices[] = [
        //             'id'                  => $invoiceDetail->id,
        //             'company'             => $invoiceDetail->company,
        //             'elements'            => $this->getInvoiceDetail($invoiceDetail->invoiceDetail),
        //             'orders_vat'          => $invoiceDetail->orders_vat,
        //             'materials_vat'       => $invoiceDetail->orders_vat,
        //             'vat_registration'    => $invoiceDetail->vat_registration,
        //             'total_count'         => $invoiceDetail->total_count,
        //             'total_orders_amount' => $invoiceDetail->total_orders_amount,
        //             'total_items_amount'  => $invoiceDetail->total_items_amount,
        //             'total'               => $invoiceDetail->total,
        //             'status'              => !empty($invoiceDetail->status)? $invoiceDetail->status : '',
        //             'vat_total'           => $invoiceDetail->total_due_commission,
        //             'qr_code_image'       => $invoiceDetail->img_qr_code
        //     ];

        //     $invoiceStatus = !empty($invoiceDetail->status)? $invoiceDetail->status : '';

        //     }
        //     $monthlyInvoices['status']              =  $invoiceStatus;
        //     $monthlyInvoices['invoiceGenerated']    = true;
        // }else{
        //     foreach(collect($companies)->toArray() as $company){
        //         $monthlyInvoices[] = billToProvider($provider_id, $company['id'], $month, $year);
        //     }
        //     $monthlyInvoices['status']              = 'invoice-generated';
        //     $monthlyInvoices['invoiceGenerated']    = false;
        // }


        $invoiceHead    = QInvoiceHead::where('provider_id', $provider_id )->whereYear('date', '=', $year)->whereMonth('date', '=', $month)->with(['invoiceDetail', 'company'])->get();



        $data['status'] = '';
        $data = array();

         $invoiceStatus = '';
        if(!empty($invoiceHead) && count(collect($invoiceHead)) > 0){
            $data = $invoiceHead;
            $data['status']              =  $data['0']['status'];
            $data['invoiceGenerated']    = true;
        }else{

            foreach($companies_id as $companyId){
                // dd($companyId);
               $orders = '';
               $orders   = Order::where('completed', 1)->where('provider_id', Auth::guard('provider')->user()->provider_id)->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->where('company_id', $companyId)->with(['items' => function($q){
                $q->with(['this_item']);
            } , 'details',  'category' => function($q){
                $q->with('parent');
            }, 'company' , 'provider'])->get();
            // $data[]   =  $this->orderInvoiceCollection($request,collect($orders)->toArray());
            $data[] = $this->orderInvoiceCollection($request,collect($orders)->toArray(), $companyId);

            }
            $data['status'] = 'open';
            $data['invoiceGenerated']    = false;
        }

        // old code
        // $from = $request->from;
        // $to   = $request->to;
        // $provider_id  = Auth::guard('provider')->user()->provider_id;
        // $companies_id = DB::table('collaborations')->where('provider_id', $provider_id)->pluck('company_id')->toArray();
        // $companies    = Company::whereIn('id', $companies_id)->where('active', true)->get();
         // old
        //  dd($data);
        return view('provider.invoices.index', compact( 'data', 'companies'));
    }
    public function isPaidStore(Request $request){
        $data = array();
        $month = $request->month;
        $year   = $request->year;
        $date = $year ."-". $month . "-01";
        $isPaid = $request->is_paid;
        $provider_id    = Auth::guard('provider')->user()->provider_id;
        // dd($date, $provider_id, $request->companyId);
        $qInvoiceHead = QInvoiceHead::where('provider_id', $provider_id )->where('company_id', $request->companyId )->where('date' , $date);
        if(!empty($qInvoiceHead->first())){
            $qInvoiceHead->update(['is_paid' => $isPaid]);
            $data['success']  = true;
            $data['code']     = 200;
        }else{
            $data['success']  = false;
            $data['code']     = 200;
        }


        return response()->json($data);

    }
    public function getItems(Request $request, $items){
        //    dd($items);
        //    $data = array();
        //    foreach(){
        //     $data[] = [

        //     ];
        //    }
    }
    public function getItemsOrder(Request $request, $details){
            $qty = 0;
            $price = 0;
            $itemAmount = 0;
            $data = array();
            foreach($details as $value){

                $data[] = [
                    'order_id' => $value['order_id'],
                    'id'       => $value['id'],
                 ];
                 $price = $value['this_item']['price'];
                 $qty += $value['taken'];
            }
               $itemAmount = (!empty($qty)? $qty: 0) * (!empty($price)? $price: 0);
               return ['qty' => $qty, 'price' => $price, 'item_amount' => $itemAmount];
    }
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
    public function itemInvoiceCollection(Request $request , $items, $companyId){
        $orderSumTotal = '';
        $totalHrs = '';
        $itemSumTotal  = 0;
        $totalRateFees = '';
       return $this->getItemsOrder($request, $items );

    }
    public function orderInvoiceCollection(Request $request , $orders, $companyId ){
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
        //    echo "<pre/>";
        //    print_r($orders);
           foreach($orders as $order){

            // dd($order['items'] , 'working');
            // dd($this->itemInvoiceCollection($request, $order['items'], $companyId));
            //    $this->itemInvoiceCollection($request, $order['items'], $companyId);
           $rateFee = 0;
           $workingHrs = 0;
        //    echo "<pre/>";

           if(!empty($order['items']) && count($order['items']) > 0){
            // echo "<pre/>";
            // print_r($order['items']);
           $getItemsOrder = $this->itemInvoiceCollection($request, $order['items'], $companyId);
           }else{
            // echo "her";
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
            //  dd($fee );
           $qtyRateTotal =  (!empty($rateFee)? $rateFee: 1)*(!empty($workingHrs)? $workingHrs : 1) ;
            $data['invoiceDetail'][] = [
                'id' => $order['id'],
               'order_id' => $order['id'],
            //    'service_charges' => $order['total_amount'],
               'created_at' => Carbon::parse($order['created_at'])->format('d-M-Y h:i:s'),
               'item_total' => $order['item_total'],
               'hrs' => $workingHrs,
               'service_per_hr' => $rateFee,
               'qty_rate_total'  => $qtyRateTotal,
               // item
               // qty =>

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
            // dd($data);
            // echo "<pre>";
            // print_r($order['company']);
           }
        //   exit;
           $total =  $orderSumTotal + $itemAmountSumTotal;
           $total_count_orders  = isset($data['invoiceDetail'])? count($data['invoiceDetail']) : 0;
           $provider = Provider::where('id', provider()->provider_id)->first();
           $data['vat_total']  = $total * $provider->vat/ 100;
           $data['order_vat_total']  = $orderSumTotal * $provider->vat/ 100;
           $data['material_vat_total']  = $itemAmountSumTotal * $provider->vat/ 100;
           $data['qr_code_image ']  = null;
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
           $data['company'] = Company::where('id', $companyId)->where('active', true)->first();
           $provider_id = Auth::guard('provider')->user()->provider_id;
           $provider  = Provider::where('id', $provider_id)->first();
           $data['provider'] = $provider;
           $data['status'] = 'generate-invoice';
        //    dd($data);

           return $data;
    }

    public function printOrdersInvoice(Request $request, $company_id)
    {
        // dd('working');

        $month = $request->month;
        $year   = $request->year;
        $date   = (!empty($year)? $year : date("Y")) ."-". (!empty($month)? $month : date("m") ). "-01";
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');
        $provider_id    = Auth::guard('provider')->user()->provider_id;
        $provider       = \App\Models\Provider::where('id',  Auth::guard('provider')->user()->provider_id)->with('address')->first();
        $company        =  Company::where('id', $company_id)->with('address')->first();
        $data    =  QInvoiceHead::where('provider_id', $provider_id )->where('company_id', $company_id)->whereYear('date', '=', $year)->whereMonth('date', '=', $month)->with(['invoiceDetail', 'company'])->first();
        // $orders         = Order::where('completed', 1)->where('provider_id', Auth::guard('provider')->user()->provider_id)->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->where('company_id', $company->id)->with(['items' => function($q){
        //     $q->with(['this_item']);
        // } , 'details',  'category' => function($q){
        //     $q->with('parent');
        // }, 'company' , 'provider'])->get();
        // // dd( $orders );

        // $data   =  $this->orderInvoiceCollection($request,collect($orders)->toArray(), $company->id);
            //   dd($data);
        // $data = array();
        // if(!empty($invoiceHead)){
        //     foreach($invoiceHead as $invoiceDetail){
        //         $data = [
        //             'id'                  => $invoiceDetail->id,
        //             'company'             => $invoiceDetail->company,
        //             'elements'            => $this->getInvoiceDetail($invoiceDetail->invoiceDetail),
        //             'vat'                 => $invoiceDetail->orders_vat,
        //             'orders_vat'          => $invoiceDetail->orders_vat,
        //             'vat_registration'    => number_format($provider->vat_registration, 0, '.', ''),
        //             'materials_vat'       => $invoiceDetail->order_vat,
        //             'vat_registration'    => $invoiceDetail->vat_registration,
        //             'total_count'         => $invoiceDetail->total_count,
        //             'total_orders_amount' => $invoiceDetail->total_orders_amount,
        //             'total_items_amount'  => $invoiceDetail->total_items_amount,
        //             'total'               => $invoiceDetail->total,
        //             'status'              => $invoiceDetail->status,
        //             'vat_total'           => $invoiceDetail->total_due_commission,
        //             'date'                => Carbon::parse($invoiceDetail->date)->format('F Y'),
        //             'qr_code_image'       => $invoiceDetail->img_qr_code
        //     ];

        //     }

        //     $data['invoiceGenerated']    = true;
        // }
        $from = $month;
        $to =  $year;
        // dd($data['invoiceDetail']);
        return view('provider.invoices.invoice', compact('data', 'company', 'provider', 'from', 'to'));
    }


    public function printMaterialsInvoice(Request $request, $company_id)
    {
        $month = $request->month;
        $year   = $request->year;

        $date   = (!empty($year)? $year : date("Y") )."-". (!empty($month)? $month : date("m") ). "-01";

        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');

        $provider_id = Auth::guard('provider')->user()->provider_id;
        $provider  = \App\Models\Provider::where('id',  Auth::guard('provider')->user()->provider_id)->with('address')->first();
        $company = Company::where('id', $company_id)->with('address')->first();

        $data    =  QInvoiceHead::where('provider_id', $provider_id )->where('company_id', $company_id)->whereYear('date', '=', $year)->whereMonth('date', '=', $month)->with(['invoiceDetail', 'company', 'invoiceMaterial'])->first();
        //  dd($data);
        // $data = array();
        // if(!empty($invoiceHead)){
        //     foreach($invoiceHead as $invoiceDetail){
        //         $data = [
        //             'id'                  => $invoiceDetail->id,

        //             'company'             => $invoiceDetail->company,
        //             'elements'            => $this->getInvoiceDetail($invoiceDetail->invoiceDetail),
        //             'vat'                 => $invoiceDetail->orders_vat,
        //             'order_vat'           => $invoiceDetail->orders_vat,
        //             'materials_vat'       => $invoiceDetail->orders_vat,
        //             'vat_registration'    => $invoiceDetail->vat_registration,
        //             'total_count'         => $invoiceDetail->total_count,
        //             'total_orders_amount' => $invoiceDetail->total_orders_amount,
        //             'total_items_amount'  => $invoiceDetail->total_items_amount,
        //             'total'               => $invoiceDetail->total,
        //             'status'              => $invoiceDetail->status,
        //             'vat_total'           => $invoiceDetail->total_due_commission,
        //             'date'                => Carbon::parse($invoiceDetail->date)->format('F Y'),
        //             'qr_code_image'       => $invoiceDetail->img_qr_code
        //     ];

        //     }

        //     $data['invoiceGenerated']    = true;
        // }
        $from = $month;
        $to =  $year;
        return view('provider.invoices.material', compact('data', 'company', 'provider', 'from', 'to'));
    }
    public function getInvoiceDetail($invoiceDetail){
        $elements = array();
        foreach(collect($invoiceDetail) as $value){
            $elements []= (object)$value;
        }
        return $elements;
    }

    public function getMonthlyInvoice(Request $request){

        $data   = array();
        $month  = $request->month;
        $year   = $request->year;
        $date   = $year."-".$month."-"."01" ;
        $provider  = \App\Models\Provider::where('id',  Auth::guard('provider')->user()->provider_id)->with('address')->first();
        $provider_id    = Auth::guard('provider')->user()->provider_id;
        $companies_id   = DB::table('collaborations')->where('provider_id', $provider_id)->pluck('company_id')->toArray();
        $companies      = Company::whereIn('id', $companies_id)->where('active', true)->get();







        //   dd($data);

        $invoiceHead    = QInvoiceHead::where('provider_id', $provider_id )->where('date' , $date)->with(['invoiceDetail', 'company'])->get();




        $data = array();

         $invoiceStatus = '';
        if(!empty($invoiceHead) && count(collect($invoiceHead)) > 0){
            $data = $invoiceHead;

            $data['status']              =  $data['0']['status'];
            $data['invoiceGenerated']    = true;
        }else{

            foreach($companies_id as $companyId){
                // dd($companyId);
               $orders = '';
               $orders   = Order::where('completed', 1)->where('provider_id', Auth::guard('provider')->user()->provider_id)->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->where('company_id', $companyId)->with(['items' => function($q){
                $q->with(['this_item']);
            } , 'details',  'category' => function($q){
                $q->with('parent');
            }, 'company' , 'provider'])->get();
            // $data[]   =  $this->orderInvoiceCollection($request,collect($orders)->toArray());
            $data[] = $this->orderInvoiceCollection($request,collect($orders)->toArray(), $companyId);

            }
            $data['status']              =  'open';
            $data['invoiceGenerated']    = false;
        }


        return view('provider.invoices.index', compact('data', 'month', 'year'));

    }
    public function closeInvoice(Request $request){
        $data = array();
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $month = $request->month;
        $year   = $request->year;
        $date = $year."-".$month."-01";
        $invoiceHead =  QInvoiceHead::where('provider_id', $provider_id )->where('date' , $date)->with(['invoiceDetail', 'company'])->get();
        foreach($invoiceHead as $invoiceDetail){

            $invoiceDetail =  QInvoiceHead::where('id', $invoiceDetail->id);
            $invoiceDetail->update(['status' => 'close']);

        }
        $data['success']  = true;
        $data['code']     = 200;

        return response()->json($data);
    }

    public function generateMonthlyInvoice(Request $request){
        $data = array();
        $month = $request->month;
        $year   = $request->year;
        $date = $year."-".$month."-01";
        $month = Carbon::parse($date)->format('m');
        $year = Carbon::parse($date)->format('Y');
        $isUpdate = $request->isUpdate;
        $provider_id = Auth::guard('provider')->user()->provider_id;
        $provider  = Provider::where('id', $provider_id)->first();
        $companies_id = DB::table('collaborations')->where('provider_id', $provider_id)->pluck('company_id')->toArray();
        $companies = Company::whereIn('id', $companies_id)->where('active', true)->get();
        $dataOrder = array();
        $data = array();
        foreach($companies_id as $companyId){
            // dd($companyId);
           $orders = '';
           $orders   = Order::where('completed', 1)->where('provider_id', Auth::guard('provider')->user()->provider_id)->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->where('company_id', $companyId)->with(['items' => function($q){
            $q->with(['this_item']);
        } , 'details',  'category' => function($q){
            $q->with('parent');
        }, 'company' , 'provider'])->get();

        // $ordersItem   = Order::where('completed', 1)->where('provider_id', Auth::guard('provider')->user()->provider_id)->whereYear('created_at', '=', $year)->whereMonth('created_at', '=', $month)->where('company_id', $companyId)->whereHas('items')->with(['items' => function($q){
        //     $q->with(['this_item']);
        // }])->get();

        // $itemData[]   =  $this->itemInvoiceCollection($request,collect($ordersItem)->toArray(), $companyId);
        // dd(collect($orders)->toArray());
        $data[] = $this->orderInvoiceCollection($request,collect($orders)->toArray(), $companyId);

        }

        // dd($itemData);
        // dd($data);



        // $monthlyInvoices = array();

        $invoiceHead =  QInvoiceHead::where('provider_id', $provider_id )->whereYear('date', '=', $year)->whereMonth('date', '=', $month);
        if(!empty($invoiceHead->get()) && count($invoiceHead->get()) > 0){
           $invoiceHead->delete();
           $QInvoiceDetail  = QInvoiceDetail::where('provider_id', $provider_id)->whereYear('date', '=', $year)->whereMonth('date', '=', $month);
           if(!empty($QInvoiceDetail->get()) && count($QInvoiceDetail->get()) > 1){
            $QInvoiceDetail->delete();
           }
           $QInvoiceMaterial  = QInvoiceMaterial::where('provider_id', $provider_id)->whereYear('date', '=', $year)->whereMonth('date', '=', $month);
           if(!empty($QInvoiceMaterial->get()) && count($QInvoiceMaterial->get()) > 1){
            $QInvoiceMaterial->delete();
           }
        }

        // $monthlyInvoices = array();
        // if(!empty($invoiceHead) && count(collect($invoiceHead)) > 0 && ($request->type != 're-generate')){
        //     foreach($invoiceHead as $invoiceDetail){

        //         $monthlyInvoices[] = [
        //             'id'                  => $invoiceDetail->id,
        //             'company'             => $invoiceDetail->company,
        //             'elements'            => $this->getInvoiceDetail($invoiceDetail->invoiceDetail),
        //             'orders_vat'          => $provider->vat,
        //             'materials_vat'       => $provider->vat,
        //             'total_count'         => $invoiceDetail->total_count,
        //             'total_orders_amount' => $invoiceDetail->total_orders_amount,
        //             'total_items_amount'  => $invoiceDetail->total_items_amount,
        //             'total'               => $invoiceDetail->total,
        //             'status'              => $invoiceDetail->status,
        //             'vat_total'           => $invoiceDetail->vat_total
        //     ];

        //     }

        // }else{
            // foreach(collect($companies)->toArray() as $company){
            //     $monthlyInvoices[] = billToProvider($provider_id, $company['id'], $month, $year);
            //     $isUpdate = false;
            // }
        // }
        $invoiceHeadIds = array();
        foreach($data as $monthlyInvoice){
            // dd($monthlyInvoice);
        //     if($isUpdate){
        //         $invoiceHead =  QInvoiceHead::where('id', $monthlyInvoice['id'])->where('date', $date)->first();

        //     }else{
            $invoiceHead                       =  new QInvoiceHead();

            // }
            $totalWithVat =  $monthlyInvoice['vat_total'];
            // dd($totalWithVat);
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
            ->sellerName($provider->en_name)
            ->vatRegistrationNumber(!empty($provider->vat_registration)? $provider->vat_registration: '')
            ->timestamp(date('Y-m-d H:i:s'))
            ->totalWithVat($totalWithVat)
            ->vatTotal($provider->vat)
            ->toQrCode($qrCodeOptions);
            $filename = time() . uniqid().'-'.'zatca.svg';
            $filepath =  'public/qrcode/'.$filename;
            Storage::disk('local')->put($filepath , $qrCode);

            $invoiceHead->bill_to              = Auth::guard('provider')->user()->name;
            $invoiceHead->bill_from            = $monthlyInvoice['company']['en_name'];
            $invoiceHead->vat                  = $provider->vat;
            $invoiceHead->vat_total            = $monthlyInvoice['vat_total'];
            $invoiceHead->provider_id          = $provider_id;
            $invoiceHead->company_id           = $monthlyInvoice['company']['id'];
            $invoiceHead->company_name         = $monthlyInvoice['company']['en_name'];
            $invoiceHead->provider_name        = Auth::guard('provider')->user()->name;
            $invoiceHead->total                = $monthlyInvoice['total'];
            $invoiceHead->total_qty                     = $monthlyInvoice['total_hrs'];
            $invoiceHead->total_rate                    = $monthlyInvoice['total_rate_fees'];
            $invoiceHead->total_count_orders            = $monthlyInvoice['total_count_orders'];
            $invoiceHead->order_sum_total               = $monthlyInvoice['order_sum_total'];
            $invoiceHead->item_sum_total                = $monthlyInvoice['item_sum_total'];
            $invoiceHead->item_amount_sum_total         =  $monthlyInvoice['item_amount_sum_total'];
            $invoiceHead->order_vat_total               =  $monthlyInvoice['order_vat_total'];
            $invoiceHead->material_vat_total            =  $monthlyInvoice['material_vat_total'];
            $invoiceHead->date                 = $year."-".$month."-01";
            $invoiceHead->status               = 'open';
            $invoiceHead->img_qr_code          = $filename;
            $invoiceHead->is_paid              = 'not-paid';
            $invoiceHead->vat_registration     = $provider->vat_registration;
            $invoiceHead->save();
            $invoiceHeadIds[] = $invoiceHead->id;
           if(isset($monthlyInvoice['invoiceDetail'])){
            foreach($monthlyInvoice['invoiceDetail'] as $element){


                        $InvoiceDetail = new QInvoiceDetail();
                        $InvoiceDetail->order_id = $element['order_id'];
                        $InvoiceDetail->qty = $element['hrs'];
                        $InvoiceDetail->rate =  $element['service_per_hr'];
                        $InvoiceDetail->service_name = $element['service_name'];
                        $InvoiceDetail->provider_id = $provider_id;
                        $InvoiceDetail->created_at = carbon::parse($element['created_at'])->format('Y-m-d h:i:s');
                        $InvoiceDetail->date = 	carbon::parse($element['created_at'])->format('Y-m-d h:i:s');
                        $InvoiceDetail->qty_rate_total = $element['qty_rate_total'];
                        $InvoiceDetail->invoice_head_id = $invoiceHead->id;
                        $InvoiceDetail->save();
                      if(!empty($element['qty'])){

                        $invoiceMaterial = new QInvoiceMaterial();
                        $invoiceMaterial->order_id = $element['order_id'];
                        $invoiceMaterial->qty = $element['qty'];
                        $invoiceMaterial->provider_id = $provider_id;
                        $invoiceMaterial->price =  $element['price'];
                        $invoiceMaterial->item_amount = $element['item_amount'];
                        $invoiceMaterial->created_at = carbon::parse($element['created_at'])->format('Y-m-d h:i:s');
                        $invoiceMaterial->date = carbon::parse($element['created_at'])->format('Y-m-d h:i:s');
                        $invoiceMaterial->invoice_head_id = $invoiceHead->id;
                        $invoiceMaterial->save();

                        }

            }

        }


        }


        $data['response'] =  'done';
        $data['success']  = true;
        $data['code']     = 200;

        return response()->json($data);


    }

}
