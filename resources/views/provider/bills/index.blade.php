@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">Bills</li>
    </ul>
    <!-- END BREADCRUMB -->

    <style>
        .image
        {
            height: 50px;
            width: 50px;
            border: 1px solid #29B2E1;
            border-radius: 100px;
            box-shadow: 2px 2px 2px darkcyan;
        }
    </style>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div>
                            {{isset($from)?$from:''}} &nbsp;
                            {{isset($to)?$to:''}} &nbsp;
                            {{isset($sub_company)?$sub_company->en_name:''}} &nbsp;
                            {{isset($main_cats)?$main_cats->en_name:''}} &nbsp;
                            {{isset($sub_cats)?$sub_cats->en_name:''}}
                        </div>

                        @if(Request::is("provider/collaboration/$id/bills"))
                            <a href="/provider/collaboration/{{$id}}/bills_export" style="float: right;">
                                <button type="button" class="btn btn-success"> Export Orders <i class="fa fa-file-excel-o"></i> </button>
                            </a>
                        @else
                            <a href="/provider/collaboration/{{$id}}/bills_export/{{$search}}" style="float: right;">
                                <button type="button" class="btn btn-success"> Export Orders <i class="fa fa-file-excel-o"></i> </button>
                            </a>
                            {{--<a title="View bills" style="float: right; padding-right: 10px" href="/provider/collaboration/{{$id}}/bills/view">--}}
                                {{--<button class="btn btn-primary btn-condensed"><i class="fa fa-money"></i> Bills</button></a>--}}
                            <form method="post" action="/provider/collaboration/{{$id}}/bills/view" style="float: right; padding-right: 10px">
                                {{csrf_field()}}
                                <button class="btn btn-primary btn-condensed"><i class="fa fa-money"></i> {{ __('language.Bills') }}</button>
                                <input type="hidden" name="order_data" value="{{isset($bills_export)?$bills_export:''}}">
                            </form>
                        @endif
                        {{--<a href="/provider/collaboration/{{$id}}/bills_export" style="float: right; margin-right: 3px;">--}}
                        {{--@foreach($orders as $value)--}}
                        {{--<input type="hidden" name="orders" value="{{$value}}">--}}
                        {{--@endforeach--}}
                    </div>

                    <form class="form-horizontal" method="get" action="/provider/collaboration/{{$id}}/bills/search">
                        @if(isset($cats))
                            @include('search')
                        @endif
                    </form>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('language.Order No') }}.</th>
                                    <th>{{ __('language.Mso NO.') }}</th>
                                    <th>{{ __('language.Type') }}</th>
                                    <th>{{ __('language.User') }}</th>
                                    <th>{{ __('language.Technician') }}</th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.Service Fee') }}</th>
                                    <th>Items Total</th>
                                    <th>{{ __('language.Total Amount') }}</th>
                                    <th>{{ __('language.Date') }}</th>
                                    {{--<th>Operations</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{isset($order->id) ? $order->id : '-'}}</td>
                                        <td>{{isset($order->smo) ? $order->smo : '-'}}</td>
                                        <td>{{$order->type}}</td>
                                        <td>{{isset($order->user_id) ? $order->user->en_name : __('language.Not selected yet') }}</td>
                                        <td>{{isset($order->tech_id) ? $order->tech->en_name : __('language.Not selected yet') }}</td>
                                        <td>@if($order->completed == 1 && $order->canceled == 0) <span class="label label-success">Completed</span> @elseif($order->completed == 0 && $order->canceled == 1) @if($order->canceled_by == 'user') <span class="label label-danger">{{ __('language.Canceled By User') }}</span> @elseif($order->canceled_by == 'tech') <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span> @else <span class="label label-danger">{{ __('language.Canceled By Admin') }}</span> @endif @else <span class="label label-primary">{{ __('language.Open') }}</span> @endif</td>
                                        <td>{{$order->order_total}} S.R</td>
                                        <td>{{$order->item_total}} S.R</td>
                                        <td>{{$order->item_total+$order->order_total}} S.R</td>
                                        <td>{{$order->created_at}}</td>
                                        <td>
                                            <a title="View" href="/provider/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                            {{--<a title="View bills" href="/provider/collaboration/{{$id}}/bills/{{$order->id}}/view"><button class="btn btn-primary btn-condensed"><i class="fa fa-money"></i></button></a>--}}
                                        </td>
                                        {{--<td>--}}
                                        {{--<a title="View" href="/providerprovider/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>--}}
                                        {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$order->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                        {{--</td>--}}
                                    </tr>

                                @endforeach

                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
                {{$orders->appends($_GET)->links()}}
            </div>
        </div>
    </div>
    <script>

        $(document).ready(function (e) {
            var parent_id = $('#company_id').val();
            if (parent_id) {
                $.ajax({
                    url: '/provider/get_sub_company/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_company').empty();
                        $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');
                        $.each(data, function (i, sub_company) {
                            $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');
                        });
                    }
                });

            }
        });

        // $('#company_id').on('change', function (e) {
        //     var parent_id = $('#company_id').val();
        //     if (parent_id) {
        //         $.ajax({
        //             url: '/provider/get_sub_company/'+parent_id,
        //             type: "GET",
        //
        //             dataType: "json",
        //
        //             success: function (data) {
        //                 $('#sub_company').empty();
        //                 $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');
        //                 $.each(data, function (i, sub_company) {
        //                     $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');
        //                 });
        //             }
        //         });
        //
        //     }
        // });

        $('#main_cats').on('change', function (e) {
            var parent_id = e.target.value;
            var company_id = $('#company_id').val();
            if (parent_id) {
                $.ajax({
                    url: '/provider/'+company_id+'/get_sub_category_provider/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats').empty();
                        $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                        $.each(data, function (i, sub_cat) {
                            $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');
                        });
                    }
                });

            }
        });

    </script>
@endsection
@section('scripts')
  <script type="text/javascript">
      $("#ise_default").ionRangeSlider();
      $("#ise_step").ionRangeSlider({
        type: "double",
        grid: true,
        min: 0,
        max: 10000,
        from: {{ request('price_range') ? explode(';', request('price_range'))[0] : 0 }},
        to: {{ request('price_range') ? explode(';', request('price_range'))[1] : 1000 }},
        step: 50
      });
  </script>
@endsection
