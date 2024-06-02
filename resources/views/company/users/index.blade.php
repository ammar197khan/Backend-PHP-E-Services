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
        <li><a @if(Request::is('company/users/active')) href="/company/users/active" @else
            href="/company/users/suspended" @endif>
                {{ __('language.Users') }}</a></li>
        @if(strpos($_SERVER['REQUEST_URI'],'company/users/active'))
            <li class="active">{{ __('language.Active') }}</li>
        @elseif(Request::is('company/users/search'))
            <li class="active">{{ __('language.Search') }}</li>
        @else
            <li class="active">{{ __('language.Suspended') }}</li>
        @endif
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">

                    <div class="panel-heading">
                        @if(company()->hasPermissionTo('Add user'))
                            <a href="/company/user/create"><button type="button" class="btn btn-info"><i class="fa fa-plus"></i> {{ __('language.New User') }} </button></a>
                        @endif
                        @if(company()->hasPermissionTo('Upload excel user'))
                            <a href="/company/user/excel/view"><button type="button" class="btn btn-info"><i class="fa fa-upload"></i> {{ __('language.Import Users') }} </button></a>
                        @endif
                        @if(company()->hasPermissionTo('Upload image user'))
                            <a href="/company/user/images/view"><button type="button" class="btn btn-info"><i class="fa fa-plus"></i> {{ __('language.Import Users Images') }} </button></a>
                        @endif

                        @if(Request::is('company/users/active'))
                            <a href="/company/users/active/excel/export" style="float: right;"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> {{ __('language.Export Users') }}</button></a>
                        @elseif(strpos($_SERVER['REQUEST_URI'], 'sub_company') !== false )
                                <a href="/company/users/active/excel/export" style="float: right;"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> {{ __('language.Export Users') }}</button></a>
                        @else
                            @if(count($users) != 0)
                            <a href="/company/users/suspended/excel/export" style="float: right;"><button type="button" class="btn btn-success">  {{ __('language.Export Users') }}<i class="fa fa-file-excel-o"></i></button></a>

                            @endif
                        @endif

                        <div style="float: right; margin-right: 10px">
                            <select name="users_status" class="form-control" onchange="location = this.value" >
                                <option @if(request('users_status') == 'active') selected @endif value="/company/users/active?users_status=active">{{ __('language.Active') }}</option>
                                <option @if(request('users_status') == 'suspended') selected @endif value="/company/users/suspended?users_status=suspended">{{ __('language.Suspended') }}</option>
                            </select>
                        </div>

                    </div>

                    <form class="form-horizontal" method="get" @if(Request::is('company/users/active')) action="/company/users/active/search"
                    @elseif(Request::is('company/users/suspended')) action="/company/users/suspended/search" @endif>
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="{{ __('language.Search by user badge_id,name,email or phone') }}" style="margin-top: 1px;"/>
                                    <span class="input-group-addon btn btn-default">
                                            <button class="btn btn-default">{{ __('language.Search now') }}</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                     <div class="panel-body">
                        <div class="table-responsive">
                          <div class="pull-right" style="padding:0 10px 10px 0">
                            {{ __('language.Count') }} : {{ $users->total() }}
                          </div>
                            <table class="table">
                                <thead>
                                <tr>
                                    <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i>{{ __('language.ID') }}</a></td>
                                    <td><a href="?sort=badge_id.{{$sorter == 'badge_id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'badge_id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i>{{ __('language.Badge ID') }}</a></td>
                                    <td><a href="?sort=en_name.{{$sorter == 'en_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.English Name') }}</a></td>
                                    <td><a href="?sort=orders_count.{{$sorter == 'orders_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'orders_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Orders') }}</a></td>
                                    <td><a href="?sort=services_sales.{{$sorter == 'services_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'services_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Services Sales') }}</a></td>
                                    <td><a href="?sort=items_sales.{{$sorter == 'items_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'items_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Items Sales') }}</a></td>
                                    <td><a href="?sort=total_sales.{{$sorter == 'total_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'total_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> {{ __('language.Total Sales') }}</a></td>
                                    <td></td>
                                    <td>{{ __('language.Email') }}</td>
                                    <td>{{ __('language.Phone') }}</td>
                                    <td>{{ __('language.Image') }}</td>
                                    <td>{{ __('language.Operations') }}</td>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($users as $user)
                                    <tr>
                                        <td>{{$user->id}}</td>
                                        <td>{{$user->badge_id}}</td>
                                        <td>{{$user->en_name}}</td>
                                        <td>{{ $user->orders_count }}</td>
                                        {{-- <td>{{ $usernician->customers_count }}</td> --}}
                                        <td>@readable_int($user->services_sales)</td>
                                        <td>@readable_int($user->items_sales)</td>
                                        <td>@readable_int($user->total_sales)</td>
                                        <td>
                                        </td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->phone}}</td>
                                        <td>
                                            <img src="/companies/users/{{$user->image}}" class="image_radius"/>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn" type="button" data-toggle="dropdown" style="width:21px">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    @if(company()->hasPermissionTo('View user'))
                                                        <li><a href="/company/user/{{$user->id}}/view" style="text-decoration: none"><i class="fa fa-eye"></i> {{ __('language.View User') }}</a></li>
                                                    @endif
                                                    @if(company()->hasPermissionTo('Add user'))
                                                        <li><a href="/company/user/{{$user->id}}/orders/request"><i class="fa fa-truck" style="text-decoration: none"></i> {{ __('language.View Orders') }}</a></li>
                                                        <li><a href="/company/user/{{$user->id}}/order/create" style="text-decoration: none"><i class="fa fa-mail-forward"></i> {{ __('language.Make order') }}</a></li>
                                                    @endif
                                                    @if(company()->hasPermissionTo('Edit user'))
                                                            <li><a href="/company/user/{{$user->id}}/edit" style="text-decoration: none"><i class="fa fa-edit"></i> {{ __('language.Edit User') }}</a></li>
                                                    @endif
                                                    @if($user->active == 1)
                                                        @if(company()->hasPermissionTo('Suspend user'))
                                                                <li><a href="" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-suspend-{{$user->id}}" style="text-decoration: none"><i class="fa fa-minus-square"></i> {{ __('language.Suspend') }}</a></li>
                                                        @endif
                                                    @else
                                                        @if(company()->hasPermissionTo('Active user'))
                                                                <li><a href="" onclick="event.preventDefault()" class="mb-control" data-box="#message-box-activate-{{$user->id}}" style="text-decoration: none"><i class="fa fa-check-square"></i> {{ __('language.Activate') }}</a></li>
                                                        @endif
                                                    @endif

                                                </ul>
                                            </div>
                                            {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$user->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                        </td>
                                    </tr>

                                    <!-- activate with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$user->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to activate a user,it will now be available for orders and app usage.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/company/user/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="user_id" value="{{$user->id}}">
                                                        <input type="hidden" name="state" value="1">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">{{ __('language.Activate') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end activate with sound -->

                                    <!-- suspend with sound -->
                                    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$user->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to suspend a user,and the technician wont be available for orders nor app usage.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/company/user/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="user_id" value="{{$user->id}}">
                                                        <input type="hidden" name="state" value="0">
                                                        <button type="submit" class="btn btn-primary btn-lg pull-right">{{ __('language.Suspend') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end suspend with sound -->
                                @endforeach

                                </tbody>
                            </table>
                            {{$users->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready( function() {
            $('#table').dataTable( {
                "iDisplayLength": 50
            } );
        } )
    </script>
@endsection
