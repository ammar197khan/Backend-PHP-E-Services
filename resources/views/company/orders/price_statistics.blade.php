@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Orders') }}</li>
        <li class="active">{{ __('language.View Order') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="text-center">{{ __('language.Order Id') }}</th>
                                <th class="text-center">{{ __('language.Categories') }}</th>
                                <th class="text-center">{{ __('language.Price categories') }}</th>
                                <th class="text-center">{{ __('language.Working hours') }}</th>
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
                                            isset($detail->cat_fee($order->provider_id,company()->company_id,$detail->type_id)->third_fee) ?
                                            $detail->cat_fee($order->provider_id,company()->company_id,$detail->type_id)->third_fee : 0}}</td>
                                            <td class="text-center">{{
                                            isset($detail->working_hours) ? $detail->working_hours : 0}}</td>
                                </tr>
                                <td></td>
                            @endforeach
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center">
                                {{$order->order_total }}
                            </td>
                            {{--@if(count($order->items) > 0)--}}
                            {{--@foreach($order->items as $item)--}}
                            {{--<tr>--}}
                            {{--<td></td>--}}
                            {{--<td></td>--}}
                            {{--<td></td>--}}
                            {{--<td></td>--}}
                            {{--<td class="text-center">{{$item->get_this_item(provider()->provider_id,$item->item_id)->en_name}}</td>--}}
                            {{--<td class="text-center">{{$item->get_this_item(provider()->provider_id,$item->item_id)->price}}</td>--}}
                            {{--<td class="text-center">{{$item->taken}}</td>--}}

                            {{--@endforeach--}}
                            {{--<td class="text-center">--}}
                            {{--{{$order->order_total + $order->item_total}}--}}
                            {{--</td>--}}
                            {{--@else--}}
                            {{--<td></td>--}}
                            {{--<td></td>--}}
                            {{--<td></td>--}}
                            {{--<td></td>--}}
                            {{--<td></td>--}}
                            {{--<td></td>--}}
                            {{--<td class="text-center">--}}
                            {{--{{$order->order_total + $order->item_total}}--}}
                            {{--</td>--}}
                            {{--</tr>--}}
                            {{--@endif--}}

                            @else
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center">
                                    {{$order->order_total }}
                                </td>
                                </tr>
                            @endif
                            @endforeach

                            <th>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center">{{$total_sum}} S.R</td>
                                </tr>
                            </th>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    {{$orders->appends($_GET)->links()}}

@endsection
