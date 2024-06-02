@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>Orders</li>
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
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">

                    {{--<form class="form-horizontal" method="get" action="/provider/{{$type}}/search">--}}
                        {{--@include('provider.orders_search')--}}
                    {{--</form>--}}
                    <div class="panel-body">
                        <div class="table-invoice">
                            <table class="table">
                                <tr>

                                    <th>{{ __('language.Order Id') }}</th>
                                    <th>{{ __('language.Item Description') }}</th>
                                    <th class="text-center">{{ __('language.Item') }}  {{ __('language.Price') }}</th>
                                    <th class="text-center">{{ __('language.Item Count') }}</th>
                                    <th class="text-center">{{ __('language.Image') }}</th>
                                    <th class="text-center">{{ __('language.Status') }}</th>
                                    <th class="text-center">{{ __('language.Operations') }}</th>
                                </tr>
                                @foreach($orders as $order)
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>{{$order->id}}</td>
                                            <td>
                                                <a href="/provider/warehouse/item/{{$item->get_this_item($item->provider_id,$item->item_id)->code}}/edit"><strong>{{$item->get_this_item($item->provider_id,$item->item_id)->en_name}}</strong>
                                                    <p>{{$item->get_this_item($item->provider_id,$item->item_id)->en_desc}}</p>
                                                </a>
                                            </td>
                                            <td class="text-center">{{$item->get_this_item($item->provider_id,$item->item_id)->price}} S.R</td>
                                            <td class="text-center">{{$item->taken}}</td>
                                            <td class="text-center"><a target="_blank" href="/warehouses/{{$item->get_this_item($item->provider_id,$item->item_id)->image}}" title="/warehouses/{{$item->get_this_item($item->provider_id,$item->item_id)->image}}" data-gallery>
                                                    <img src="/warehouses/{{$item->get_this_item($item->provider_id,$item->item_id)->image}}" class="image_radius"/></a></td>
                                            <td class="text-center">@if($item->status == 'confirmed') <span class="label label-success">{{ __('language.Approved') }}</span> @elseif($item->status == 'awaiting') <span class="label label-warning">Awaiting</span> @else <span class="label label-danger">{{ __('language.Declined') }}</span> @endif</td>

                                            <td class="text-center"><a title="View" href="/provider/order/{{$order->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a></td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                {{--<th>--}}
                                {{--<td></td>--}}
                                {{--<td></td>--}}
                                {{--<td></td>--}}
                                {{--<td></td>--}}
                                {{--<td class="text-center">{{$order->item_total}} S.R</td>--}}
                                {{--</th>--}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{$orders->appends($_GET)->links()}}
        </div>
    </div>
@endsection
