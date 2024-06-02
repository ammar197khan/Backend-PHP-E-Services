@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Admins') }}</li>
        <li class="active">{{ __('language.Search') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="/admin/admin/create"><button type="button" class="btn btn-info"> {{ __('language.+New admin') }}</button></a>
                        {{--<a href="/admin/admin/excel/export"><button type="button" class="btn btn-success pull-right">Download <i class="fa fa-file-excel-o"></i></button></a>--}}
                    </div>
                    <form class="form-horizontal" method="get" action="/admin/admins/search">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="{{ __('language.Search by name,email or phone') }}" style="margin-top: 1px;"/>
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
                                    <th>{{ __('language.Role') }}</th>
                                    <th>{{ __('language.Name') }}</th>
                                    <th>{{ __('language.Email') }}</th>
                                    <th>{{ __('language.View Phone') }}</th>
                                    <th>{{ __('language.Image') }}</th>
                                    <th>{{ __('language.Status') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($admins as $admin)
                                    <tr>
                                        <td><label class="label label-info">{{$admin->role}}</label></td>
                                        <td>{{$admin->name}}</td>
                                        <td>{{$admin->email}}</td>
                                        <td>{{$admin->phone}}</td>
                                        <td>
                                            <img src="/qareeb_admins/{{$admin->image}}" class="image_radius"/>
                                        </td>
                                        <td>
                                            @if($admin->active == 1)
                                                <label class="label label-success">{{ __('language.Active') }}</label>
                                            @else
                                                <label class="label label-primary">{{ __('language.Suspend') }}</label>
                                            @endif
                                        </td>
                                        <td>
                                            <a title="View" href="/admin/admin/{{$admin->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                            {{--<a title="Permissions" href="/admin/{{$role}}/{{$admin->id}}/permissions/edit"><button class="btn btn-success btn-condensed"><i class="fa fa-check-circle"></i></button></a>--}}
                                            <a title="Edit" href="/admin/admin/{{$admin->id}}/edit"><button class="btn btn-warning btn-condensed"><i class="fa fa-edit"></i></button></a>
                                            @if($admin->active == 1)
                                                <button class="btn btn-primary btn-condensed mb-control" data-box="#message-box-suspend-{{$admin->id}}" title="Suspend"><i class="fa fa-minus-square"></i></button>
                                            @else
                                                <button class="btn btn-success btn-condensed mb-control" data-box="#message-box-activate-{{$admin->id}}" title="Activate"><i class="fa fa-check-square"></i></button>
                                            @endif
                                            <button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-danger-{{$admin->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>

                                    <!-- activate with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$admin->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to activate a admin,and will be able to do business in the admin panel.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/admin/admin/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="admin_id" value="{{$admin->id}}">
                                                        <input type="hidden" name="state" value="1">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">{{ __('language.Activate') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end activate with sound -->

                                    <!-- suspend with sound -->
                                    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$admin->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to suspend a admin,and will not be able to do business in the admin panel any more.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                    <form method="post" action="/admin/admin/change_state" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="admin_id" value="{{$admin->id}}">
                                                        <input type="hidden" name="state" value="0">
                                                        <button type="submit" class="btn btn-primary btn-lg pull-right">{{ __('language.Suspend') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end suspend with sound -->

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$admin->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Are you sure?') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __("language.Your are about to delete a admin,and you won't be able to restore its data again.") }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/admin/admin/delete" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="admin_id" value="{{$admin->id}}">
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
                            {{$admins->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
