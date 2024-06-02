@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li> <a href="/admin/addresses/all">{{ __('language.Addresses') }}</a></li>
        <li class="active">{{isset($address) ? __('language.Update an address') : __('language.Create an address') }}</li>
    </ul>
    <!-- END BREADCRUMB -->
{{--{{dd($errors)}}--}}
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="{{isset($address) ? '/admin/address/update' : '/admin/address/store'}}">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($address) ?  __('language.Update an address')  :   __('language.Create an address') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            @if(isset($countries))
                                <div class="form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Country') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <select class="form-control selected" name="parent_id">
                                                @forelse($countries as $country)
                                                    <option value="{{$country->id}}" @if(isset($address) && $address->parent_id == $country->id) selected @endif>{{$country->en_name}}</option>
                                                @empty
                                                    <option selected disabled>{{ __('language.Please add a country first in order to add cities.') }}</option>
                                                @endforelse
                                            </select>
                                            <span class="input-group-addon"><span class="fa fa-flag"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'parent_id'])
                                    </div>
                                </div>
                            @endif
                            <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="en_name" @if(isset($address)) value="{{$address->en_name}}" @else value="{{old('en_name')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-flag"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'en_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="ar_name" @if(isset($address)) value="{{$address->ar_name}}" @else value="{{old('ar_name')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-flag"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'ar_name'])
                                </div>
                            </div>

                            @if(isset($address))
                            <input type="hidden" name="address_id" value="{{$address->id}}">
                            @endif
                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                {{isset($address) ? {{ __('language.Update') }} :  __('language.Create') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection
