@php
  $sorter     = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
  $direction  = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
  $dirIcon    = $direction == 'asc' ? 'desc' : 'asc';
@endphp

@extends('admin.layouts.app')
@section('content')
    <style media="screen">
        .dropdown-menu:before {
            bottom: 100%;
            left: 85%;
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
        }
    </style>
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Technicians') }}</li>
    </ul>
    <!-- END BREADCRUMB -->


    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">

                    {{-- <div class="panel-heading">
                      @if(admin()->hasPermissionTo('Add provider'))
                        <a href="/admin/provider/create" class="">
                          <button type="button" class="btn btn-info">
                            <i class="fa fa-plus"></i>
                            New Provider
                          </button>
                        </a>
                        @endif
                    </div> --}}

                    <div class="panel-body">
                      {{-- <form class="form-horizontal" method="get" action="/admin/providers/search">
                        <div class="form-group">
                          <div class="col-md-6 col-xs-12">
                            <div class="input-group" style="margin-top: 10px;">
                              <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by name or email" style="margin-top: 1px;"/>
                              <span class="input-group-addon btn btn-default">
                                <button class="btn btn-default">Search now</button>
                              </span>
                            </div>
                          </div>
                            <div class="col-md-offset-4 col-md-2" style="margin-top: 10px;">
                                <select name="provider_status" class="form-control" onclick="location = this.value;">
                                    <option value="/admin/providers">All</option>
                                    <option value="/admin/providers?active=1">Active</option>
                                    <option value="/admin/providers?active=0">Suspended</option>
                                </select>
                            </div>

                        </div>
                      </form> --}}
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.ID') }}</a></td>
                                    <td><a href="?sort=en_name.{{$sorter == 'en_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.English Name') }}</a></td>
                                    <td><a href="?sort=provider.{{$sorter == 'provider' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'provider' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Provider') }}</a></td>
                                    <td><a href="?sort=orders_count.{{$sorter == 'orders_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'orders_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Orders') }}</a></td>
                                    <td><a href="?sort=services_sales.{{$sorter == 'services_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'services_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Services Sales') }}</a></td>
                                    <td><a href="?sort=items_sales.{{$sorter == 'items_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'items_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Items Sales') }}</a></td>
                                    <td><a href="?sort=total_sales.{{$sorter == 'total_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'total_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Total Sales') }}</a></td>
                                    <td><a href="?sort=rate_count.{{$sorter == 'rate_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Count') }}</a></td>
                                    <td><a href="?sort=rate_average.{{$sorter == 'rate_average' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_average' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Avg') }}</a></td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($technicians as $technician)
                                    <tr>
                                        <td>{{$technician->id}}</td>
                                        <td>{{$technician->en_name}}</td>
                                        <td>{{$technician->provider}}</td>
                                        <td>{{ $technician->orders_count }}</td>
                                        {{-- <td>{{ $technician->customers_count }}</td> --}}
                                        <td>@readable_int($technician->services_sales)</td>
                                        <td>@readable_int($technician->items_sales)</td>
                                        <td>@readable_int($technician->total_sales)</td>
                                        <td>{{ $technician->rate_count }}</td>
                                        <td>@include('layouts.components.rateStars', ['rate' => $technician->rate_average])</td>
                                        <td>
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$technicians->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
