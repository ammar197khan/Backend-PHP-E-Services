@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    @php
        if($admin->active == 1)
        {
            $name =  'Active';
        }
        elseif($admin->active == 0)
        {
            $name = 'Suspended';
        }
    @endphp

    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Admins') }}</li>
        <li class="active">{{$admin->name}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- PAGE TITLE -->
    <div class="page-title">
        <h2><span class="fa fa-eye"></span> {{ __('language.View Profile') }}</h2>
        <input type="button" id="button_click" value="Print" onclick="printDiv()" class="btnprn pull-right btn btn-primary" style="font-size: 20px">
        <script type="text/javascript">
            function printDiv() {
                var divContents = document.getElementById("GFG").innerHTML;
                var a = window.open('', '', 'height=500, width=500');
                a.document.write('<html>');
                a.document.write('<link rel="stylesheet" type="text/css" id="theme" href="{{asset("admin/css/theme-default.css")}}"/>');
                a.document.write('\x3Cscript type="text/javascript" src="{{asset("admin/js/plugins/jquery/jquery.min.js")}}">\x3C/script>');
                a.document.write('\x3Cscript type="text/javascript" src="{{asset("admin/js/plugins/jquery/jquery-ui.min.js")}}">\x3C/script>');
                a.document.write('\x3Cscript type="text/javascript" src="{{asset("admin/js/plugins/bootstrap/bootstrap.min.js")}}">\x3C/script>');
                a.document.write('<body >');
                a.document.write(divContents);
                a.document.write('\x3Cscript> $("#settings").remove(); \x3C/script>');
                a.document.write('</body></html>');
                // a.document.close();
                // a.print();
            }
            // $(document).ready(function () {
            //     $('.btnprn').printPage();
            // });
        </script>
    </div>
    <!-- END PAGE TITLE -->
    @include('admin.layouts.message')
    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap" id="GFG">
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-5">

                <form action="#" class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-Admin-secret"></span> {{$admin->name}} </h3>
                            <p>
                                @if($admin->active == 1)
                                    <span class="label label-success label-form"> {{ __('language.Active') }} </span>
                                @elseif($admin->active == 0)
                                    <span class="label label-primary label-form"> {{ __('language.Suspended') }} </span>
                                @endif
                            </p>
                            <div class="text-center" id="Admin_image">
                                <img src="/qareeb_admins/{{$admin->image}}" class="img-thumbnail" width="300px" height="300px"/>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 col-xs-5 control-label">{{ __('language.Registration') }}</label>
                                <div class="col-md-8 col-xs-7 line-height-30">{{$admin->created_at}}</div>
                            </div>
{{--                            <div class="form-group">--}}
{{--                                <label class="col-md-4 col-xs-5 control-label">Last Update</label>--}}
{{--                                <div class="col-md-8 col-xs-7 line-height-30">--}}
{{--                                    @if($admin->created_at == $admin->updated_at)--}}
{{--                                        <span class="label label-default">No updates yet</span>--}}
{{--                                    @else--}}
{{--                                        {{$admin->updated_at}}--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </div>

                    </div>
                </form>

            </div>
            <div class="col-md-6 col-sm-8 col-xs-7">

                <form action="#" class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-pencil"></span> {{ __('language.Profile') }}</h3>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$admin->name}} </span>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$admin->email}} </span>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$admin->phone}} </span>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">#</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$admin->id}} </span>
                                </div>
                            </div>

                            {{--<div class="form-group">--}}
                            {{--<div class="col-md-12 col-xs-12">--}}
                            {{--<a href="#" class="btn btn-warning btn-block btn-rounded" data-toggle="modal" data-target="#modal_change_password">Change password</a>--}}
                            {{--</div>--}}
                            {{--</div>--}}

                        </div>

                    </div>
                </form>

                {{--<div class="panel panel-default tabs">--}}
                    {{--<ul class="nav nav-tabs">--}}
                        {{--<li class="active"><a href="#tab1" data-toggle="tab">Send Email</a></li>--}}
                    {{--</ul>--}}
                    {{--<div class="tab-content">--}}
                        {{--<div class="tab-pane panel-body active" id="tab1">--}}
                            {{--<div class="form-group">--}}
                                {{--<label>E-mail</label>--}}
                                {{--<input type="email" class="form-control" placeholder="youremail@domain.com">--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label>Subject</label>--}}
                                {{--<input type="email" class="form-control" placeholder="Message subject">--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label>Message</label>--}}
                                {{--<textarea class="form-control" placeholder="Your message" rows="3"></textarea>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                    {{--</div>--}}

                {{--</div>--}}

            </div>

            <div class="col-md-3">
{{--                <div class="panel panel-default form-horizontal">--}}
{{--                    <div class="panel-body">--}}
{{--                        <h3><span class="fa fa-info-circle"></span> Quick Info</h3>--}}
{{--                    </div>--}}
{{--                    <div class="panel-body form-group-separated">--}}
{{--                        <div class="form-group">--}}
{{--                            <label class="col-md-4 col-xs-5 control-label">Registration</label>--}}
{{--                            <div class="col-md-8 col-xs-7 line-height-30">{{$admin->created_at}}</div>--}}
{{--                        </div>--}}
{{--                        <div class="form-group">--}}
{{--                            <label class="col-md-4 col-xs-5 control-label">Last Update</label>--}}
{{--                            <div class="col-md-8 col-xs-7 line-height-30">--}}
{{--                                @if($admin->created_at == $admin->updated_at)--}}
{{--                                    <span class="label label-default">No updates yet</span>--}}
{{--                                @else--}}
{{--                                    {{$admin->updated_at}}--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="panel panel-default" id="settings">
                    <div class="panel-body">
                        <h3><span class="fa fa-cog"></span> {{ __('language.Settings') }}</h3>
                    </div>
                    <div class="panel-body form-horizontal form-group-separated">
                        <div class="form-group">
                            <label class="col-md-6 col-xs-6 control-label">{{ __('language.Edit') }}</label>
                            <div class="col-md-6 col-xs-6">
                                <a href="/admin/admin/{{$admin->id}}/edit"><button class="btn btn-warning">{{ __('language.Edit') }}</button></a>
                            </div>
                        </div>
                        @if($admin->active == 1)
                            <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label">{{ __('language.Suspend Admin') }}</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-primary mb-control" data-box="#message-box-suspend-{{$admin->id}}" title="{{ __('language.Suspend') }}">{{ __('language.Suspend') }}</button>
                                </div>
                            </div>
                        @elseif($admin->active == 0)
                            <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label">{{ __('language.Activate Admin') }}</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-success mb-control" data-box="#message-box-activate-{{$admin->id}}" title="{{ __('language.Activate') }}">{{ __('language.Activate') }}</button>
                                </div>
                            </div>
                        @endif
                            <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label">{{ __('language.Delete Admin') }}</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-danger mb-control" title="{{ __('language.Delete') }}" data-box="#message-box-warning-{{$admin->id}}">{{ __('language.Delete') }}</button>
                                </div>
                            </div>
                    </div>
                </div>

        </div>

            <div class="col-md-12 col-sm-8 col-xs-7">
                <form action="#" class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-lock"></span> {{ __('language.Permissions') }}</h3>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <div class="col-md-12 col-xs-7">
                                    <table class="table-hover table-bordered">
                                    @foreach($get_permission_admin as $key=>$value)
                                        <tr>
                                            <th style="font-weight: bold">{{$key}}</th>
                                            @foreach($value as $val)
                                                <td> {{$val}} </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

    </div>
    <!-- activate with sound -->
    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$admin->id}}">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                <div class="mb-content">
                    <p>{{ __('language.Your are about to activate an admin,he will now be able to log in the system and so stuff.') }}</p>
                    <br/>
                    <p>{{ __('language.Are you sure?') }} </p>
                </div>
                <div class="mb-footer buttons">
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                    <form method="post" action="/admin/admin/change_status" class="buttons">
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
                    <p>{{ __('language.Your are about to suspend an admin,he will now prohibited from being able to log in the system and so stuff.') }}</p>
                    <br/>
                    <p>{{ __('language.Are you sure?') }}</p>
                </div>
                <div class="mb-footer buttons">
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                    <form method="post" action="/admin/admin/change_status" class="buttons">
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
                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                <div class="mb-content">
                    <p>{{ __("language.Your are about to delete an admin,and you won't be able to restore its data again.") }}</p>
                    <br/>
                    <p> {{ __('language.Are you sure') }}</p>
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

    <!-- END PAGE CONTENT WRAPPER -->
@endsection
