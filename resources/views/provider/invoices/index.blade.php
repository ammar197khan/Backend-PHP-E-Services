@php
  $month = isset($month) ? $month : NULL;
  $year = isset($year) ? $year : NULL;
@endphp
@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Orders') }}</li>
        <li class="active">View Order</li>
    </ul>
    <!-- END BREADCRUMB -->

    <!-- PAGE CONTENT WRAPPER -->
    <form class="form-horizontal" action="/provider/get-monthly-invoices" method="GET">
        <div class="form-group col-md-2">
        </div>
        <div class="form-group col-md-2 {{ $errors->has('from') ? ' has-error' : '' }}">
            <div class="input-group">
                @php
                $already_selected_value = $curyear = date("Y");;
                $earliest_year = 1950;
                $curyear = date("Y");
                @endphp

                   <select name="year" class="form-control" id="year">
                      <option value="">Year</option>
                          @foreach (range(date('Y'), $earliest_year) as $x)
                                     <option value="{{ $x }}" {{   !empty(app('request')->input('year')) &&  app('request')->input('year') == $x? 'selected': ''  }}  {{  $curyear == $x? 'selected' : ''   }} >{{ $x }}</option>
                          @endforeach
                   </select>
                <span class="input-group-addon" ><span class="fa fa-calendar" style="color: black;"></span></span>
            </div>
        </div>

        <div class="form-group col-md-2 {{ $errors->has('to') ? ' has-error' : '' }}">
            <div class="input-group">
                <select name="month" size='1' class="form-control" id="month">
                    <option value="" selected>Month</option>
                    @for ($i = 0; $i < 12; $i++)
                    @php
                        $time = strtotime(sprintf('%d months', $i));
                        $label = date('F', $time);
                        $value = date('n', $time);
                        $curmonth = date("n");
                        $curyear = date("Y");
                        @endphp
                    <option value='{{  $value}}' {{ !empty(app('request')->input('month')) &&  app('request')->input('month') == $value? 'selected': ''  }}  {{  $curmonth == $value ? 'selected' : ''  }}>{{ $label }}</option>";
                    @endfor

                </select>
                <span class="input-group-addon" ><span class="fa fa-calendar" style="color: black;"></span></span>
            </div>
        </div>
        <div class="form-group col-md-2">
            <div class="input-group">
            <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </div>
        <div class="form-group col-md-2">
            <div class="input-group" id="generate-invoice">
                @if(($data['invoiceGenerated']) && ($data['status'] != 'close') )
                    <a href="javascript:void(0)" class="btn btn-info" style = "text-decoration:none"   onclick="generateInvoice(true, `re-generate`);" >Re Generate Invoice</a>
                @elseif($data['status'] != 'close')
                    <a href="javascript:void(0)" class="btn btn-info"  style = "text-decoration:none"  onclick="generateInvoice();" >Generate Invoice</a>

                @endif
            </div>
        </div>
        @if(($data['invoiceGenerated']) && ($data['status'] != 'close') )
        <div class="form-group col-md-2">
            <div class="input-group" id="invoice-status">
                @if($data['invoiceGenerated'])
                <a href="javascript:void(0)" class="btn btn-primary" style = "text-decoration:none"  onclick="closeInvoice();" >Close Invoice</a>
                @endif
            </div>
        </div>
        @endif

    </form>

    {{-- @foreach($companies as $company) --}}
    <div class="page-content-wrap">
        <div class="row">

            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" id="invocie-dta">

                        <table class="table table-striped sticky-header" >
                            <thead>
                                <tr id="myHeader">
                                         <th class="col-md-1 text-center">Name</th>
                                         <th class="col-md-1 text-center">Status</th>
                                         <th class="col-md-1 text-center">{{ __('language.Count orders') }}</th>
                                         <th class="col-md-1 text-center">{{ __('language.Total orders') }}</th>
                                         <th class="col-md-1 text-center">{{ __('language.Total items') }}</th>
                                         <th class="col-md-1 text-center">{{ __('language.Total') }}</th>
                                         <th class="col-md-1 text-center">{{ __('language.VAT') }}%</th>
                                         <th class="col-md-1 text-center">{{ __('language.Vat Total') }}</th>
                                         <th class="col-md-1 text-center">{{ __('language.Is Paid') }}</th>
                                         <th class="col-md-2 text-center"></th>
                                        </tr>

                                    </thead>

                                    <tbody>
                                     @foreach ( $data  as $monthlyInvoice )

                                     @if(!empty($monthlyInvoice['invoiceDetail']) && count($monthlyInvoice['invoiceDetail']) > 0)

                                        <tr>
                                            <td class="text-center">{{  $monthlyInvoice['company']['en_name']  }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['status'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['total_count_orders'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['order_sum_total'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['item_sum_total'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['total'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['vat'] }} %</td>
                                            <td class="text-center">{{  $monthlyInvoice['vat_total'] }}</td>
                                            <td>
                                                @if($data['invoiceGenerated'])
                                                <div class="form-check" style="float: centre;">
                                                <label class="form-check-label">
                                                  <input type="radio" class="form-check-input" name="is_paid" onclick="isPaid({{ $monthlyInvoice['company']['id'] }} , 'paid');" value="paid" style="margin-right: 3px" {{   !empty($monthlyInvoice['is_paid']) && $monthlyInvoice['is_paid'] == 'paid' ? 'checked disabled' : '' }} >Paid
                                                </label>
                                              </div>
                                              {{-- <div class="form-check" style="float: left;">
                                                <label class="form-check-label">
                                                  <input type="radio" class="form-check-input" name="is_paid" onclick="isPaid({{ $monthlyInvoice['company']['id'] }} , 'not-paid');"  value="not-paid"  style="margin-right: 3px" {{   !empty($monthlyInvoice['is_paid']) && $monthlyInvoice['is_paid'] == 'not-paid' ? 'checked' : '' }}>Not Paid
                                                </label>
                                              </div> --}}
                                              @endif
                                            </td>
                                            <td class="text-center">
                                                @if($data['invoiceGenerated'])
                                                    <a href="#" class="col-md-6" onclick="print('{{ url("provider/invoices/".$monthlyInvoice['company']['id']."/orders/print?month=$month&year=$year") }}')" style="text-decoration:none">
                                                        <i class="fa fa-print" aria-hidden="true"></i>
                                                        {{ __('language.Orders Invoice') }}
                                                      </a>
                                                      <a href="#" class="col-md-6" onclick="print('{{ url("provider/invoices/".$monthlyInvoice['company']['id']."/materials/print?month=$month&year=$year") }}')" style="text-decoration:none">
                                                        <i class="fa fa-print" aria-hidden="true"></i>
                                                        {{ __('language.Materials Invoice') }}
                                                      </a>
                                                @endif
                                            </td>
                                         </tr>
                                         @endif
                                         @endforeach
                                        </tbody>
                                    </table>
                    </div>
                </div>
            </div>
        </div>
    {{-- @endforeach --}}
<script type="text/javascript">
  function print(url) {
      mywindow = window.open(url, 'PRINT', 'height=800,width=590');
      mywindow.document.close(); // necessary for IE >= 10
      mywindow.focus(); // necessary for IE >= 10*/
      // mywindow.print();
      // mywindow.close();
  }

  $('ducoment').ready(function(){
    getMonthlyInvoice();
  });

  $('#month').on('change', function (e) {
    getMonthlyInvoice();
    });

    function getMonthlyInvoice(){
        var month = $('#month').val();
        var year = $('#year').val();
        if (month) {
            $.ajax({
                url: 'get-monthly-invoices?month='+month+'&year='+year,
                type: "GET",
                dataType: "json",
                success: function (data) {
                     console.log(data);
                     var row = '';
                     var generate_invoice = '';
                     var invoice_status = '';
                     var count = true;
                     console.log(data.success);
                        if(data.success == true){
                            jQuery.each(data.response, function(index, value){

                              if(!isEmpty(value.elements)){
                                count = false;
                                    row  += '<table class="table table-striped sticky-header" ><thead> <tr id="myHeader"> <th class="col-md-1 text-center">Name</th> <th class="col-md-1 text-center">Status</th> <th class="col-md-1 text-center">{{ __('language.Count orders') }}</th> <th class="col-md-1 text-center">{{ __('language.Total orders') }}</th> <th class="col-md-1 text-center">{{ __('language.Total items') }}</th> <th class="col-md-1 text-center">{{ __('language.Sub Total') }}</th> <th class="col-md-1 text-center">{{ __('language.VAT') }}</th> <th class="col-md-1 text-center">{{ __('language.Total') }}</th> <th class="col-md-2 text-center"></th> </tr> </thead> <tbody> <tr> <td class="text-center">'+ value.company.en_name +'</td> <td class="text-center">'+ value.status +'</td><td class="text-center">'+ value.total_count +'</td> <td class="text-center">'+ value.total_orders_amount +'</td> <td class="text-center">'+ value.total_items_amount +'</td> <td class="text-center">'+ value.total +'</td> <td class="text-center">'+ value.orders_vat +'%</td> <td class="text-center">'+  value.vat_total +'</td> <td class="text-center">';
                                    if(data.response.invoiceGenerated == true){
                                        row  += '<a href="#" class="col-md-6" onclick="print(`'+ APP_URL + '/provider/invoices/'+value.company.id + '/orders/print?month='+month+'&year='+year+''+'`)" style="text-decoration:none"> <i class="fa fa-print" aria-hidden="true"></i> Orders Invoice </a> <a href="#" class="col-md-6" onclick="print(`'+ APP_URL + '/provider/invoices/'+value.company.id + '/materials/print?month='+month+'&year='+year+''+'`)" style="text-decoration:none"> <i class="fa fa-print" aria-hidden="true"></i> Materials Invoice</a>';
                                    }

                                    row  += ' </td> </tr> </tbody></table>';

                                }
                            });

                            if(data.response.invoiceGenerated == true &&  data.response.status !== 'close' ){
                                invoice_status = '<a href="javascript:void(0)" class="btn btn-primary" style = "text-decoration:none"  onclick="closeInvoice();" >Close Invoice</a>';
                                generate_invoice  = '<a href="javascript:void(0)" class="btn btn-info" style = "text-decoration:none"   onclick="generateInvoice(true, `re-generate`);" >Re Generate Invoice</a>';

                            }else if(data.response.invoiceGenerated == false){
                                generate_invoice  = '<a href="javascript:void(0)" class="btn btn-info"  style = "text-decoration:none"  onclick="generateInvoice();" >Generate Invoice</a>';

                            }else{
                                invoice_status = '';
                                generate_invoice  = '';
                            }
                            $('#invoice-status').html(invoice_status);
                            $('#generate-invoice').html(generate_invoice);

                            $('#invocie-dta').html(row);
                        }
                        console.log('count' + count);
                        if(count){
                            $('#invocie-dta').html("");
                            $('#invoice-status').html("");
                            $('#generate-invoice').html("");

                        }

                }
            });

        }
    }




</script>
@endsection
@section('scripts')
<script>
    function generateInvoice(isUpdate = null , type = null) {
        var month = $('#month').val();
        var year = $('#year').val();
        if (month) {
            $.ajax({
                url: "{{ route('provider.generate.monthly.invoice') }}"+"?month="+month+"&year="+year+"&isUpdate="+isUpdate + "&type="+ type ,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    console.log();
                    if(data.success == true){
                    swal({
                        title: "Success!",
                        text: "Invoice Generated Successfully!.",
                        timer: 15000
                    });
                    location.reload();
                }
                }
            });

        }
    }
    function closeInvoice(isUpdate = null) {
        var month = $('#month').val();
        var year = $('#year').val();
        if (month) {

            $.ajax({
                url: "{{ route('provider.close.invoice') }}"+"?month="+month+"&year="+year,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    //  console.log(JSON.stringify(data) + 'this');
                    if(data.success == true){
                        swal({
                        title: "Success!",
                        text: "Invoice Close Successfully!.",
                        timer: 15000
                    });
                    location.reload();
                    }

                }
            });

        }
    }

    function isPaid(companyId, is_paid) {
        var month = $('#month').val();
        var year = $('#year').val();
        if (month) {
            $.ajax({
                url: "{{ route('provider.is.paid.store') }}"+"?is_paid="+is_paid+"&companyId="+companyId+"&month="+month+"&year="+year,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    //  console.log(JSON.stringify(data) + 'this');
                    if(data.success == true){
                        swal({
                        title: "Success!",
                        text: "Invoice Update Successfully!.",
                        timer: 3000
                    });
                    location.reload();
                    }

                }
            });

        }
    }

</script>
@endsection
