@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>Orders</li>
        <li class="active">View Order</li>
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
                                <th class="text-center">{{ __('language.Order') }} {{ __('language.Id') }} </th>
                                <th class="text-center">{{ __('language.Items') }}</th>
                                <th class="text-center">{{ __('language.Price') }} {{ __('language.Items') }}</th>
                                <th class="text-center"> {{ __('language.Count') }}</th>
                                <th class="text-center">{{ __('language.Rate Count') }}</th>
                                <th class="text-center">{{ __('language.Total') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td class="text-center">{{$order->id}}</td>
                                    @if(count($order->items) > 0)
                                        @foreach($order->items as $item)
                                            <td class="text-center">{{$item->get_this_item($order->provider_id,$item->item_id)->en_name}}</td>
                                            <td class="text-center">{{$item->get_this_item($order->provider_id,$item->item_id)->price}}</td>
                                            <td class="text-center">{{$item->taken}}</td>
                                </tr>
                                <td></td>
                            @endforeach
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center">
                                {{$order->item_total }}
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
                                    {{$order->item_total }}
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
