@php
  $sorter     = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
  $direction  = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
  $dirIcon    =  (isset($direction) &&  $direction == 'asc') ? 'desc' : 'asc';

//   dd($sorter, $direction, $dirIcon);
@endphp

@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Companies') }}</li>
        {{-- @if(Request::is('admin/companies/active'))
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
                    @if(admin()->hasPermissionTo('Add company'))
                        <div class="panel-heading">
                            <a href="/admin/company/create">
                              <button type="button" class="btn btn-info">
                                <i class="fa fa-plus"></i>
                                {{ __('language.New Company') }}
                              </button>
                            </a>
                        </div>
                    @endif
                    <form class="form-horizontal" method="get" action="/admin/companies/search">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="{{ __('language.Search by name or email') }}" style="margin-top: 1px;"/>
                                    <span class="input-group-addon btn btn-default">
                                            <button class="btn btn-default">{{ __('language.Search now') }}</button>
                                    </span>
                                </div>
                            </div>


                            <div class="col-md-offset-4 col-md-2" style="margin-top: 10px;">
                                <select name="company_status" class="form-control" onclick="location = this.value;">
                                    <option value="?active=" {{  !isset($_GET['active'])? 'selected' : '' }} >{{ __('language.All') }}</option>
                                    <option @if(request('active') == 1) selected @endif value="?active=1">{{ __('language.Active') }}</option>
                                    <option @if( isset($_GET['active']) && $_GET['active'] != '' && request('active') == 0) selected @endif value="?active=0">{{ __('language.Suspended') }}</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i>{{ __('language.ID') }} </a></td>
                                    <td><a href="?sort=en_name.{{$sorter == 'en_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.English Name') }}</a></td>
                                    <td><a href="?sort=ar_name.{{$sorter == 'ar_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'ar_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Arabic Name') }}</a></td>
                                    <td><a href="?sort=collaborations_count.{{$sorter == 'collaborations_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'collaborations_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Partenerships') }}</a></td>
                                    <td><a href="?sort=orders_count.{{$sorter == 'orders_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'orders_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i>{{ __('language.Orders') }}</a></td>
                                    <td><a href="?sort=services_sales.{{$sorter == 'services_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'services_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Services Sales') }}</a></td>
                                    <td><a href="?sort=items_sales.{{$sorter == 'items_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'items_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Items Sales') }}</a></td>
                                    <td><a href="?sort=total_sales.{{$sorter == 'total_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'total_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Total Sales') }}</a></td>
                                    <td><a href="?sort=rate_count.{{$sorter == 'rate_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Count') }}</a></td>
                                    <td><a href="?sort=rate_average.{{$sorter == 'rate_average' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_average' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Rate Avg') }}</a></td>
                                    {{-- <th>English Name</th>
                                    <th>Arabic Name</th>
                                    <th>Email</th>
                                    <th>Phones</th>
                                    <th>logo</th> --}}
                                    @if(admin()->hasPermissionTo('Edit company') || admin()->hasPermissionTo('Delete company'))
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
                                        {{-- <td>{{$company->email}}</td> --}}
                                        {{-- <td>
                                          @foreach(unserialize($company->phones) as $phone)
                                            <span>{{$phone}}</span><br/>
                                          @endforeach
                                        </td> --}}
                                        <td>{{ $company->collaborations_count }}</td>
                                        <td>{{ $company->orders_count }}</td>

                                        <td>@readable_int(!empty($company->services_sales)? $company->services_sales : 0)</td>
                                        <td>@readable_int( !empty($company->items_sales)? $company->items_sales : 0)</td>
                                        <td>@readable_int(!empty($company->total_sales)? $company->total_sales : 0)</td>
                                        <td>{{ $company->rate_count }}</td>
                                        <td>@include('layouts.components.rateStars', ['rate' => $company->rate_average])</td>
                                        {{-- <td><img src="/providers/logos/{{$provider->logo}}" class="image_radius"/></td> --}}
                                        <td>
                                            <div class="dropdown">
                                              <button class="btn" type="button" data-toggle="dropdown" style="width:21px">
                                                  <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                              </button>
                                              <ul class="dropdown-menu dropdown-menu-right">
                                                @if(admin()->hasPermissionTo('View company'))
                                                  <li><a href="/admin/company/{{$company->id}}/view" style="text-decoration:none"><i class="fa fa-eye"></i> {{ __('language.View Details') }}</a></li>
                                                @endif
                                                @if(admin()->hasPermissionTo('Statistics company'))
                                                  <li><a href="/admin/company/{{$company->id}}/statistics" style="text-decoration:none"><i class="fa fa-area-chart"></i> {{ __('language.Statistics') }}</a></li>
                                                @endif
                                                @if(admin()->hasPermissionTo('Subscription company'))
                                                  <li><a href="/admin/company/{{$company->id}}/subscriptions" style="text-decoration:none"><i class="fa fa-check-square"></i> {{ __('language.Subscriptions') }}</a></li>
                                                @endif
                                                @if(admin()->hasPermissionTo('Edit company'))
                                                  <li><a href="/admin/company/{{$company->id}}/edit" style="text-decoration:none"><i class="fa fa-edit"></i> {{ __('language.Edit') }}</a></li>
                                                @endif
                                                @if(admin()->hasPermissionTo('Suspend company'))
                                                    @if($company->active == 1)
                                                      <li><a href="" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-suspend-{{$company->id}}" style="text-decoration:none"><i class="fa fa-minus-square"></i> {{ __('language.Suspend') }}</a></li>
                                                    @elseif($company->active == 0)
                                                      <li><a href="" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-activate-{{$company->id}}" style="text-decoration:none"><i class="fa fa-check-square"></i> {{ __('language.Activate') }}</a></li>
                                                    @endif
                                                @endif
                                              </ul>
                                            </div>
                                        </td>
                                        {{-- <td>
                                            <a title="View Provider" href="/admin/company/{{$company->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                            @if(admin()->hasPermissionTo('Statistics company'))
                                                <a title="Statistics" href="/admin/company/{{$company->id}}/statistics"><button class="btn btn-info btn-condensed"><i class="fa fa-area-chart"></i></button></a>
                                            @endif
                                            @if(admin()->hasPermissionTo('Subscription company'))
                                                <a title="Subscriptions" href="/admin/company/{{$company->id}}/subscriptions"><button class="btn btn-warning btn-condensed"><i class="fa fa-check-square"></i></button></a>
                                            @endif
                                            @if($company->active == 1)
                                                @if(admin()->hasPermissionTo('Suspend company'))
                                                    <button class="btn btn-default btn-condensed mb-control" data-box="#message-box-suspend-{{$company->id}}" title="Suspend"><i class="fa fa-minus-square"></i></button>
                                                @endif
                                            @else
                                                @if(admin()->hasPermissionTo('Active company'))
                                                    <button class="btn btn-success btn-condensed mb-control" data-box="#message-box-activate-{{$company->id}}" title="Activate"><i class="fa fa-thumbs-up"></i></button>
                                                @endif
                                            @endif
                                            @if(admin()->hasPermissionTo('Edit company'))
                                                <a title="Edit" href="/admin/company/{{$company->id}}/edit"><button class="btn btn-warning btn-condensed"><i class="fa fa-edit"></i></button></a>
                                            @endif --}}

{{--                                                @if(admin()->hasPermissionTo('Delete company'))--}}
{{--                                                    <button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$company->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
{{--                                                @endif--}}
                                        {{-- </td> --}}
                                    </tr>
                                    <!-- activate with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$company->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to activate a company,it will now be available for orders and search.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }} </p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/admin/company/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="company_id" value="{{$company->id}}">
                                                        <input type="hidden" name="state" value="1">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">{{ __('language.Activate') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end activate with sound -->

                                    <!-- suspend with sound -->
                                    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$company->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p> {{ __('language.Your are about to suspend a company,and the provider wont be available for orders nor search.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }} </p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/admin/company/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="company_id" value="{{$company->id}}">
                                                        <input type="hidden" name="state" value="0">
                                                        <button type="submit" class="btn btn-primary btn-lg pull-right">{{ __('language.Suspend') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end suspend with sound -->

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$company->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p> {{ __("language.Your are about to delete a company,and you won't be able to restore its data again like technicians,companies and orders under this provider.") }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }} </p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/admin/company/delete" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="company_id" value="{{$company->id}}">
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
                            {{$companies->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
