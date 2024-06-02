@extends('provider.layouts.app')
@section('content')
<ul class="breadcrumb">
    <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
    <li><a href="/admin/categories">{{ __('language.Categories') }}</a></li>
    <li class="active">Export</li>
</ul>

<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
        @include('admin.layouts.message')
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ __('language.Export categories') }}</h3>
                </div>
                <div class="panel-body">
                      <form class="form-horizontal" action="index.html" method="post" enctype="multipart/form-data">

                          <div class="form-group {{ $errors->has('desc') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{ __('language.Description') }}</label>
                            <div class="col-md-6 col-xs-12">
                              <div class="input-group">
                                <textarea name="desc" class="form-control" rows="4"></textarea>
                                <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                              </div>
                              @include('admin.layouts.error', ['input' => 'desc'])
                            </div>
                          </div>

                          <div class="form-group {{ $errors->has('before_images') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{ __('language.Image Before') }}</label>
                            <div class="col-md-6 col-xs-12">
                              <div class="input-group">
                                <input type="file" name="before_images" class="form-control">
                                <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                              </div>
                              @include('admin.layouts.error', ['input' => 'before_images'])
                            </div>
                          </div>

                          <div class="form-group {{ $errors->has('after_images') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{ __('language.Image After') }}</label>
                            <div class="col-md-6 col-xs-12">
                              <div class="input-group">
                                <input type="file" name="after_images" class="form-control">
                                <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                              </div>
                              @include('admin.layouts.error', ['input' => 'after_images'])
                            </div>
                          </div>

                          <br><br>
                          <div class="form-group">
                            <label class="col-md-3 col-xs-12 control-label"><h4>
                              <b>{{ __('language.Technician Jobs') }}</b></h4></label>
                            <div class="col-md-6 col-xs-12 text-center">
                              <h4><b>{{ __('language.Working hours') }}</b></h4>
                            </div>
                          </div>

                          @for ($i=0; $i < 6; $i++)
                            <div class="form-group {{ $errors->has('after_images') ? ' has-error' : '' }}">
                              <label class="col-md-3 col-xs-12 control-label">{{ __('language.Task') }}</label>
                              <div class="col-md-6 col-xs-12">
                                <div class="input-group">
                                  <input type="number" name="after_images" min="0" class="form-control">
                                  <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                </div>
                                @include('admin.layouts.error', ['input' => 'after_images'])
                              </div>
                            </div>
                          @endfor

                          <br><br>
                          <div class="form-group">
                            <label class="col-md-3 col-xs-12 control-label"><h4>
                              <b>{{ __('language.Items') }}</b></h4></label>
                            <div class="col-md-6 col-xs-12">
                              <small> <a href="#" style="text-decoration:none"><i class="fa fa-external-link" aria-hidden="true"></i> {{ __('language.Items Catalog') }} </a> </small>
                            </div>
                          </div>

                          <div class="form-group {{ $errors->has('items') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{ __('language.Items IDs') }}</label>
                            <div class="col-md-6 col-xs-12">
                              <div class="input-group">
                                <input type="text" name="items" class="form-control">
                                <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                              </div>
                              @include('admin.layouts.error', ['input' => 'items'])
                            </div>
                          </div>

                          <div class="form-group {{ $errors->has('qty') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{ __('language.Quantaties') }}</label>
                            <div class="col-md-6 col-xs-12">
                              <div class="input-group">
                                <input type="text" name="qty" class="form-control">
                                <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                              </div>
                              @include('admin.layouts.error', ['input' => 'qty'])
                            </div>
                          </div>

                          <div style="margin-top:40px">
                            <button type="submit" class="btn btn-primary col-md-2 pull-right">{{ __('language.Submit') }}</button>
                          </div>
                      </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
