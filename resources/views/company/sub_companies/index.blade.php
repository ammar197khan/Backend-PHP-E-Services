@php
    $sorter     = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
    $direction  = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
    $dirIcon    = $direction == 'asc' ? 'desc' : 'asc';
@endphp

@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Sub Companies') }}</li>
        <li class="active">{{isset($status) ? $status : 'Search'}}</li>
    </ul>



    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    @if(company()->hasPermissionTo('Add sub company'))
                        <div class="panel-heading">
                            <a href="/company/sub_company/create"><button type="button" class="btn btn-info"><i class="fa fa-plus"></i>  {{ __('language.New Sub Company') }}</button></a>
                            <div style="float: right; margin-right: 10px;">
                                <select name="sub_company_status" class="form-control" onchange="location = this.value">
                                    <option @if(request('sub_company_status') == 'active') selected @endif value="/company/sub_companies/active?sub_company_status=active">{{ __('language.Active') }}</option>
                                    <option @if(request('sub_company_status') == 'suspended') selected @endif value="/company/sub_companies/suspended?sub_company_status=suspended">{{ __('language.Suspended') }}</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    <form class="form-horizontal" method="get" @if(Request::is('company/sub_companies/active') || Request::is('company/sub_company/active/search') )
                    action="/company/sub_company/active/search"
                    @else action="/company/sub_company/suspended/search" @endif>
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by name" style="margin-top: 1px;"/>
                                    <span class="input-group-addon btn btn-default">
                                            <button class="btn btn-default">{{ __('language.Search now') }}</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.ID') }}</a></td>
                                    <td><a href="?sort=en_name.{{$sorter == 'en_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.English Name') }}</a></td>
                                    <td><a href="?sort=ar_name.{{$sorter == 'ar_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'ar_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Arabic Name') }}</a></td>
                                    <td><a href="?sort=users.{{$sorter == 'users' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'users' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.View Users') }}</a></td>
                                    <td><a href="?sort=orders_count.{{$sorter == 'orders_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'orders_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.View Orders') }}</a></td>
                                    <td><a href="?sort=services_sales.{{$sorter == 'services_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'services_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Services Sales') }}</a></td>
                                    <td><a href="?sort=items_sales.{{$sorter == 'items_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'items_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Items Sales') }}</a></td>
                                    <td><a href="?sort=total_sales.{{$sorter == 'total_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'total_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Total Sales') }}</a></td>
                                    @if(company()->hasPermissionTo('Edit sub company'))
                                        <th>{{ __('language.Operations') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($subs as $sub)
                                    <tr>
                                        <td>{{$sub->id}}</td>
                                        <td>{{$sub->en_name}}</td>
                                        <td>{{$sub->ar_name}}</td>
                                        <td>
                                            {{$sub->users_count}}
                                        </td>
                                        <td>{{ $sub->orders_count }}</td>
                                        <td>@readable_int($sub->services_sales)</td>
                                        <td>@readable_int($sub->items_sales)</td>
                                        <td>@readable_int($sub->total_sales)</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn" type="button" data-toggle="dropdown" style="width:21px">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    @if($sub->users_count != 0)
                                                        <li><a href="/company/sub_company/{{$sub->id}}/users" style="text-decoration: none"><i class="fa fa-eye"></i> {{ __('language.View Users') }}</a></li>
                                                    @endif
                                                    @if(company()->hasPermissionTo('Edit sub company'))
                                                        <li><a href="/company/sub_company/{{$sub->id}}/edit" style="text-decoration: none"><i class="fa fa-edit"></i> {{ __('language.Edit') }}</a></li>
                                                    @endif
                                                    @if(company()->hasPermissionTo('Suspend sub company'))
                                                        @if($sub->status == 'active')
                                                            <li><a href="#" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-suspend-{{$sub->id}}" style="text-decoration: none"><i class="fa fa-minus-square"></i> {{ __('language.Suspend') }}</a></li>
                                                        @else
                                                            <li><a href="#" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-activate-{{$sub->id}}" style="text-decoration: none"><i class="fa fa-check-square"></i> {{ __('language.Activate') }}</a></li>
                                                        @endif
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$sub->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                    <div class="mb-content">
                                                        <p>{{ __("language.Your are about to suspend a sub company,its individuals won't be able to use the application.") }}</p>
                                                        <br/>
                                                        <p>{{ __('language.Are you sure?') }}</p>
                                                    </div>
                                                    <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/company/sub_company/status/change" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="sub_id" value="{{$sub->id}}">
                                                        <button type="submit" class="btn btn-primary btn-lg pull-right">{{ __('language.Suspend') }}</button>
                                                    </form>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$sub->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p> {{ __('language.Your are about to activate a sub company,its individuals will be able to use the application.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/company/sub_company/status/change" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="sub_id" value="{{$sub->id}}">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">{{ __('language.Activate') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->

                                @endforeach
                                </tbody>
                            </table>
                            {{$subs->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
