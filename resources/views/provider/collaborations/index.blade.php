@php
    $sorter     = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
    $direction  = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
    $dirIcon    = $direction == 'asc' ? 'desc' : 'asc';
@endphp


@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{ __('language.Collaborations') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    {{--<form class="form-horizontal" method="get" action="/provider/collaborations/search">--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-xs-12">--}}
                                {{--<div class="input-group" style="margin-top: 10px;">--}}
                                    {{--<input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by name or email" style="margin-top: 1px;"/>--}}
                                    {{--<span class="input-group-addon btn btn-default">--}}
                                            {{--<button class="btn btn-default">Search now</button>--}}
                                    {{--</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                    <div class="panel-body">
                        <div class="">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.ID') }}</a></td>
                                    <td><a href="?sort=en_name.{{$sorter == 'en_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.English Name') }}</a></td>
                                    <td><a href="?sort=ar_name.{{$sorter == 'ar_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'ar_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Arabic Name') }}</a></td>
                                    <td><a href="?sort=orders_count.{{$sorter == 'orders_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'orders_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Orders') }}</a></td>
                                    <td><a href="?sort=services_sales.{{$sorter == 'services_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'services_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Services Sales') }}</a></td>
                                    <td><a href="?sort=items_sales.{{$sorter == 'items_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'items_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Items Sales') }}</a></td>
                                    <td><a href="?sort=total_sales.{{$sorter == 'total_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'total_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Total Sales') }}</a></td>
                                    <td><a href="?sort=rate_count.{{$sorter == 'rate_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Count') }}</a></td>
                                    <td><a href="?sort=rate_average.{{$sorter == 'rate_average' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_average' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Avg') }}</a></td>
                                    @if(provider()->hasPermissionTo('Statistics collaboration') || provider()->hasPermissionTo('Bills collaboration')
                                    || provider()->hasPermissionTo('Service fee collaboration')  || provider()->hasPermissionTo('Third fee collaboration') )
                                        <th>{{ __('language.Operations') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($companies as $company)
                                    <tr>
                                        <td>{{$company->id}}</td>
                                        <td>{{$company->en_name}}</td>
                                        <td>{{$company->ar_name}}</td>
                                        <td>{{ $company->orders_count }}</td>
                                        <td>@readable_int($company->services_sales)</td>
                                        <td>@readable_int($company->items_sales)</td>
                                        <td>@readable_int($company->total_sales)</td>
                                        <td>{{ $company->rate_count }}</td>
                                        <td>@include('layouts.components.rateStars', ['rate' => $company->rate_average])</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn" type="button" data-toggle="dropdown" style="width:21px">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                @if(provider()->hasPermissionTo('Statistics collaboration'))
                                                        <li><a href="/provider/collaboration/{{$company->id}}/statistics" style="text-decoration:none"><i class="fa fa-area-chart"></i> {{ __('language.view statistics') }}</a></li>
                                                    @endif
                                                    @if(provider()->hasPermissionTo('Service fee collaboration'))
                                                        <li><a href="/provider/collaboration/{{$company->id}}/services/fees/view" style="text-decoration:none"><i class="fa fa-money"></i> View Service fees</a></li>
                                                    @endif
                                                    @if(provider()->hasPermissionTo('Third fee collaboration'))
                                                        <li><a href="/provider/collaboration/{{$company->id}}/third/fees/view" style="text-decoration:none"><i class="fa fa-usd"></i> View Third fees</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>

                                            {{--<a title="Print Bills" href="/provider/collaboration/{{$company->id}}/print/bills"><button class="btn btn-success btn-condensed"><i class="fa fa-money"></i></button></a>--}}
                                            {{--<a title="Subscriptions" href="/provider/company/{{$company->id}}/subscriptions"><button class="btn btn-warning btn-condensed"><i class="fa fa-check-square"></i></button></a>--}}
                                            {{--@if($company->active == 1)--}}
                                                {{--<button class="btn btn-primary btn-condensed mb-control" data-box="#message-box-suspend-{{$company->id}}" title="Suspend"><i class="fa fa-thumbs-o-down"></i></button>--}}
                                            {{--@else--}}
                                                {{--<button class="btn btn-success btn-condensed mb-control" data-box="#message-box-activate-{{$company->id}}" title="Activate"><i class="fa fa-thumbs-up"></i></button>--}}
                                            {{--@endif--}}
                                            {{--<a title="Edit" href="/provider/company/{{$company->id}}/edit"><button class="btn btn-warning btn-condensed"><i class="fa fa-edit"></i></button></a>--}}
                                            {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$company->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}

                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                            {{$companies->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
