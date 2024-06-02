@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->

    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{ __('language.Profile') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    @if($errors->has('password') || $errors->has('password_confirmation'))
        <script>
            $(window).load(function() {
                $('#modal_change_password').modal('show');
            });
        </script>
    @endif

    <!-- PAGE TITLE -->
    <div class="page-title">
        <h2><span class="fa fa-eye"></span>{{ __('language.View Profile') }}</h2>
    </div>
    <!-- END PAGE TITLE -->
    @include('admin.layouts.message')
    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-5">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-user-secret"></span> {{$admin->name}} </h3>
                            <p>
                                @if($admin->role)
                                  <span class="label label-info label-form"> {{$admin->role}} </span>
                                @endif
                                @if($admin->active == 1)
                                    <span class="label label-success label-form"> {{ __('language.Active') }} </span>
                                @elseif($admin->active == 0)
                                    <span class="label label-primary label-form"> {{ __('language.Suspended') }} </span>
                                @endif
                            </p>
                            <div class="text-center" id="user_image">
                                <img src="/providers/admins/{{$admin->image}}" class="img-thumbnail" width="300px" height="300px"/>
                            </div>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">#{{ __('language.ID') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$admin->badge_id}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12 col-xs-12">
                                    <a href="#" class="btn btn-warning btn-block btn-rounded" data-toggle="modal" data-target="#modal_change_password">{{ __('language.Change password') }}</a>
                                </div>
                            </div>

                        </div>
                    </div>
            </div>
            <div class="col-md-6 col-sm-8 col-xs-7">

                <form class="form-horizontal" method="post" action="/provider/profile/update">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-pencil"></span> {{ __('language.Profile') }}</h3>
                        </div>

                            {{csrf_field()}}
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="name" value="{{$admin->name}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Username') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$admin->username}} </span>
                                </div>
                            </div>



                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="email" value="{{$admin->email}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="phone" value="{{$admin->phone}}">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-3">
                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3><span class="fa fa-info-circle"></span> {{ __('language.Quick Info') }}</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Registration') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{$admin->created_at}}</div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- change password -->
    <div class="modal animated fadeIn" id="modal_change_password" tabindex="-1" role="dialog" aria-labelledby="smallModalHead" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="smallModalHead">{{ __('language.Change password') }}</h4>
                </div>
                <form method="post" action="/provider/change_password">
                    {{csrf_field()}}
                    <div class="modal-body form-horizontal form-group-separated">
                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-3 control-label">{{ __('language.New Password') }}</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" name="password" required/>
                                @include('admin.layouts.error', ['input' => 'password'])
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="col-md-3 control-label">{{ __('language.Repeat New') }}</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" name="password_confirmation" required/>
                                @include('admin.layouts.error', ['input' => 'password_confirmation'])
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Change</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end change password -->

    <!-- END PAGE CONTENT WRAPPER -->
@endsection
