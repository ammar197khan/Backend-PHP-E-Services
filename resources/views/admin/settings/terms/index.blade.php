@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">Home</a></li>
        <li>{{ __('language.Application Settings') }}</li>
        <li class="active">Terms</li>
    </ul>

    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12  col-xs-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-body row"  style="padding-top:25px; padding-bottom:50px;">

                        <div class="col-md-6 col-xs-6 box">
                          <div class=" panel-default">
                            <div class="panel-heading">{{ __('language.English Text') }}</div>
                            <div class="panel-body">{!! $term->en_text !!}</div>
                          </div>
                        </div>

                        <div class="col-md-6 col-xs-6 box">
                          <div class=" panel-default">
                            <div class="panel-heading">{{ __('language.Arabic Text') }}</div>
                            <div class="panel-body">{!! $term->ar_text !!}</div>
                          </div>
                        </div>


                    </div>
                </div>
                @if(admin()->hasPermissionTo('Edit settings'))
                  <td>
                    <a href="/admin/settings/terms/edit" title="Edit" class="buttons">
                      <button class="btn btn-warning btn-condensed col-md-2 pull-right">
                        <i class="fa fa-edit"></i>
                        {{ __('language.Edit') }}
                      </button>
                    </a>
                  </td>
                @endif
            </div>
        </div>
    </div>
@endsection
