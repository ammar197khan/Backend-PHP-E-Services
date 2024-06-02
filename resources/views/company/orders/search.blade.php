@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="{{Request::url()}}">{{ __('language.Orders') }}</a></li>
        <li class="active">{{ __('language.Search') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    {{--<div class="panel-heading">--}}
                        {{--<a href="/company/order/create"><button type="button" class="btn btn-info"> Make an order </button></a>--}}
                    {{--</div>--}}

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
                                    <th>{{ __('language.Date') }}</th>
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
                                                {{isset($order->scheduled_at) ? $order->scheduled_at : __('language.Not selected yet') }}
                                            @endif
                                        </td>
                                        <td>@if($order->completed == 1) <span class="label label-success">{{ __('language.Completed') }}</span> @elseif($order->canceled == 1) @if($order->canceled_by == 'user') <span class="label label-danger">{{ __('language.Canceled By User') }}</span> @else <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span> @endif @else <span class="label label-primary">{{ __('language.Open') }}</span> @endif</td>
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
                                            <a title="View" href="/company/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
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
                        </div>
                    </div>
                </div>
                {{$orders->appends($_GET)->links()}}
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

            var parent_id = {{isset(request('main_cats')[0])?request('main_cats')[0]: 0}};

            if (parent_id) {
                $.ajax({
                    url: '/company/get_sub_cats/' + parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats').empty();
                        // $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                        $.each(data, function (i, sub_cat) {
                            var selected = [{{  implode(',',request('sub_cats') ? request('sub_cats') : []) }}]
                            var includes = selected.includes(sub_cat.id)

                            if (includes == true)
                                $('#sub_cats').append('<option value="' + sub_cat.id + '" selected>' + sub_cat.en_name + '</option>');
                            else
                                $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');
                        });
                        $('#sub_cats').selectpicker('refresh');
                    }
                });
            }
        });

        $('#main_cats').on('change', function (e) {
            var parent_id = $('#main_cats').val();

            if (parent_id) {
                $.ajax({
                    url: '/company/get_sub_cats/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats').empty();
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
