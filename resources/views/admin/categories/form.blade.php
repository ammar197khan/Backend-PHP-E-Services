@php
  $postUrl = isset($category)
  ? route('admin.categories.update', $category->id)
  : route('admin.categories.store');

  if(request()->has('level') && !isset($category)) {
      $level = request()->level;
  } elseif (isset($category)) {
      $level = $category->type;
  } else {
      $level = 1;
  }
@endphp

@extends('admin.layouts.app')
@section('content')
<ul class="breadcrumb">
    <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
    <li> <a href="/admin/categories">{{ __('language.Categories') }}</a></li>
    <li class="active">{{isset($category) ? __('language.Update a category') : __('language.Create a category')}}</li>
</ul>

<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal" method="post" action="{{ $postUrl }}" enctype="multipart/form-data">

                {{csrf_field()}}
                @if (isset($category))
                  <input type="hidden" name="_method" value="PUT">
                @endif
                <input type="hidden" name="type" value="{{ $level }}">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {{isset($category) ? __('language.Update an category') : __('language.Create an category')}}
                        </h3>
                    </div>

                    <div class="panel-body">
                        @if ($level != '1')
                            {{-- ======= START MAIN CATEGORY ======= --}}
                            <div class="form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                              <label class="col-md-3 col-xs-12 control-label">{{ __('language.Main Category') }}</label>
                              <div class="col-md-6 col-xs-12">
                                <div class="input-group">
                                  <select class="form-control select" id="main_cats" name="parent_id">
                                    <option selected disabled>{{ __('language.Select Category') }}</option>
                                    @forelse($categories as $cat)
                                      <option value="{{$cat->id}}">{{$cat->en_name}}</option>
                                    @empty
                                      <option selected disabled>{{ __('language.Please add a category first in order to add sub categories.') }}</option>
                                    @endforelse
                                  </select>
                                  <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                </div>
                                @if(isset($category))
                                  <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                @endif
                              </div>
                            </div>
                            {{-- ======= END MAIN CATEGORY ======= --}}

                        @endif

                        @if ($level == 3)
                          {{-- ======= START SUB CATEGORY ======= --}}
                          <div class="form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{ __('language.Sub Category') }}</label>
                            <div class="col-md-6 col-xs-12">
                              <div class="input-group">
                                <select class="form-control selected" name="parent_id" id="sub_cats">
                                  <option selected disabled>{{ __('language.Please choose a category first.') }} </option>
                                </select>
                                <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                              </div>
                              @if(isset($category))
                                <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                              @endif
                              @include('admin.layouts.error', ['input' => 'parent_id'])
                            </div>
                          </div>
                          {{-- ======= END SUB CATEGORY ======= --}}
                        @endif


                        {{-- ======= START NAME EN ======= --}}
                        <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Name') }}</label>
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="en_name" @if(isset($category)) value="{{$category->en_name}}" @else value="{{old('en_name')}}" @endif required/>
                                    <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                </div>
                                @include('admin.layouts.error', ['input' => 'en_name'])
                            </div>
                        </div>
                        {{-- ======= END NAME EN ======= --}}

                        {{-- ======= START NAME AR ======= --}}
                        <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{ __('language.Arabic Name') }}</label>
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="ar_name" @if(isset($category)) value="{{$category->ar_name}}" @else value="{{old('ar_name')}}" @endif required/>
                                    <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                </div>
                                @include('admin.layouts.error', ['input' => 'ar_name'])
                            </div>
                        </div>
                        {{-- ======= END NAME AR ======= --}}

                        @if ($level == 1)
                          {{-- ======= START ACTIVE ======= --}}
                          <div class="form-group {{ $errors->has('active') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{ __('language.Active') }}</label>
                            <div class="col-md-6 col-xs-12">
                              <div class="input-group">
                                <select class="form-control" name="active">
                                  <option value="1" {{ (isset($category) && $category->active == '1') || old('active') == '1' ? 'selected' : '' }}>{{ __('language.Yes') }}</option>
                                  <option value="0" {{ (isset($category) && $category->active == '0') || old('active') == '0' ? 'selected' : '' }}>{{ __('language.No') }}</option>
                                </select>
                                <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                              </div>
                              @include('admin.layouts.error', ['input' => 'active'])
                            </div>
                          </div>
                          {{-- ======= END ACTIVE ======= --}}
                        @endif

                        @if ($level != '3')
                            {{-- ======= START IMAGE ======= --}}
                            <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                              <label class="col-md-3 col-xs-12 control-label">{{ __('language.Image') }}</label>
                              <div class="col-md-6 col-xs-12">
                                <div class="input-group">
                                  <input type="file" class="fileinput btn-info" name="image" id="cp_photo" data-filename-placement="inside" title="__('language.Select Image')"/>
                                </div>
                                @include('admin.layouts.error', ['input' => 'image'])
                                <br/>
                                @if(isset($category))
                                  <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                  <br/>
                                  <br/>
                                  <div>
                                    <img style="border : solid black 1px; width: 300px; height: 300px;" src="{{asset('categories/'.$category->image)}}" alt="{{$category->en_name}}"/>
                                  </div>
                                @endif
                              </div>
                            </div>
                            {{-- ======= END IMAGE ======= --}}
                        @endif

                    </div>

                    <div class="panel-footer">
                        <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                        <button class="btn btn-primary pull-right">{{ __('language.Save') }}</button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<script>
    $('#main_cats').on('change', function (e) {
        var parent_id = e.target.value;
        if (parent_id) {
            $.ajax({
                url: '/admin/get_sub_cats/'+parent_id,
                type: "GET",

                dataType: "json",

                success: function (data) {
                    $('#sub_cats').empty();
                    $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                    $.each(data, function (i, sub_cat) {
                        $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');
                    });
                }
            });
        }
    });
</script>
@endsection
