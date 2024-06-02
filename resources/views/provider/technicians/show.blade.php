@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    @php
        if($technician->active == 1)
        {
            $state = 'active';
            $name = 'Active';
        }
        elseif($technician->active == 0)
        {
            $state = 'suspended';
            $name = 'Suspended';
        }
    @endphp

    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li> <a href="/admin/technicians/{{$state}}">{{$name}} Technicians</a></li>
        <li class="active">{{$technician->en_name}}</li>
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
                            <h3><span class="fa fa-wrench"></span> {{$technician->en_name}} </h3>
                            <p>
                                @if($technician->active == 1)
                                    <span class="label label-success label-form"> {{ __('language.Active Technician') }} </span>
                                @elseif($technician->active == 0)
                                    <span class="label label-primary label-form"> {{ __('language.Suspended Technician') }} </span>
                                @endif
                            </p>
                            <div class="text-center" id="user_image">
                                <img src="/providers/technicians/{{$technician->image}}" class="img-thumbnail" width="300px" height="300px"/>
                            </div>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">#{{ __('language.ID') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$technician->id}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Badge ID') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$technician->badge_id}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$technician->email}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$technician->phone}} </span><br/>
                                </div>
                            </div>

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
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Categories') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    @foreach($technician->get_category_list($technician->cat_ids) as $cat)
                                        <span class="form-control" style="margin-top: 2px;">{{$cat}}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Company Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                        @if($technician->company_id != 0)
                                        <span class="form-control"> {{$technician->company->en_name}} </span>
                                        @else
                                            -
                                        @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Sub Company Name') }} </label>
                                <div class="col-md-9 col-xs-7">
                                    @if($technician->sub_company_id)
                                        @foreach($technician->get_sub_company_list($technician->sub_company_id) as $sub)
                                            <p class="form-control">{{$sub}}</p>
                                            @endforeach
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$technician->en_name}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$technician->ar_name}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Appearance average') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{(integer)$technician->all_rates['appearance']}} / 5</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Cleanliness average') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{(integer)$technician->all_rates['cleanliness']}} / 5</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Performance average') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{(integer)$technician->all_rates['performance']}} / 5</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Commitment average') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{(integer)$technician->all_rates['commitment']}} / 5</span>
                                </div>
                            </div>


                            {{--<div class="form-group">--}}
                                {{--<label class="col-md-3 col-xs-5 control-label">Location</label>--}}
                                {{--<div class="col-md-9 col-xs-7">--}}
                                    {{--<span class="form-control"> {{$technician->address->parent->en_name}} - {{$technician->address->en_name}} </span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="col-md-3 col-xs-5 control-label">English Description</label>--}}
                                {{--<div class="col-md-9 col-xs-7">--}}
                                    {{--<textarea class="form-control" rows="5">{{$technician->en_desc}}</textarea>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="col-md-3 col-xs-5 control-label">Arabic Description</label>--}}
                                {{--<div class="col-md-9 col-xs-7">--}}
                                    {{--<textarea class="form-control" rows="5">{{$technician->ar_desc}}</textarea>--}}
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
                            <div class="col-md-8 col-xs-7 line-height-30">{{$technician->created_at}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Last Update') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">
                                @if($technician->created_at == $technician->updated_at)
                                    <span class="label label-default">{{ __('language.No updates yet') }}</span>
                                @else
                                    {{$technician->updated_at}}
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Orders') }}</label>
                            <form class="form-horizontal" method="post" action="/provider/technician/orders/invoice/show">
                                {{csrf_field()}}
                                <div class="col-md-8 col-xs-7 line-height-30">
                                    <button class="btn btn-primary">{{$technician->orders->count()}}</button></div>
                                <input type="hidden" name="tech_id" value="{{$technician->id}}">
                            </form>
                        </div>
                        {{--<div class="form-group">--}}
                            {{--<label class="col-md-4 col-xs-5 control-label">Rate</label>--}}
                            {{--<div class="col-md-8 col-xs-7 line-height-30">--}}
                                {{--@if(isset($technician->rate))--}}
                                    {{--@for($i = 0; $i >= $technician->rates->count(); $i++)--}}
                                        {{--<i class="fa fa-star" style="color: gold;"></i>--}}
                                    {{--@endfor--}}
                                {{--@else--}}
                                    {{--<span class="label label-default">No Rating Yet</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3><span class="fa fa-cog"></span> {{ __('language.Settings') }}</h3>
                    </div>
                    <div class="panel-body form-horizontal form-group-separated">
                        <div class="form-group">
                            <label class="col-md-6 col-xs-6 control-label">{{ __('language.Edit') }}</label>
                            <div class="col-md-6 col-xs-6">
                                <a href="/provider/technician/{{$technician->id}}/edit"><button class="btn btn-warning">{{ __('language.Edit') }}</button></a>
                            </div>
                        </div>
                        @if($technician->active == 1)
                            <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label"> {{ __('language.Suspend') }}</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-primary mb-control" data-box="#message-box-suspend-{{$technician->id}}" title="suspend">Suspend</button>
                                </div>
                            </div>
                        @elseif($technician->active == 0)
                            <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label">{{ __('language.Remove Suspension') }}</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-success mb-control" data-box="#message-box-activate-{{$technician->id}}" title="Activate">Activate</button>                                </div>
                            </div>
                        @endif
                            {{--<div class="form-group">--}}
                                {{--<label class="col-md-6 col-xs-6 control-label">Delete Technician</label>--}}
                                {{--<div class="col-md-6 col-xs-6">--}}
                                    {{--<button class="btn btn-danger mb-control" title="Click to delete !" data-box="#message-box-warning-{{$technician->id}}">Delete</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                    </div>
                </div>
            </div>

        </div>


    </div>
    <!-- activate with sound -->
    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$technician->id}}">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                <div class="mb-content">
                    <p>{{ __('language.Your are about to activate a Technician,it will now be available for orders and search.') }}</p>
                    <br/>
                    <p>{{ __('language.Are you sure?') }}</p>
                </div>
                <div class="mb-footer buttons">
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                    <form method="post" action="/provider/technician/change_state" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="tech_id" value="{{$technician->id}}">
                        <input type="hidden" name="state" value="1">
                        <button type="submit" class="btn btn-success btn-lg pull-right">Activate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end activate with sound -->

    <!-- suspend with sound -->
    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$technician->id}}">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                <div class="mb-content">
                    <p>{{ __('language.Your are about to suspend a Technician,and the Technician wont be available for orders nor search.') }}</p>
                    <br/>
                    <p>{{ __('language.Are you sure?') }}</p>
                </div>
                <div class="mb-footer buttons">
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                    <form method="post" action="/provider/technician/change_state" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="tech_id" value="{{$technician->id}}">
                        <input type="hidden" name="state" value="0">
                        <button type="submit" class="btn btn-primary btn-lg pull-right">Suspend</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end suspend with sound -->

    <!-- danger with sound -->
    {{--<div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$technician->id}}">--}}
        {{--<div class="mb-container">--}}
            {{--<div class="mb-middle warning-msg alert-msg">--}}
                {{--<div class="mb-title"><span class="fa fa-times"></span>Alert !</div>--}}
                {{--<div class="mb-content">--}}
                    {{--<p>Your are about to delete a Technician,and you won't be able to restore its data again like technicians,companies and orders under this Technician .</p>--}}
                    {{--<br/>--}}
                    {{--<p>Are you sure ?</p>--}}
                {{--</div>--}}
                {{--<div class="mb-footer buttons">--}}
                    {{--<button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>--}}
                    {{--<form method="post" action="/provider/technician/delete" class="buttons">--}}
                        {{--{{csrf_field()}}--}}
                        {{--<input type="hidden" name="tech_id" value="{{$technician->id}}">--}}
                        {{--<button type="submit" class="btn btn-danger btn-lg pull-right">Delete</button>--}}
                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    <!-- end danger with sound -->


    <!-- END PAGE CONTENT WRAPPER -->
@endsection
