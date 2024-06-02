@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    @php
        if($provider->active == 1)
        {
            $state = 'active';
            $name = 'Active';
        }
        elseif($provider->active == 0)
        {
            $state = 'suspended';
            $name = 'Suspended';
        }
    @endphp

    @if($errors->has('password') || $errors->has('password_confirmation'))
        <script>
            $(window).load(function() {
                $('#modal_change_password').modal('show');
            });
        </script>
    @endif
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li> <a href="/admin/providers">{{ __('language.Providers') }}</a></li>
        <li class="active">{{$provider->en_name}}</li>
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
                            <h3><span class="fa fa-industry"></span> {{$provider->en_name}} </h3>
                            <p>
                                @if($provider->active == 1)
                                    <span class="label label-success label-form"> Active Provider </span>
                                @elseif($provider->active == 0)
                                    <span class="label label-primary label-form"> Suspended Provider </span>
                                @endif
                            </p>
                            <div class="text-center" id="user_image">
                                <img src="/providers/logos/{{$provider->logo}}" class="img-thumbnail" width="300px" height="300px"/>
                            </div>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">#{{ __('language.ID') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->id}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Username') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->admin->username}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->email}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Phones') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    @foreach(unserialize($provider->phones) as $phone)
                                        <span class="form-control"> {{$phone}} </span><br/>
                                    @endforeach
                                </div>
                            </div>
                            @if(!empty($provider->cr_upload))
                            <div class="form-group">
                                <div class="col-md-12 col-xs-7">
                                    <a href="{{ route('admin.get.download', [ "file_name" => $provider->cr_upload]) }}"> Download CR</a>
                                </div>
                            </div>
                            @endif
                            @if(!empty($provider->vat_upload))
                            <div class="form-group">
                                <div class="col-md-12 col-xs-7">
                                    <a href="{{ route('admin.get.download', [ "file_name" => $provider->vat_upload]) }}"> Download VAT</a>
                                </div>
                            </div>
                            @endif
                            @if(!empty($provider->agreement_upload))
                            <div class="form-group">
                                <div class="col-md-12 col-xs-7">
                                    <a href="{{ route('admin.get.download', [ "file_name" => $provider->agreement_upload]) }}"> Download Agreement</a>
                                </div>
                            </div>
                            @endif

                            {{--<div class="form-group">--}}
                                {{--<label class="col-md-3 col-xs-5 control-label">Username</label>--}}
                                {{--<div class="col-md-9 col-xs-7">--}}
                                    {{--<span class="form-control"> {{$provider->username}} </span>--}}
                                {{--</div>--}}
                            {{--</div>--}}

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
                            <h3><span class="fa fa-pencil"></span> {{ __('language.Profile') }}</h3>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->en_name}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->ar_name}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Location') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->address->parent->en_name}} - {{$provider->address->en_name}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <label class="input-group-text" rows="5">{{$provider->en_desc}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <label class="input-group-text" rows="5">{{$provider->ar_desc}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Type') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->type}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Interest Fee') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    @if ($provider->type == 'categorized')
                                      @foreach ($provider->commission_categories as $key => $value)
                                        <span class="form-control"> {{str_replace(':', ' ⮕ ', $key)}} = {{$value}} </span>
                                      @endforeach
                                    @else
                                      <span class="form-control"> {{$provider->interest_fee}} </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Warehouse Fee') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->warehouse_fee}} </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>

                <div class="panel panel-default tabs">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab1" data-toggle="tab">{{ __('language.Send Email') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <form method="post" action="/admin/mail/send">
                            {{csrf_field()}}
                            <div class="tab-pane panel-body active" id="tab1">
                                <div class="form-group">
                                    <label>{{ __('language.E-mail') }}</label>
                                    <input type="email" class="form-control" name="email" value="{{$provider->email}}" required>
                                    @include('admin.layouts.error', ['input' => 'email'])
                                </div>

                                <div class="form-group">
                                    <label>{{ __('language.Subject') }}</label>
                                    <input type="text" class="form-control" name="subject" placeholder="Message subject" required>
                                    @include('admin.layouts.error', ['input' => 'subject'])
                                </div>


                                <div class="form-group">
                                    <label>{{ __('language.Message') }}</label>
                                    <textarea class="form-control" name="text" placeholder="Your message" rows="3" required></textarea>
                                    @include('admin.layouts.error', ['input' => 'text'])
                                </div>

                                <button type="submit" class="btn btn-info btn-lg pull-right">{{ __('language.Send') }}</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <div class="col-md-3">
                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3><span class="fa fa-info-circle"></span> {{ __('language.Quick Info') }}</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Registration') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{$provider->created_at}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label line-height-30">{{ __('language.Technicians') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">
                                <a href="/admin/provider/{{$provider->id}}/technicians">{{$provider->technicians->count()}}</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label line-height-30">{{ __('language.Orders') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{$provider->orders->count()}}</div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3><span class="fa fa-cog"></span> {{ __('language.Settings') }}</h3>
                    </div>
                    <div class="panel-body form-horizontal form-group-separated">

                        @if($provider->active == 1)
                            <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label">{{ __('language.Suspend') }}</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-primary mb-control" data-box="#message-box-suspend-{{$provider->id}}" title="{{ __('language.Suspend') }}">{{ __('language.Suspend') }}</button>
                                </div>
                            </div>
                        @elseif($provider->active == 0)
                            <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label">{{ __('language.Remove Suspension') }}</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-success mb-control" data-box="#message-box-activate-{{$provider->id}}" title="{{ __('language.Activate') }}">{{ __('language.Activate') }}</button>                                </div>
                            </div>
                        @endif
                            {{--<div class="form-group">--}}
                                {{--<label class="col-md-6 col-xs-6 control-label">Delete Provider</label>--}}
                                {{--<div class="col-md-6 col-xs-6">--}}
                                    {{--<button class="btn btn-danger mb-control" title="Click to delete !" data-box="#message-box-warning-{{$provider->id}}">Delete Provider</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                    </div>
                </div>
            </div>

        </div>


    </div>
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
                    <p> {{ __('language.Your are about to suspend a provider,and the provider wont be available for orders nor search.') }}</p>
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
                    <p>{{ __("language.Your are about to delete a provider,and you won't be able to restore its data again like technicians,companies and orders under this provider.") }}</p>
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

    <!-- change password -->
    {{--<div class="modal animated fadeIn" id="modal_change_password" tabindex="-1" role="dialog" aria-labelledby="smallModalHead" aria-hidden="true">--}}
        {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>--}}
                    {{--<h4 class="modal-title" id="smallModalHead">Change password</h4>--}}
                {{--</div>--}}
                {{--<form method="post" action="/admin/provider/change_password">--}}
                    {{--{{csrf_field()}}--}}
                    {{--<div class="modal-body form-horizontal form-group-separated">--}}
                        {{--<div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">--}}
                            {{--<label class="col-md-3 control-label">New Password</label>--}}
                            {{--<div class="col-md-9">--}}
                                {{--<input type="password" class="form-control" name="password" required/>--}}
                                {{--@include('admin.layouts.error', ['input' => 'password'])--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">--}}
                            {{--<label class="col-md-3 control-label">Repeat New</label>--}}
                            {{--<div class="col-md-9">--}}
                                {{--<input type="password" class="form-control" name="password_confirmation" required/>--}}
                                {{--@include('admin.layouts.error', ['input' => 'password_confirmation'])--}}
                            {{--</div>--}}

                        {{--</div>--}}
                        {{--<input type="hidden" value="{{$provider->id}}" name="provider_id">--}}
                    {{--</div>--}}

                {{--<div class="modal-footer">--}}
                    {{--<button type="submit" class="btn btn-warning">Change</button>--}}
                    {{--</form>--}}
                    {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    <!-- end change password -->

    <!-- END PAGE CONTENT WRAPPER -->
@endsection
