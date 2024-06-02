
@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/provider/warehouse/all">{{ __('language.Warehouse') }}</a></li>
        <li class="active">{{isset($item) ? 'Update an item' : 'Create an item'}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    {{--{{dd($errors)}}--}}
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="{{isset($item) ? '/provider/warehouse/item/update' : '/provider/warehouse/item/store'}}" novalidate enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($item) ? 'Update an item' : 'Create an item'}}
                            </h3>
                        </div>
                        <div class="panel-body">
                            @if(isset($item))
                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">Full Category Path</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            {{--<label class="form-control">{{$item->category->parent->en_name}} - {{$item->category->en_name}}</label>--}}
                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Main Category') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" name = "main_cat[]" title="Please select a category" id="main_cat" multiple required>
                                            {{-- <option selected disabled>Please select a category</option> --}}
                                            {{-- @foreach($cats as $cat)
                                                <option value="{{$cat->id}}"
                                                    @if(old('main_cat'))
                                                    @foreach(old('main_cat') as $key => $value) {{  $value == $cat->id? 'selected' : ''}}     @endforeach
                                                    @endif
                                                     >{{$cat->en_name}}</option>
                                            @endforeach --}}


                                                    {{-- @php
                                                    $cat_ids = unserialize($item->cat_id);
                                                    if(is_array($cat_ids)){

                                                        $cat_ids =   $cat_ids;
                                                    }else{
                                                        $cat_ids= explode(',', $cat_ids);

                                                    }
                                                    @endphp --}}






                                                        @foreach($cats as $cat)
                                                        <option value="{{$cat->id}}"
                                                        @if(old('main_cat'))
                                                        @foreach(old('main_cat') as $key => $value) {{  $value == $cat->id? 'selected' : ''}}     @endforeach
                                                        @endif
                                                        @if($mainCats)
                                                        @foreach($mainCats as $mainCa ) {{ $mainCa['id'] == $cat->id? 'selected' : ''}}     @endforeach
                                                        @endif
                                                        {{-- @if(!empty($cat_ids))
                                                        @foreach((array)$cat_ids as $cat_id) {{  $cat_id == $cat->id? 'selected' : ''}}     @endforeach
                                                        @endif --}}
                                                         >{{$cat->en_name}}</option>
                                                        @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cubes"></span></span>
                                    </div>
                                    @if(isset($item))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('cat_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Sub Category') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" name="cat_id[]" id="sub_cats" title="Please select a category" multiple required>
                                            {{-- <option selected disabled>Please select a category first</option> --}}

                                            @if(isset($item))

                                                    @php
                                                    $cat_ids = unserialize($item->cat_id);
                                                    if(is_array($cat_ids)){
                                                        $cat_ids =   $cat_ids;
                                                    }else{
                                                        $cat_ids= explode(',', $cat_ids);
                                                    }
                                                    @endphp
                                                     @foreach($cat_ids as $cat_id)
                                                     @php
                                                     $old_en_name = \App\Models\Category::where('id', $cat_id)->first()->en_name;
                                                     @endphp
                                                    <option value ="{{ $cat_id }}"  selected>{{ $old_en_name }} </option>
                                                     @endforeach
                                                @endif

                                                @if(old('cat_id'))
                                                @foreach(old('cat_id') as $key => $value)
                                                    @php
                                                    $old_en_name = \App\Models\Category::where('id', $value)->first()->en_name;
                                                    @endphp
                                                    <option value ="{{ $value }}"  selected>{{ $old_en_name }} </option>
                                                    @endforeach
                                                @endif
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cubes"></span></span>
                                    </div>
                                    @if(isset($item))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('company.layouts.error', ['input' => 'cat_id'])
                                </div>
                            </div>


                            <div class="form-group {{ $errors->has('code') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.code') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="code" value="{{isset($item) ? $item->code : old('code')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-qrcode"></span></span>
                                    </div>
                                    @include('company.layouts.error', ['input' => 'code'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="en_name" value="{{isset($item) ? $item->en_name : old('en_name')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('company.layouts.error', ['input' => 'en_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">Arabic Name</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="ar_name" value="{{isset($item) ? $item->ar_name : old('ar_name')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('company.layouts.error', ['input' => 'ar_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('en_desc') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Description') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <textarea class="form-control" name="en_desc" rows="5">@if(isset($item)){{$item->en_desc}}@else{{old('en_desc')}}@endif</textarea>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('company.layouts.error', ['input' => 'en_desc'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('ar_desc') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Arabic Description') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <textarea class="form-control" name="ar_desc" rows="5">@if(isset($item)){{$item->ar_desc}}@else{{old('ar_desc')}}@endif</textarea>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('company.layouts.error', ['input' => 'ar_desc'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">Image</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="image" id="cp_photo" data-filename-placement="inside" title="Select Image"/>
                                    </div>
                                    @if(isset($item))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('company.layouts.error', ['input' => 'image'])
                                </div>
                            </div>


                            <div class="form-group {{ $errors->has('count') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">Count</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="number" min="1" @if(isset($item)) value="{{$item->count}}" @else value="{{old('count')}}" @endif class="form-control" name="count"/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('company.layouts.error', ['input' => 'count'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('price') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Price') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" min="1" @if(isset($item)) value="{{$item->price}}" @else value="{{old('price')}}" @endif class="form-control" name="price"/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('company.layouts.error', ['input' => 'price'])
                                </div>
                            </div>

                            @if(isset($item))
                                <input type="hidden" name="item_id" value="{{$item->id}}">
                            @endif
                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                {{isset($item) ? 'Update' : __('language.Create') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>

        $('#main_cat').on('change', function (e) {
            var parent_id = e.target.value;
            if (parent_id) {
                $.ajax({
                    url: '/provider/get_sub_cats/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats').empty();
                        // $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                        $.each(data, function (i, sub_cat) {
                            $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');
                        });
                        $('#sub_cats').selectpicker('refresh');
                    }
                });
            }
        });
    </script>
@endsection
