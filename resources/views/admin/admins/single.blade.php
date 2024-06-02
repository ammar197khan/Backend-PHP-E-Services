@extends('admin.layouts.app')
@section('content')
@if(\Session::get('current_locale',config('app.fallback_locale','en')) == 'ar')
<style>

 input[type=checkbox] {

    float: right!important;
    margin: 9px 0px 0px 2px!important;
  }
</style>
  @endif
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Admins') }}</li>
        <li class="active">{{isset($admin) ? __('language.Update an admin') : __('language.Create a admin')}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    {{--{{dd($errors)}}--}}
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                @include('admin.layouts.message')
                <form class="form-horizontal" method="post" action="{{isset($admin) ? '/admin/admin/update' : '/admin/admin/store'}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($admin) ? __('language.Update an admin') : __('language.Create an admin') }}
                            </h3>
                        </div>
                        <div class="panel-body">

                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Permissions') }}</label>
                                    <div class="col-md-6 col-xs-12"  style="padding: 25px">
                                        {{--<div class="input-group">--}}
                                            {{--<select class="form-control select" name="role" id="role_select" data-style="btn-success" required>--}}
                                                {{--<option selected disabled>Please Choose a role</option>--}}
                                                {{--@foreach($roles as $role)--}}
                                                    {{--<option value="{{$role->name}}" @if(isset($admin) && $admin->hasRole($role->name)) selected @endif>{{$role->slogan}}</option>--}}
                                                {{--@endforeach--}}
                                            {{--</select>--}}
                                            {{--<span class="input-group-addon"><span class="fa fa-check-circle"></span></span>--}}
                                        {{--</div>--}}

                                        @if(isset($admin))
                                            @foreach($data as $key => $permission)
                                            <h4 style="color: #1d75b3">{{$key}}</h4>
                                            <div class="row" style="padding-bottom: 20px">
                                                <div class="col-lg-12">
                                                    @foreach($permission as $single_permission)
                                                        <div class="col-md-4">
                                                            <input type="checkbox" id="check{{$single_permission->id}}" name="check_list[]" value="{{ $single_permission->name }}"
                                                                   @if($admin->hasPermissionTo($single_permission->name)) checked @endif>
                                                            <label class="control-label" for="check{{$single_permission->id}}">{{$single_permission->name}}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            @foreach($data as $key => $permission)
                                                <h4 style="color: #1d75b3">{{$key}}</h4>
                                                <div class="row" style="padding-bottom: 20px">
                                                    <div class="col-lg-12">
                                                        @foreach($permission as $single_permission)
                                                            <div class="col-md-4">
                                                                <input type="checkbox" id="check{{$single_permission->id}}" name="check_list[]" value="{{ $single_permission->name }}">
                                                                <label class="control-label" style="padding: 4px" for="check{{$single_permission->id}}">{{$single_permission->name}}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                        {{--@if(isset($admin))--}}
                                            {{--<div class="permission">--}}
                                                {{--@foreach($permissions[$admin->role] as $permission)--}}
                                                    {{--<div style="padding-bottom: 5px !important; display: inline-block;">--}}
                                                        {{--<label class="label label-info">{{$permission}}</label>--}}
                                                    {{--</div>--}}
                                                {{--@endforeach--}}
                                            {{--</div>--}}

                                            {{--@foreach($permissions as $key => $permission)--}}
                                                {{--<div class="permission {{$key}}" style="display: none">--}}
                                                    {{--@foreach($permission as $p)--}}
                                                        {{--<div style="padding-bottom: 5px !important; display: inline-block;">--}}
                                                            {{--<label class="label label-info">{{$p}}</label>--}}
                                                        {{--</div>--}}
                                                    {{--@endforeach--}}
                                                {{--</div>--}}
                                            {{--@endforeach--}}
                                        {{--@else--}}
                                            {{--@foreach($permissions as $key => $permission)--}}
                                                {{--<div class="permission {{$key}}" style="display: none">--}}
                                                    {{--@foreach($permission as $p)--}}
                                                        {{--<div style="padding-bottom: 5px !important; display: inline-block;">--}}
                                                            {{--<label class="label label-info">{{$p}}</label>--}}
                                                        {{--</div>--}}
                                                    {{--@endforeach--}}
                                                {{--</div>--}}
                                            {{--@endforeach--}}
                                        {{--@endif--}}

                                        @include('admin.layouts.error', ['input' => 'role'])
                                    </div>
                            </div>

                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" value="{{isset($admin) ? $admin->name : old('name')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'name'])
                                </div>
                            </div>


                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="email" value="{{isset($admin) ? $admin->email : old('email')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-envelope-o"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'email'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.View Phone') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="phone" value="{{isset($admin) ? $admin->phone : old('phone')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'phone'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('vat') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">Vat</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="vat" value="{{isset($admin) ? $admin->vat : old('vat')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-vat"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'vat'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">Vat Registration</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="vat_registration" value="{{isset($admin) ? $admin->vat_registration : old('vat_registration')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'vat_registration'])
                                </div>
                            </div>




                            <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Image') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="image" id="cp_photo" data-filename-placement="inside" title="{{ __('language.Select Image') }}"/>
                                    </div>
                                    @if(isset($admin))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'image'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Password') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password"/>
                                        <span class="input-group-addon"><span class="fa fa-asterisk"></span></span>
                                    </div>
                                    @if(isset($admin))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'password'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Re-Type Password') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password_confirmation"/>
                                        <span class="input-group-addon"><span class="fa fa-asterisk"></span></span>
                                    </div>
                                    @if(isset($admin))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'password_confirmation'])
                                </div>
                            </div>

                            @if(isset($admin))
                                <input type="hidden" name="admin_id" value="{{$admin->id}}">
                            @endif
                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                {{isset($admin) ? __('language.Update') : __('language.Create')}}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>

        $('#role_select').on('change', function (e) {
            var role = e.target.value;
            $('.permission').hide();
            $('.' + role).show()
        });

    </script>
@endsection
