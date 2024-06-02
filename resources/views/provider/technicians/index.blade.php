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
        @if(Request::is('provider/technicians/active'))
            <li class="active">{{ __('language.Active') }}</li>
        @else
            <li class="active">{{ __('language.Suspended') }}</li>
        @endif
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
                    <div class="panel-heading">
                        @if(provider()->hasPermissionTo('Add technician'))
                            <a href="/provider/technician/create">
                              <button type="button" class="btn btn-info">
                                <i class="fa fa-plus"></i>
                                {{ __('language.New Technician') }}
                              </button>
                            </a>
                        @endif
                        @if(provider()->hasPermissionTo('Upload excel technician'))
                            <a href="/provider/technician/excel/view">
                              <button type="button" class="btn btn-info">
                                <i class="fa fa-upload"></i>
                                Import Technicians
                              </button>
                            </a>
                        @endif
                        @if(provider()->hasPermissionTo('Upload image technician'))
                            <a href="/provider/technician/images/view">
                              <button type="button" class="btn btn-info">
                                <i class="fa fa-upload"></i>
                                Import Technicians Images
                              </button>
                            </a>
                        @endif

                        @if(Request::is('provider/technicians/active'))
                            <a href="/provider/technician/active/excel/export" style="float: right;">
                              <button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export Technicians</button>
                            </a>
                        @else
                            <a href="/provider/technician/suspended/excel/export" style="float: right;"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export Technicians</button></a>
                        @endif
                    </div>
{{--                    @if(Request::is('provider/technicians/active'))--}}
{{--                        <a href="/provider/technicians/active" class="btnprn pull-right" style="font-size: 20px; padding-right: 10px; text-decoration:none;"> <i class="fa fa-print"></i> PRINT</a>--}}
{{--                    @else--}}
{{--                        <a href="/provider/technicians/suspended" class="btnprn pull-right" style="font-size: 20px; padding-right: 10px; text-decoration:none;"> <i class="fa fa-print"></i> PRINT</a>--}}
{{--                    @endif--}}
{{--                    <script>--}}
{{--                        $(document).ready(function () {--}}
{{--                            $('.btnprn').printPage();--}}
{{--                        });--}}
{{--                    </script>--}}
                    <form class="form-horizontal" method="get" @if(Request::is('provider/technicians/active'))
                    action="/provider/technicians/active/search" @else action="/provider/technicians/suspended/search" @endif style="padding-top: 70px">
                        <div class="form-row">
                            <div class="col-md-4" style="padding-right: 0">
                                <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by technician badge_id,name,email or phone"/>
                            </div>
                            <div class="col-md-2">
                                <span class="input-group-addon btn btn-default col-md-3">
                                <button class="btn btn-default">{{ __('language.Search now') }}</button>
                                </span>
                            </div>
                            <div class="col-md-offset-4 col-md-2">
                                <select name="tech_status" class="form-control" onchange="location = this.value;">
                                    <option @if(request('tech_status') == 'all') selected @endif value="/provider/technicians/{{$state}}?tech_status=all">All</option>
                                    <option @if(request('tech_status') == '0') selected @endif value="/provider/technicians/{{$state}}?tech_status=0">Available</option>
                                    <option @if(request('tech_status') == '1') selected @endif value="/provider/technicians/{{$state}}?tech_status=1">Busy</option>
                                </select>
                            </div>
                        </div>
                    </form>

                     <div class="panel-body">
                        <div class="pull-right" style="padding:0 10px 10px 0">
                          Count : {{ $techs->total() }}
                        </div>
                        <div class="">
                            <table class="table">
                                <tbody>
                                @foreach($techs as $tech)
                                    <tr>
                                        <td>{{$tech->id}}</td>
                                        <td>{{$tech->badge_id}}</td>
                                        <td>{{$tech->en_name}}</td>
                                        <td>{{ !empty($tech->techRole)?   str_replace(' ', '-',ucWords(str_replace('-', ' ',$tech->techRole)))  : '' }}</td>
                                        <td>{{ $tech->orders_count }}</td>
                                        {{-- <td>{{ $technician->customers_count }}</td> --}}
                                        <td>@readable_int($tech->services_sales)</td>
                                        <td>@readable_int($tech->items_sales)</td>
                                        <td>@readable_int($tech->total_sales)</td>
                                        {{-- <td>{{ $tech->busy == 0 ? 'Not busy' : 'Busy' }}</td> --}}
                                        <td>
                                          @if($tech->busy == 1)
                                            <a href="/provider/orders/tech_status/{{$tech->id}}">Busy</a>
                                          @else
                                              Not busy
                                          @endif
                                        </td>
                                        <td>{{ $tech->online == 1 ? 'Online' : 'Offline' }}</td>
                                        <td>{{ $tech->rate_count }}</td>
                                        <td>@include('layouts.components.rateStars', ['rate' => $tech->rate_average])</td>
                                        <td></td>

                                        {{--<td>--}}
                                            {{--@if($tech->rotation_id != NULL)--}}
                                                {{--<label class="label label-danger label-form">--}}
                                                    {{--{{$tech->rotation->en_name}}<br/>--}}
                                                    {{--{{\Carbon\Carbon::parse($tech->rotation->from)->format('g:i A')}} - {{\Carbon\Carbon::parse($tech->rotation->to)->format('g:i A')}}--}}
                                                {{--</label>--}}
                                            {{--@else--}}
                                                {{--Not Assigned--}}
                                            {{--@endif--}}
                                        {{--</td>--}}

                                        <td>
                                            <img src="/providers/technicians/{{$tech->image}}" class="image_radius"/>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn" type="button" data-toggle="dropdown" style="width:21px">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a href="{{ url('/provider/technician/'.$tech->id.'/view') }}" style="text-decoration: none"><i class="fa fa-eye"></i> {{ __('language.Name') }}</a></li>
                                                    <li><a href="/provider/technician/{{$tech->id}}/orders/request" style="text-decoration: none"><i class="fa fa-truck"></i> {{ __('language.View Orders') }}</a></li>
                                                    @if(provider()->hasPermissionTo('Edit technician'))
                                                        <li><a href="/provider/technician/{{$tech->id}}/edit" style="text-decoration: none"><i class="fa fa-edit"></i> {{ __('language.Edit') }}</a></li>
                                                    @endif

                                                    @if($tech->active == 1)
                                                        @if(provider()->hasPermissionTo('Active technician'))
                                                            <li><a href="" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-suspend-{{$tech->id}}" style="text-decoration: none"><i class="fa fa-minus-square"></i> Suspend</a></li>
                                                        @endif
                                                    @else
                                                        @if(provider()->hasPermissionTo('Suspend technician'))
                                                            <li><a href="" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-activate-{{$tech->id}}" style="text-decoration: none"><i class="fa fa-check-square"></i> Activate</a></li>
                                                        @endif
                                                    @endif
                                                </ul>
                                            </div>
                                            {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$tech->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                        </td>
                                    </tr>
                                    <!-- activate with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$tech->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to activate a Technician,it will now be available for orders and search.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                    <form method="post" action="/provider/technician/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="tech_id" value="{{$tech->id}}">
                                                        <input type="hidden" name="state" value="1">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">Activate</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end activate with sound -->

                                    <!-- suspend with sound -->
                                    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$tech->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>Alert !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to suspend a Technician,and the Technician wont be available for orders nor search.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                    <form method="post" action="/provider/technician/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="tech_id" value="{{$tech->id}}">
                                                        <input type="hidden" name="state" value="0">
                                                        <button type="submit" class="btn btn-primary btn-lg pull-right">{{ __('language.Suspend') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end suspend with sound -->

                                    <!-- danger with sound -->
                                    {{--<div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$tech->id}}">--}}
                                    {{--<div class="mb-container">--}}
                                    {{--<div class="mb-middle warning-msg alert-msg">--}}
                                    {{--<div class="mb-title"><span class="fa fa-times"></span>Alert !</div>--}}
                                    {{--<div class="mb-content">--}}
                                    {{--<p>Your are about to delete a technician,and you won't be able to restore its data again like orders under this technician .</p>--}}
                                    {{--<br/>--}}
                                    {{--<p>Are you sure ?</p>--}}
                                    {{--</div>--}}
                                    {{--<div class="mb-footer buttons">--}}
                                    {{--<button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>--}}
                                    {{--<form method="post" action="/provider/technician/delete" class="buttons">--}}
                                    {{--{{csrf_field()}}--}}
                                    {{--<input type="hidden" name="tech_id" value="{{$tech->id}}">--}}
                                    {{--<button type="submit" class="btn btn-danger btn-lg pull-right">Delete</button>--}}
                                    {{--</form>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    <!-- end danger with sound -->

                                @endforeach

                                </tbody>
                                <thead>
                                <tr>
                                    <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.ID') }}</a></td>
                                    <td><a href="?sort=badge_id.{{$sorter == 'badge_id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'badge_id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i>{{ __('language.Badge ID') }}</a></td>
                                    <td><a href="?sort=en_name.{{$sorter == 'en_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.English Name') }}</a></td>
                                    <td><a href="?sort=en_name.{{$sorter == 'role' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Role') }}</a></td>
                                    <td><a href="?sort=orders_count.{{$sorter == 'orders_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'orders_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Orders') }}</a></td>
                                    <td><a href="?sort=services_sales.{{$sorter == 'services_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'services_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Services Sales') }}</a></td>
                                    <td><a href="?sort=items_sales.{{$sorter == 'items_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'items_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Items Sales') }}</a></td>
                                    <td><a href="?sort=total_sales.{{$sorter == 'total_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'total_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Total Sales') }}</a></td>
                                    <td><a href="?sort=busy.{{$sorter == 'busy' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'busy' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Status') }}</a></td>
                                    <td><a href="?sort=online.{{$sorter == 'online' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'online' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Availability') }}</a></td>
                                    <td><a href="?sort=rate_count.{{$sorter == 'rate_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Count') }}</a></td>
                                    <td><a href="?sort=rate_average.{{$sorter == 'rate_average' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_average' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Avg') }}</a></td>
                                    <td></td>
                                    <td>{{ __('language.Image') }}</td>
                                    <td>{{ __('language.Operations') }}</td>
                                </tr>
                                </thead>
                            </table>
                            {{$techs->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready( function() {
            $('#table').dataTable({
                "iDisplayLength": 50
            });
        });
    </script>
@endsection
