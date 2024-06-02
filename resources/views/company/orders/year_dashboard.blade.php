@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Orders') }}</li>
        <li class="active">{{$type}}</li>
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
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">

                    <form class="form-horizontal" method="get" action="/company/{{$type}}/search">
                        @include('provider.search')
                    </form>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('language.Order No') }}.</th>
                                    <th>{{ __('language.Mso NO.') }}</th>
                                    <th>{{ __('language.Type') }}</th>
                                    <th>{{ __('language.Badge ID') }}</th>
                                    <th>{{ __('language.Technician') }}</th>
                                    <th>{{ __('language.Date') }}</th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.Items Approval') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{isset($order->id) ? $order->id : '-'}}</td>
                                        <td>{{isset($order->smo) ? $order->smo : '-'}}</td>
                                        <td>{{$order->type}}</td>
                                        <td>@if(isset($order->tech_id))  {{$order->tech->badge_id}} @else {{ __('language.Not selected yet') }} @endif</td>
                                        <td>{{isset($order->tech_id) ? $order->tech->en_name : __('language.Not selected yet') }}</td>
                                        <td>
                                            @if($order->type == 'Urgent')
                                                {{$order->created_at}}
                                            @elseif($order->type == 'Scheduled')
                                                {{$order->scheduled_at}}
                                            @else
                                                {{isset($order->scheduled_at) ? $order->scheduled_at : __('language.Not selected yet') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->completed == 1 && $order->canceled == 0)
                                                <span class="label label-success">{{ __('language.Completed') }}</span>
                                            @elseif($order->completed == 0 && $order->canceled == 1 || $order->completed == 1 && $order->canceled == 1)
                                                @if($order->canceled_by == 'user')
                                                    <span class="label label-danger">{{ __('language.Canceled By User') }}</span>
                                                @elseif($order->canceled_by == 'tech')
                                                    <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span>
                                                @else <span class="label label-danger">{{ __('language.Canceled By Admin') }}</span>
                                                @endif @else <span class="label label-primary">{{ __('language.Open') }}</span>
                                            @endif
                                        </td>
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

                                    <!-- danger with sound -->

                                    <div class="message-box message-box-warning animated fadeIn" data-sound="alert/fail" id="message-box-danger-{{$order->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span> {{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to cancel order,are you sure') }}? .</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <form method="post" action="/company/order/cancel/{{$order->type}}" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="order_id" value="{{$order->id}}">
                                                        <button class="btn btn-warning btn-lg btn-warning btn-lg pull-right">{{ __('language.Cancel') }}</button>
                                                    </form>
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-right: 5px;"> {{ __('language.Close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->
                                @endforeach

                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </div>
            {{$orders->appends($_GET)->links()}}
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
                    $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');
                    $.each(data, function (i, sub_company) {
                        $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');
                    });
                }
            });
        });

        $('#main_cats').on('change', function (e) {
            var parent_id = $('#main_cats').val();
            console.log(parent_id);
            if (parent_id) {
                $.ajax({
                    url: '/company/get_sub_category/'+parent_id,
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
