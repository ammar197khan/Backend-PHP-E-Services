@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->

    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li> {{ __('language.Users') }}</li>
        <li class="active">{{$user->en_name}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- PAGE TITLE -->
    <div class="page-title">
        <h2><span class="fa fa-eye"></span> {{ __('language.View Profile') }}</h2>
    </div>
    <!-- END PAGE TITLE -->
    @include('admin.layouts.message')
    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-5">

                <form action="#" class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-wrench"></span> {{$user->en_name}} </h3>
                            <p>
                                <span class="label label-success label-form"> {{ __('language.Active User') }} </span>
                            </p>
                            <div class="text-center" id="user_image">
                                <img src="/companies/users/{{$user->image}}" class="img-thumbnail" width="300px" height="300px"/>
                            </div>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">#{{ __('language.ID') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$user->id}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Badge ID') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$user->badge_id}} </span>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$user->email}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$user->phone}} </span><br/>
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

            </div>
            <div class="col-md-6 col-sm-8 col-xs-7">

                <form action="#" class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-pencil"></span> Profile</h3>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$user->en_name}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$user->ar_name}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Sub Company Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$user->sub_company->en_name}} </span>
                                </div>
                            </div>
                            {{--<div class="form-group">--}}
                            {{--<label class="col-md-3 col-xs-5 control-label">Location</label>--}}
                            {{--<div class="col-md-9 col-xs-7">--}}
                            {{--<span class="form-control"> {{$user->address->parent->en_name}} - {{$user->address->en_name}} </span>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                            {{--<label class="col-md-3 col-xs-5 control-label">English Description</label>--}}
                            {{--<div class="col-md-9 col-xs-7">--}}
                            {{--<textarea class="form-control" rows="5">{{$user->en_desc}}</textarea>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                            {{--<label class="col-md-3 col-xs-5 control-label">Arabic Description</label>--}}
                            {{--<div class="col-md-9 col-xs-7">--}}
                            {{--<textarea class="form-control" rows="5">{{$user->ar_desc}}</textarea>--}}
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
                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3><span class="fa fa-info-circle"></span> {{ __('language.Quick Info') }}</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Registration') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{$user->created_at}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Last Update') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">
                                @if($user->created_at == $user->updated_at)
                                    <span class="label label-default">{{ __('language.No updates yet') }}</span>
                                @else
                                    {{$user->updated_at}}
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Orders') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">
                                <button class="btn btn-primary">{{$user->orders->count()}}</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>


    </div>

    <!-- END PAGE CONTENT WRAPPER -->
@endsection
