@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/collaboration/{{$id}}/statistics">Dashboard</a></li>
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

                    <form class="form-horizontal" method="get" action="/company/collaboration/{{$id}}/statistics/{{$type}}/search">
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
                                    <th>{{ __('language.User') }}</th>
                                    <th>{{ __('language.Date') }}</th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.Items') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{isset($order->id) ? $order->id : '-'}}</td>
                                        <td>{{isset($order->smo) ? $order->smo : '-'}}</td>
                                        <td>{{$order->type}}</td>
                                        <td>@if(isset($order->user_id))  {{$order->user->badge_id}} @else {{ __('language.Not selected yet') }} @endif</td>
                                        <td>{{isset($order->user_id) ? $order->user->en_name : __('language.Not selected yet') }}</td>
                                        <td>
                                            @if($order->type == 'Urgent')
                                                {{$order->created_at}}
                                            @elseif($order->type == 'Scheduled')
                                                {{$order->scheduled_at}}
                                            @else
                                                {{isset($order->scheduled_at) ? $order->scheduled_at : __('language.Not selected yet') }}
                                            @endif
                                        </td>
                                        <td>@if($order->completed == 1 && $order->canceled == 0)
                                                <span class="label label-success">Completed</span>
                                            @elseif($order->completed == 1 && $order->canceled == 1)
                                                @if($order->canceled_by == 'user')
                                                    <span class="label label-danger">{{ __('language.Canceled By User') }}</span>
                                                @elseif($order->canceled_by == 'tech')
                                                    <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span>
                                                @else <span class="label label-danger">{{ __('language.Canceled By Admin') }}</span>
                                                @endif
                                            @else <span class="label label-primary">{{ __('language.Open') }}</span> @endif</td>
                                        <td>{{$order->items->count()}}</td>
                                        <td>
                                            <a title="View" href="/company/collaboration/{{$id}}/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>

                                            {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$order->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>


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
