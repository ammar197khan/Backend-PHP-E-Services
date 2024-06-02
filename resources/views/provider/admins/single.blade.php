@extends('provider.layouts.app')
@section('content')

    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Admins') }}</li>
        <li class="active">{{isset($admin) ? 'Update an admin' : 'Create a admin'}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    {{--{{dd($errors)}}--}}
    @if(\Session::get('current_locale',config('app.fallback_locale','en')) == 'ar')
    <style>

     input[type=checkbox] {

        float: right!important;
        margin: 14px 0px 0px!important;
      }
    </style>
      @endif
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                @include('admin.layouts.message')
                <form class="form-horizontal" method="post" action="{{isset($admin) ? '/provider/admin/update' : '/provider/admin/store'}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($admin) ? 'Update an admin' : __('language.Create an admin') }}
                            </h3>
                        </div>
                        <div class="panel-body">

                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">Permissions</label>
                                    <div class="col-md-6 col-xs-12" style="padding: 25px">

                                        @if(isset($admin))
                                            @foreach($data as $key => $permission)
                                                <h4 style="color: #1d75b3">{{$key}}</h4>
                                                <div class="row" style="padding-bottom: 20px">
                                                    <div class="col-lg-12">
                                                        @foreach($permission as $single_permission)
                                                            <div class="col-md-4">
                                                                <input type="checkbox" id="check{{$single_permission->id}}" name="check_list[]" value="{{ $single_permission->name }}"
                                                                       @if($admin->hasPermissionTo($single_permission->name)) checked @endif>
                                                                <label class="control-label" style="padding: 4px" for="check{{$single_permission->id}}">{{$single_permission->name}}</label>
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
                                    </div>
                            </div>

                            <div class="form-group {{ $errors->has('badge_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Badge ID') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="badge_id" value="{{isset($admin) ? $admin->badge_id : old('badge_id')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-id-card"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'badge_id'])
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
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="phone" value="{{isset($admin) ? $admin->phone : old('phone')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'phone'])
                                </div>
                            </div>



                            <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Image') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="image" id="cp_photo" data-filename-placement="inside" title="Select Image"/>
                                    </div>
                                    @if(isset($admin))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'image'])
                                </div>
                            </div>

                            {{-- @if(isset($admin)) --}}
                                <div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Username') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="username" value="{{isset($admin) ? $admin->username : old('username')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'username'])
                                    </div>
                                </div>
                            {{-- @endif --}}

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
                                {{isset($admin) ? 'Update' : 'Create'}}
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
