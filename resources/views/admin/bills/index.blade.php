@php
  $from = isset($from) ? $from : NULL;
  $to = isset($to) ? $to : NULL;
@endphp
@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Orders') }}</li>
        <li class="active"> {{ __('language.View Order') }} </li>
    </ul>
    <!-- END BREADCRUMB -->

    <!-- PAGE CONTENT WRAPPER -->
    <form class="form-horizontal" method="get" action="/admin/provider/bills/all/search" style="padding-bottom:5px;">

        <div class="form-group col-md-2">
        </div>
        <div class="form-group col-md-2 {{ $errors->has('from') ? ' has-error' : '' }}">
            <div class="input-group">
                @php
                $already_selected_value = $curyear = date("Y");
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
        <div class="form-group col-md-2">
            <div class="input-group" id="invoice-status">
                @if($data['invoiceGenerated'])
                <a href="javascript:void(0)" class="btn btn-primary" style = "text-decoration:none"  onclick="closeInvoice();" >Close Invoice</a>
                @endif
            </div>
        </div>

    </form>

    {{-- @foreach($providers as $provider) --}}
    @if(!empty($data['data']))
    @foreach ( $data['data']  as $monthlyInvoice )

    <div class="page-content-wrap">
        {{-- <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <table class="table table-striped sticky-header">
                            <thead>
                                <tr id="myHeader">
                                    <th class="text-center">{{ __('language.Name') }}</th>
                                    <th class="text-center">{{ __('language.Count orders') }}</th>
                                    <th class="text-center">{{ __('language.Total orders') }}</th>
                                    <th class="text-center">{{ __('language.Total items') }}</th>
                                    <th class="text-center">{{ __('language.Sub Total') }}</th>
                                    <th class="text-center">{{ __('language.VAT') }}</th>
                                    <th class="text-center">{{ __('language.Total') }}</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>

                            <tbody>
                              <tr>
                                <td class="text-center">{{ $provider->en_name }}</td>
                                <td class="text-center">{{ $provider->BillToQreeb($from, $to,request('main_cats'), request('provider_name'))['total_count'] }}</td>
                                <td class="text-center">{{ $provider->BillToQreeb($from, $to,request('main_cats'), request('provider_name'))['total_orders_amount'] }}</td>
                                <td class="text-center">{{ $provider->BillToQreeb($from, $to,request('main_cats'), request('provider_name'))['total_items_amount'] }}</td>
                                <td class="text-center">{{ $provider->BillToQreeb($from, $to,request('main_cats'), request('provider_name'))['total'] }}</td>
                                <td class="text-center">{{ $provider->BillToQreeb($from, $to,request('main_cats'), request('provider_name'))['orders_vat'] }}</td>
                                <td class="text-center">{{ $provider->BillToQreeb($from, $to)['total'] + $provider->BillToQreeb($from, $to)['total'] * $provider->BillToQreeb($from, $to)['orders_vat']/100 }}</td>
                                <td class="text-center">
                                  <a href="#" onclick="print('{{ url("admin/provider/".$provider->id."/invoice_bills?from=$from&to=$to") }}')"><i class="fa fa-bitcoin" aria-hidden="true"> {{ __('language.Orders Invoice') }}</i></a>
                                </td>
                                <td class="text-center">
                                  <a href="#" onclick="print('{{ url("admin/provider/".$provider->id."/invoice?from=$from&to=$to") }}')"><i class="fa fa-print" aria-hidden="true"> {{ __('language.Materials Invoice') }}</i></a>
                                </td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}


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


                                        <tr>
                                            <td class="text-center">{{  $monthlyInvoice['provider']['ar_name'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['status'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['total_count_orders'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['order_sum_total'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['item_amount_sum_total'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['total'] }}</td>
                                            <td class="text-center">{{  $monthlyInvoice['vat'] }} %</td>
                                            <td class="text-center">{{  $monthlyInvoice['vat_total'] }}</td>
                                            <td>
                                                @if($data['invoiceGenerated'])
                                                <div class="form-check" style="float: centre;">
                                                <label class="form-check-label">
                                                  <input type="radio" class="form-check-input" name="is_paid{{ $monthlyInvoice['provider']['id'] }}" onclick="isPaid({{ $monthlyInvoice['provider']['id'] }} , 'paid');" value="paid" style="margin-right: 3px" {{   !empty($monthlyInvoice['is_paid']) && $monthlyInvoice['is_paid'] == 'paid' ? 'checked disabled' : '' }} >Paid
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
                                                    <a href="#" class="col-md-6" onclick="print('{{ url("admin/provider/".$monthlyInvoice['provider']['id']."/invoice_bills?month=$month&year=$year&provider_id=".$monthlyInvoice['provider']['id']."") }}')" style="text-decoration:none">
                                                        <i class="fa fa-print" aria-hidden="true"></i>
                                                        {{ __('language.Orders Invoice') }}
                                                      </a>

                                                      <a href="#" class="col-md-6" onclick="print('{{ url("admin/provider/".$monthlyInvoice['provider']['id']."/invoice?month=$month&year=$year&provider_id=".$monthlyInvoice['provider']['id']."") }}')" style="text-decoration:none">
                                                        <i class="fa fa-print" aria-hidden="true"></i>
                                                        {{ __('language.Materials Invoice') }}
                                                      </a>
                                                @endif
                                            </td>
                                         </tr>
                                         {{-- @endif --}}
                                         {{-- @endforeach --}}

                                        </tbody>
                                    </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @endif
<script type="text/javascript">
  function print(url) {
      mywindow = window.open(url, 'PRINT', 'height=700,width=590');
      mywindow.document.close(); // necessary for IE >= 10
      mywindow.focus(); // necessary for IE >= 10*/
      // mywindow.print();
      // mywindow.close();
  }
</script>
@endsection
@section('scripts')
<script>

    function generateInvoice(isUpdate = null , type = null) {
        var month = $('#month').val();
        var year = $('#year').val();
        // alert('month', month, 'year' , year);
        if (month) {
            $.ajax({
                url: "{{ route('admin.generate.monthly.invoice') }}"+"?month="+month+"&year="+year+"&isUpdate=true&type="+ type,
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

                url: "{{ route('admin.close.invoice') }}"+"?month="+month+"&year="+year,
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

    function isPaid(providerId, is_paid) {
        var month = $('#month').val();
        var year = $('#year').val();
        if (month) {
            $.ajax({
                url: "{{ route('admin.is.paid.store') }}"+"?is_paid="+is_paid+"&providerId="+providerId+"&month="+month+"&year="+year,
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
