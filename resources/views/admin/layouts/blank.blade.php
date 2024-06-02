@extends('admin.layouts.app')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        {{-- @yield('breadcrumb') --}}
    </ul>
    <div class="page-content-wrap">
        <div class="row">
              <div class="col-md-12">
                    @include('admin.layouts.message')
                    <div class="panel panel-default">
                          <div class="panel-heading">
                              {{-- @yield('panel-heading') --}}
                          </div>
                          <div class="panel-body">
                              {{-- @yield('panel-body') --}}
                          </div>
                    </div>
              </div>
        </div>
    </div>
@endsection

@section('scripts')
  {{-- @yield('scripts') --}}
@endsection
