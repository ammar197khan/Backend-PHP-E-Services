@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
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
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                    <a href="/company/bills/excel/export" style="float: right; margin-right: 3px;"><button type="button" class="btn btn-success"> {{ __('language.Export categories') }} <i class="fa fa-file-excel-o"></i> </button></a>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('language.Order No') }}.</th>
                                    <th>{{ __('language.Mso NO.') }}</th>
                                    <th>{{ __('language.Type') }}</th>
                                    <th>{{ __('language.Technician') }}</th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.Service Fee Technician') }}</th>
                                    <th>{{ __('language.Items Tota Technician') }}</th>
                                    <th>{{ __('language.Total Amount Technician') }}</th>
                                    {{--<th>Operations</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{isset($order->id) ? $order->id : '-'}}</td>
                                        <td>{{isset($order->smo) ? $order->smo : '-'}}</td>
                                        <td>{{$order->type}}</td>
                                        <td>{{isset($order->tech_id) ? $order->tech->en_name : __('language.Not selected yet') }}</td>
                                        <td>@if($order->completed == 1 && $order->canceled == 0) <span class="label label-success">{{ __('language.Completed') }}</span> @elseif($order->completed == 0 && $order->canceled == 1) @if($order->canceled_by == 'user') <span class="label label-danger">{{ __('language.Canceled By User') }}</span> @elseif($order->canceled_by == 'tech') <span class="label label-danger">{{ __('language.Canceled By Technician') }}</span> @else <span class="label label-danger">{{ __('language.Canceled By Admin') }}</span> @endif @else <span class="label label-primary">{{ __('language.Open') }}</span> @endif</td>
                                        <td>{{$order->get_cat_fee($order->id)}} S.R</td>
                                        <td>{{$order->item_total}} S.R</td>
                                        <td>{{$order->order_total}} S.R</td>
                                        {{--<td>--}}
                                        {{--<a title="View" href="/providerprovider/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>--}}
                                        {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$order->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                        {{--</td>--}}
                                    </tr>

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
@endsection
