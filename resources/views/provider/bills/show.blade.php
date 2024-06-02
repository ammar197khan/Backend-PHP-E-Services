@extends('provider.layouts.app')
@section('content')
    <style>
        th {
            position: sticky;
            top: 0;
            background: #f9f9f9;
        }
    </style>
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Orders') }}</li>
        <li class="active">View Order</li>
    </ul>
    <!-- END BREADCRUMB -->

    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h2 ><center><strong>{{$provider_name}} To  {{$company_name}}</strong></center></h2>

                            <table class="table table-striped sticky-header">
                                <thead>
                                <tr id="myHeader">
                                    <th class="text-center">{{ __('language.Order Id') }}</th>
                                    <th class="text-center">{{ __('language.Categories') }}</th>
                                    <th class="text-center">{{ __('language.Price categories') }}</th>
                                    <th class="text-center">{{ __('language.Working hours') }}</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-center">{{ __('language.Price') }}  {{ __('language.Items') }}</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-center">{{ __('language.Rate Count') }}</th>
                                    <th class="text-center">{{ __('language.Total') }}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td class="text-center">{{$order->id}}</td>
                                    @if(count($order->details) > 0)
                                        @foreach($order->details as $detail)
                                            <td class="text-center">{{$detail->category->en_name}}</td>
                                            <td class="text-center">{{
                                            isset($detail->cat_fee(provider()->provider_id,$company_id,$detail->type_id)->third_fee) ?
                                            $detail->cat_fee(provider()->provider_id,$company_id,$detail->type_id)->third_fee : 0}}</td>
                                            <td class="text-center">{{
                                            isset($detail->working_hours) ? $detail->working_hours : 0}}</td>
                                        </tr>
                                        <td></td>
                                        @endforeach
                                    @if(count($order->items) > 0)
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-center">{{$item->get_this_item(provider()->provider_id,$item->item_id)->en_name}}</td>
                                            <td class="text-center">{{$item->get_this_item(provider()->provider_id,$item->item_id)->price}}</td>
                                            <td class="text-center">{{$item->taken}}</td>

                                        @endforeach
                                        <td class="text-center">
                                            {{$order->order_total + $order->item_total}}
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">
                                            {{$order->order_total + $order->item_total}}
                                        </td>
                                        </tr>
                                    @endif

                                        @else
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center">
                                                {{$order->order_total + $order->item_total}}
                                            </td>
                                            </tr>
                                        @endif
                                @endforeach

                                <th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-center">{{$total_sum}} S.R</td>
                                </th>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    <!-- END PAGE CONTENT WRAPPER -->
        <script src="https://unpkg.com/floatthead"></script>
        <script>
            $(document).ready(function(){
                $(".sticky-header").floatThead({top:50});
            });
        </script>

@endsection
