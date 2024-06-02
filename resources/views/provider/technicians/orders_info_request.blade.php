@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li> <a href="/provider/technicians/active">{{ __('language.Technicians') }}</a></li>
        <li>{{ __('language.Orders Info Sheet') }}</li>
        <li class=" active">Request</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="/provider/technician/orders/invoice/show">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                              Info Sheet Request
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group {{ $errors->has('tech_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Technicians') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" name="tech_id" data-style="btn-success" required>
                                            @foreach($techs as $tech)
                                                <option value="{{$tech->id}}" @if($tech->id == $tech_id) selected @endif>{{$tech->en_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-wrench"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'tech_id'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('from') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label"> {{ __('language.From') }} : </label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="date" class="form-control" name="from" value="{{old('from')}}" required>
                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'from'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('to') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">To : </label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="date" class="form-control" name="to" value="{{old('to')}}" required>
                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'to'])
                                </div>
                            </div>

                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">Reset</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                               {{ __('language.Show') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection
