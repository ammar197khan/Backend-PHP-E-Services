@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/admin/individuals/active">{{ __('language.Individuals') }}</a></li>
        <li class="active">{{isset($technician) ? 'Update ' :  __('language.Create') }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    {{--{{dd($errors)}}--}}
    <div class="page-content-wrap">
        @include('admin.layouts.message')
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="{{isset($technician) ? '/admin/individual/technician/update' : '/admin/individual/technician/store'}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($technician) ? 'Update' :  __('language.Create') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Main Category') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" id="category">
                                            <option selected disabled>Select a category</option>
                                            @foreach($cats as $cat)
                                                <option value="{{$cat->id}}">{{$cat->en_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cubes"></span></span>
                                    </div>
                                    @if(isset($technician))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('cat_ids') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">Sub Category</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control" id="sub_cats" name="cat_ids[]" multiple required>
                                                <option disabled selected>{{ __('language.Select a category first,please') }} !</option>
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                    </div>
                                    @if(isset($technician))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'cat_ids'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="en_name" value="{{isset($technician) ? $technician->en_name : old('en_name')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'en_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="ar_name" value="{{isset($technician) ? $technician->ar_name : old('ar_name')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'ar_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="email" required @if(isset($technician)) value="{{$technician->email}}" @else {{old('email')}} @endif/>
                                        <span class="input-group-addon"><span class="fa fa-envelope-o"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'email'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="phone" @if(isset($technician)) value="{{$technician->phone}}" @else {{old('phone')}} @endif required/>
                                        <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'phone'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Image') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="image" id="cp_photo" data-filename-placement="inside" title="Select Image"/>
                                    </div>
                                    @if(isset($technician))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'image'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Password') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password"/>
                                        <span class="input-group-addon"><span class="fa fa-asterisk"></span></span>
                                    </div>
                                    @if(isset($technician))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'password'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Re-Type Password') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password_confirmation"/>
                                        <span class="input-group-addon"><span class="fa fa-asterisk"></span></span>
                                    </div>
                                    @if(isset($technician))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'password_confirmation'])
                                </div>
                            </div>

                            @if(isset($technician))
                                <input type="hidden" name="tech_id" value="{{$technician->id}}">
                            @endif
                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                {{isset($technician) ? 'Update' : __('language.Create') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        function add_phone()
        {
            var row = '<input type="text" class="form-control phone" placeholder="Phone No." name="phones[]" style="margin-top: 5px;"/>';
            $('#field').append(row);
        }


        $('#category').on('change', function (e) {
            var parent_id = e.target.value;
            if (parent_id) {
                $.ajax({
                    url: '/admin/get_sub_cats/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats').empty();
                        $('#sub_cats').append('<option selected disabled> Select a sub Category </option>');
                        $.each(data, function (i, sub_cat) {
                            $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');
                        });
                    }
                });
            }
        });

    </script>
@endsection
