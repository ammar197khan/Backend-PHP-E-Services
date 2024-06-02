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
        <li>{{ __('language.Providers') }}</li>
        {{-- @if(Request::is('admin/providers/active'))
            <li class="active">Active</li>
        @else
            <li class="active">Suspended</li>
        @endif --}}
    </ul>
    <!-- END BREADCRUMB -->


    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">

                    <div class="panel-heading">
                      @if(admin()->hasPermissionTo('Add provider'))
                        <a href="/admin/provider/create" class="">
                          <button type="button" class="btn btn-info">
                            <i class="fa fa-plus"></i>
                            {{ __('language.New Provider') }}
                          </button>
                        </a>
                        @endif
                    </div>

                    <div class="panel-body">
                      <form class="form-horizontal" method="get" action="/admin/providers">
                        <div class="form-group">
                          <div class="col-md-6 col-xs-12">
                            <div class="input-group" style="margin-top: 10px;">
                              <input type="text" class="form-control" name="search" value="{{ request()->search ?? ''}}" placeholder="{{ __('language.Search by name or email') }}" style="margin-top: 1px;"/>
                              <span class="input-group-addon btn btn-default">
                                <button class="btn btn-default">{{ __('language.Search now') }}</button>
                              </span>
                            </div>
                          </div>
                            <div class="col-md-offset-4 col-md-2" style="margin-top: 10px;">
                                <select name="provider_status" class="form-control" onclick="location = this.value;">
                                    <option value="/admin/providers">{{ __('language.All') }}</option>
                                    <option @if(request('active') == 1) selected @endif value="/admin/providers?active=1">{{ __('language.Active') }}</option>
                                    <option @if(request('active') == 0) selected @endif value="/admin/providers?active=0">{{ __('language.Suspended') }}</option>
                                </select>
                            </div>

                        </div>
                      </form>
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.ID') }}</a></td>
                                    <td><a href="?sort=en_name.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.English Name') }}</a></td>
                                    <td><a href="?sort=ar_name.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'ar_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Arabic Name') }} </a></td>
                                    <td><a href="?sort=collaborations_count.{{$sorter == 'collaborations_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'collaborations_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Partenerships') }}</a></td>
                                    <td><a href="?sort=technicians_count.{{$sorter == 'technicians_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'technicians_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Technicians') }} </a></td>
                                    <td><a href="?sort=orders_count.{{$sorter == 'orders_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'orders_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Orders') }}</a></td>
                                    <td><a href="?sort=customers_count.{{$sorter == 'customers_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'customers_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i>  {{ __('language.Customers') }}</a></td>
                                    <td><a href="?sort=services_sales.{{$sorter == 'services_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'services_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Services Sales') }}</a></td>
                                    <td><a href="?sort=items_sales.{{$sorter == 'items_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'items_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Items Sales') }}</a></td>
                                    <td><a href="?sort=total_sales.{{$sorter == 'total_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'total_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Total Sales') }}</a></td>
                                    <td><a href="?sort=rate_count.{{$sorter == 'rate_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Count') }}</a></td>
                                    <td><a href="?sort=rate_average.{{$sorter == 'rate_average' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_average' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Avg') }}</a></td>
                                    {{-- <th>English Name</th>
                                    <th>Arabic Name</th>
                                    <th>Email</th>
                                    <th>Phones</th>
                                    <th>Partenerships</th>
                                    <th>Technicians</th>
                                    <th>Orders</th>
                                    <th>Customers</th>
                                    <th>Services Sales</th>
                                    <th>Items Sales</th>
                                    <th>Total Sales</th>
                                    <th>Rate Count</th>
                                    <th>Rate Avg</th> --}}
                                    {{-- <th>Logo</th> --}}
                                    @if(admin()->hasPermissionTo('Edit provider') || admin()->hasPermissionTo('Delete provider'))
                                        <th>{{ __('language.Operations') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($providers as $provider)
                                    <tr>
                                        <td>{{$provider->id}}</td>
                                        <td>{{$provider->en_name}}</td>
                                        <td>{{$provider->ar_name}}</td>
                                        {{-- <td>{{$provider->email}}</td> --}}
                                        {{-- <td>
                                          @foreach(unserialize($provider->phones) as $phone)
                                            <span>{{$phone}}</span><br/>
                                          @endforeach
                                        </td> --}}
                                        <td>{{ $provider->collaborations_count }}</td>
                                        <td>{{ $provider->technicians_count }}</td>
                                        <td>{{ $provider->orders_count }}</td>
                                        <td>{{ $provider->customers_count }}</td>
                                        <td>@readable_int($provider->services_sales)</td>
                                        <td>@readable_int($provider->items_sales)</td>
                                        <td>@readable_int($provider->total_sales)</td>
                                        <td>{{ $provider->rate_count }}</td>
                                        <td>@include('layouts.components.rateStars', ['rate' => $provider->rate_average])</td>
                                        {{-- <td><img src="/providers/logos/{{$provider->logo}}" class="image_radius"/></td> --}}
                                        <td>
                                            <div class="dropdown">
                                              <button class="btn" type="button" data-toggle="dropdown" style="width:21px">
                                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                              </button>
                                              <ul class="dropdown-menu dropdown-menu-right">
                                                @if(admin()->hasPermissionTo('View provider'))
                                                  <li><a href="/admin/provider/{{$provider->id}}/view" style="text-decoration:none"><i class="fa fa-eye"></i> {{ __('language.View Details') }}</a></li>
                                                @endif
                                                @if(admin()->hasPermissionTo('Statistics provider'))
                                                  <li><a href="/admin/provider/{{$provider->id}}/statistics" style="text-decoration:none"><i class="fa fa-area-chart"></i> {{ __('language.Statistics') }}</a></li>
                                                @endif
                                                @if(admin()->hasPermissionTo('Subscription provider'))
                                                  <li><a href="/admin/provider/{{$provider->id}}/subscriptions" style="text-decoration:none"><i class="fa fa-check-square"></i> {{ __('language.Subscriptions') }}</a></li>
                                                @endif
                                                @if(admin()->hasPermissionTo('Edit provider'))
                                                  <li><a href="/admin/provider/{{$provider->id}}/edit" style="text-decoration:none"><i class="fa fa-edit"></i> {{ __('language.Edit') }}</a></li>
                                                @endif
                                                @if(admin()->hasPermissionTo('Suspend provider'))
                                                    @if($provider->active == 1)
                                                      <li><a href="" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-suspend-{{$provider->id}}" style="text-decoration:none"><i class="fa fa-minus-square"></i> {{ __('language.Suspend') }}</a></li>
                                                    @elseif($provider->active == 0)
                                                      <li><a href="" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-activate-{{$provider->id}}" style="text-decoration:none"><i class="fa fa-check-square"></i> {{ __('language.Activate') }}<a></li>
                                                    @endif
                                                @endif
                                              </ul>
                                            </div>
                                        </td>
                                        {{-- <td>
                                            @if(admin()->hasPermissionTo('View provider'))
                                              <a title="View Provider" href="/admin/provider/{{$provider->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                            @endif
                                            @if(admin()->hasPermissionTo('Statistics provider'))
                                          @endif
                                                <a title="Statistics" href="/admin/provider/{{$provider->id}}/statistics"><button class="btn btn-info btn-condensed"><i class="fa fa-area-chart"></i></button></a>
                                            @endif
                                            @if(admin()->hasPermissionTo('Subscription provider'))
                                                <a title="Subscriptions" href="/admin/provider/{{$provider->id}}/subscriptions"><button class="btn btn-warning btn-condensed"><i class="fa fa-check-square"></i></button></a>
                                            @endif
                                            @if($provider->active == 1)
                                                @if(admin()->hasPermissionTo('Suspend provider'))
                                                    <button class="btn btn-default btn-condensed mb-control" data-box="#message-box-suspend-{{$provider->id}}" title="Suspend"><i class="fa fa-minus-square"></i></button>
                                                @endif
                                            @else
                                                @if(admin()->hasPermissionTo('Active provider'))
                                                    <button class="btn btn-success btn-condensed mb-control" data-box="#message-box-activate-{{$provider->id}}" title="Activate"><i class="fa fa-check-square"></i></button>
                                                @endif
                                            @endif
                                            @if(admin()->hasPermissionTo('Edit provider'))
                                                <a title="Edit" href="/admin/provider/{{$provider->id}}/edit"><button class="btn btn-warning btn-condensed"><i class="fa fa-edit"></i></button></a>
                                            @endif --}}

                                             {{-- @if(admin()->hasPermissionTo('View bills provider'))
                                                 <a title="View Bills" href="/admin/provider/{{$provider->id}}/bills"><button class="btn btn-primary btn-condensed"><i class="fa fa-bitcoin"></i></button></a>
                                             @endif
                                            <button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$provider->id}}" title="Delete"><i class="fa fa-trash-o"></i></button> --}}

                                        {{-- </td> --}}
                                    </tr>
                                    <!-- activate with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$provider->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to activate a provider,it will now be available for orders and search.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/admin/provider/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="provider_id" value="{{$provider->id}}">
                                                        <input type="hidden" name="state" value="1">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">{{ __('language.Activate') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end activate with sound -->

                                    <!-- suspend with sound -->
                                    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$provider->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to suspend a provider,and the provider wont be available for orders nor search.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/admin/provider/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="provider_id" value="{{$provider->id}}">
                                                        <input type="hidden" name="state" value="0">
                                                        <button type="submit" class="btn btn-primary btn-lg pull-right">{{ __('language.Suspend') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end suspend with sound -->

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$provider->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p> {{ __("language.Your are about to delete a provider,and you won't be able to restore its data again like technicians,companies and orders under this provider.") }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/admin/provider/delete" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="provider_id" value="{{$provider->id}}">
                                                        <button type="submit" class="btn btn-danger btn-lg pull-right">{{ __('language.Delete') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->
                                @endforeach
                                </tbody>
                            </table>
                            {{$providers->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
