@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/company/orders/{{$type}}">{{ __('language.Orders') }}</a></li>
        <li class="active">{{$type}}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a @if(Request::is('company/orders/urgent')) href="/company/orders/urgent/excel/view" @else href="/company/orders/scheduled/excel/view" > @endif
                            <button type="button" class="btn btn-info"><i class="fa fa-upload"></i>  </button></a>

                        <a @if(Request::is('company/orders/urgent')) href="/company/orders/open/urgent/excel/view" @else href="/company/orders/open/scheduled/excel/view" > @endif
                            <button type="button" class="btn btn-info"><i class="fa fa-upload"></i> {{ __('language.Import Open Orders') }} </button></a>

                        <a href="/company/orders/{{$type}}/invoice/request" style="float: right;"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i>{{ __('language.Export Orders') }}</button></a>

                        <div style="float: right; margin-right: 10px">
                            <select name="company_status" class="form-control" onchange="location = this.value;">
                                <option @if(request('company_status') == 'all') selected @endif value="/company/orders/all?company_status=all">{{ __('language.All') }}</option>
                                <option @if(request('company_status') == 'urgent') selected @endif value="/company/orders/urgent?company_status=urgent">{{ __('language.Urgent') }}</option>
                                <option @if(request('company_status') == 'scheduled') selected @endif value="/company/orders/scheduled?company_status=scheduled">{{ __('language.Scheduled') }}</option>
                                <option @if(request('company_status') == 're_scheduled') selected @endif value="/company/orders/re_scheduled?company_status=re_scheduled">{{ __('language.Re-Scheduled') }}</option>
                                <option @if(request('company_status') == 'canceled') selected @endif value="/company/orders/canceled?company_status=canceled">{{ __('language.Canceled') }}</option>
                                <option @if(request('company_status') == 'emergency') selected @endif value="/company/orders/emergency?company_status=emergency">Emergency</option>
                            </select>
                        </div>
                    </div>

                        <form class="form-horizontal" method="get" action="/company/orders/{{$type}}/search" novalidate>
                              @include('provider.search')
                        </form>

                     <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('language.Mso NO.') }}</th>
                                    <th>{{ __('language.Type') }}</th>
                                    <th>{{ __('language.Badge ID') }}</th>
                                    <th>{{ __('language.User') }}</th>
                                    <th>{{ __('language.Scheduled Date') }} </th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.Items Approval') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{$order->id}}</td>
                                        <td>{{isset($order->smo) ? $order->smo : '-'}}</td>
                                        <td>{{$order->type}}</td>
                                        <td>{{$order->user->badge_id}}</td>
                                        <td>{{$order->user->en_name}}</td>
                                        <td>
                                            @if($order->type == 'urgent')
                                                {{$order->created_at}}
                                            @elseif($order->type == 'scheduled')
                                                {{$order->scheduled_at}}
                                            @else
                                                {{isset($order->scheduled_at) ? $order->scheduled_at : 'Not scheduled yet'}}
                                            @endif
                                        </td>
                                        <td>
                                          @if($order->completed == 1 && $order->canceled == 0)
                                            <span class="label label-success">{{ __('language.Completed') }}</span>
                                          @elseif($order->completed == 0 && $order->canceled == 1)
                                            @if($order->canceled_by == 'user') <span class="label label-danger">{{ __('language.Canceled By User') }}</span>
                                            @elseif($order->canceled_by == 'tech') <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span>
                                            @else <span class="label label-danger">{{ __('language.Canceled By Admin') }}</span>
                                            @endif
                                          @else <span class="label label-primary">{{ __('language.Open') }}</span> @endif</td>
                                        <td>
                                          @if ($order->isAdminApprovalRequired())
                                            <span class="label label-warning">{{ __('language.Required By Admin') }}</span>
                                          @elseif ($order->isUserApprovalRequired())
                                            <span class="label label-warning">{{ __('language.Required By User') }}</span>
                                          @else
                                            <span class="label label-success">{{ __('language.Not Required') }}</span>
                                          @endif
                                        </td>
                                        <td>
                                            @if(company()->hasPermissionTo('View details order'))
                                                <a title="View" href="/company/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                            @endif
                                            {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$order->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                        </td>
                                    </tr>

                                    {{--<!-- danger with sound -->--}}
                                    {{--<div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$order->id}}">--}}
                                        {{--<div class="mb-container">--}}
                                            {{--<div class="mb-middle warning-msg alert-msg">--}}
                                                {{--<div class="mb-title"><span class="fa fa-times"></span>Alert !</div>--}}
                                                {{--<div class="mb-content">--}}
                                                    {{--<p>Your are about to delete a warehouse item,and you won't be able to restore its data again .</p>--}}
                                                    {{--<br/>--}}
                                                    {{--<p>Are you sure ?</p>--}}
                                                {{--</div>--}}
                                                {{--<div class="mb-footer buttons">--}}
                                                    {{--<button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>--}}
                                                    {{--<form method="post" action="/company/warehouse/item/delete" class="buttons">--}}
                                                        {{--{{csrf_field()}}--}}
                                                        {{--<input type="hidden" name="item_code" value="{{$order->code}}">--}}
                                                        {{--<button type="submit" class="btn btn-danger btn-lg pull-right">Delete</button>--}}
                                                    {{--</form>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<!-- end danger with sound -->--}}
                                @endforeach

                                </tbody>
                            </table>
                            {{$orders->appends($_GET)->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
                $.ajax({
                    url: '/company/get_sub_company',
                    type: "GET",
                    dataType: "json",

                    success: function (data) {
                        $('#sub_company').empty();
                        // $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');
                        $.each(data, function (i, sub_company) {
                            var selected = [{{  implode(',',request('sub_company') ? request('sub_company') : []) }}]
                            var includes = selected.includes(sub_company.id)

                            if(includes == true)
                                $('#sub_company').append('<option value="' + sub_company.id + '" selected>' + sub_company.en_name + '</option>');
                            else
                                $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');
                        });
                        $('#sub_company').selectpicker('refresh');
                    }
                });
        });

        $('#main_cats').on('change', function (e) {
            var parent_id = $('#main_cats').val();
            if (parent_id) {
                $.ajax({
                    url: '/company/get_sub_category/'+parent_id,
                    type: "GET",
                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats').empty();
                        // $('#sub_cats').selectpicker('refresh');
                        // $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                        $.each(data, function (i, sub_cat) {
                            var selected = [{{  implode(',',request('sub_cats') ? request('sub_cats') : []) }}]
                            var includes = selected.includes(sub_cat.id)

                            if(includes == true)
                                $('#sub_cats').append('<option value="' + sub_cat.id + '" selected>' + sub_cat.en_name + '</option>');
                            else
                                $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');
                        });
                        $('#sub_cats').selectpicker('refresh');
                    }
                });

            }
        });
    </script>
@endsection
