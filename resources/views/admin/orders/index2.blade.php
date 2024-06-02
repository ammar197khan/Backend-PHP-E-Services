@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
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
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <form class="form-horizontal" method="get" action="/admin/company/{{$id}}/statistics/{{$type}}/search">
                        @include('provider.search')
                    </form>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('language.Order No') }}.</th>
                                    <th>{{ __('language.type') }}</th>
                                    <th>{{ __('language.Badge ID') }}</th>
                                    <th>{{ __('language.Company') }}</th>
                                    <th>{{ __('language.Provider') }}</th>
                                    <th>{{ __('language.Technician') }}</th>
                                    <th>{{ __('language.Date') }}</th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.Items') }}</th>
                                    {{--<th>Operations</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{isset($order->id) ? $order->id : '-'}}</td>
                                        <td>{{$order->type}}</td>
                                        <td>@if(isset($order->tech_id))  {{$order->tech->badge_id}} @else {{ __('language.Not selected yet') }} @endif</td>
                                        <td>{{isset($order->company->en_name) ? $order->company->en_name : '-'}}</td>
                                        <td>{{isset($order->provider->en_name) ? $order->provider->en_name : '-'}}</td>
                                        <td>{{isset($order->tech_id) ? $order->tech->en_name : 'Not selected yet'}}</td>
                                        <td>
                                            @if($order->type == 'urgent')
                                                {{$order->created_at}}
                                            @elseif($order->type == 'scheduled')
                                                {{$order->scheduled_at}}
                                            @else
                                                {{isset($order->scheduled_at) ? $order->scheduled_at : 'Not selected yet'}}
                                            @endif
                                        </td>
                                        <td>@if($order->completed == 1 && $order->canceled == 0) <span class="label label-success">Completed</span> @elseif($order->completed == 0 && $order->canceled == 1) @if($order->canceled_by == 'user') <span class="label label-danger">{{ __('language.Canceled By User') }}</span> @elseif($order->canceled_by == 'tech') <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span> @else <span class="label label-danger">{{ __('language.Canceled By Admin') }}</span> @endif @else <span class="label label-primary">{{ __('language.Open') }}</span> @endif</td>
                                        <td>{{$order->items->count()}}</td>
                                        <td>
                                            <a title="View" href="/admin/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                        </td>
                                        {{--<td>--}}
                                        {{--<a title="View" href="/providerprovider/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>--}}
                                        {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$order->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                        {{--</td>--}}
                                    </tr>

                                @endforeach

                                </tbody>
                            </table>
                            <input type="hidden" id="company_id" value="{{$id}}" />


                            {{$orders->appends($_GET)->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

                var parent_id = {{$id}};
                if (parent_id) {
                    $.ajax({
                        url: '/admin/get_sub_company/'+parent_id,
                        type: "GET",

                        dataType: "json",

                        success: function (data) {
                            $('#sub_company').empty();
                            $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');
                            $.each(data, function (i, sub_company) {
                                var selected = [{{  implode(',',request('sub_company') ? request('sub_company') : []) }}];
                                var includes = selected.includes(sub_company.id);

                                if(includes == true)
                                    $('#sub_company').append('<option value="' + sub_company.id + '" selected>' + sub_company.en_name + '</option>');
                                else
                                    $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');
                            });
                            $('.select').selectpicker('refresh');
                        }
                    });

                }
        });
        $('#main_cats').on('change', function () {
            var parent_id = $('#main_cats').val();
            var company_id = $('#company_id').val();

            if (parent_id) {
                $.ajax({
                    url: '/admin/company/'+company_id+'/get_sub_cats/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats').empty();
                        $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                        $.each(data, function (i, sub_cat) {
                            var selected = [{{  implode(',',request('sub_cats') ? request('sub_cats') : []) }}]
                            var includes = selected.includes(sub_cat.id)

                            if(includes == true)
                                $('#sub_cats').append('<option value="' + sub_cat.id + '" selected>' + sub_cat.en_name + '</option>');
                            else
                                $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');
                        });
                        $('.select').selectpicker('refresh');
                    }
                });

            }
        });

    </script>
@endsection
