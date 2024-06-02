@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">Home</a></li>
        <li>{{ __('language.Application Settings') }}</li>
        <li class="active">About Us</li>
    </ul>
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12  col-xs-12">
            @include('admin.layouts.message')
                <div class="panel panel-default">
                    <div class="panel-body row" style="padding-top:25px; padding-bottom:50px;">

                      <div class="col-md-6">
                        <div class="panel-default">
                          <div class="panel-heading">{{ __('language.English Text') }}</div>
                          <div class="panel-body">{!! $about->en_text !!}</div>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="panel-default">
                          <div class="panel-heading">{{ __('language.Arabic Text') }}</div>
                          <div class="panel-body">{!! $about->ar_text !!}</div>
                        </div>
                      </div>


                </div>
            </div>
            @if(admin()->hasPermissionTo('Edit settings'))
              <td>
                <a href="/admin/settings/about/edit" title="Edit" class="buttons">
                  <button class="btn btn-warning btn-condensed col-md-2 pull-right">
                    <i class="fa fa-edit"></i>
                    {{ __('language.Edit') }}
                  </button></a>
                </td>
              @endif
        </div>
    </div>
    </div>

@endsection
