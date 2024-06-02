
@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/provider/technicians/active">{{ __('language.Technicians') }}</a></li>
        <li class="active">{{isset($technician) ? 'Update a technician' : 'Create a technician'}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                @include('admin.layouts.message')
                <form class="form-horizontal" method="post" action="{{isset($technician) ? '/provider/technician/update' : '/provider/technician/store'}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($technician) ? 'Update an technician' : 'Create an technician'}}
                            </h3>
                        </div>

{{--                        @if(isset($technician))--}}
                            <div class="panel-body">
{{--                                <div class="form-group">--}}
{{--                                    <label class="col-md-3 col-xs-12 control-label">Categories</label>--}}
{{--                                    <div class="col-md-6 col-xs-12">--}}
{{--                                        <div class="input-group">--}}
{{--                                            @foreach($technician->get_category_list($technician->cat_ids) as $cat)--}}
{{--                                                <p class="form-control">{{$technician->get_category_parent('en',$cat)}} - {{$cat}}</p>--}}
{{--                                            @endforeach--}}
{{--                                            <span class="input-group-addon"><span class="fa fa-cubes"></span></span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                        @endif--}}
                        @if(isset($technician->sub_company))
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Sub Company') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        @foreach($technician->get_sub_company_list($technician->sub_company_id) as $sub)
                                            <p class="form-control">{{$sub}}</p>
                                        @endforeach
                                        <span class="input-group-addon"><span class="fa fa-cubes"></span></span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="panel-body">
                            <div class="form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Main Category') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" name="main_cat[]" id="category" title="Select a category" multiple>
                                            {{-- <option selected disabled>Select a category</option> --}}
                                            @foreach($cats as $cat)
                                                <option @if(isset($technician))
                                                        {{ in_array($cat->id, $technician->get_parent_cat($technician->cat_ids) ) ? 'selected' : '' }}
                                                        @endif value="{{$cat->id}}"
                                                        @if(old('main_cat'))
                                                        @foreach(old('main_cat') as $key => $value) {{  $value == $cat->id? 'selected' : ''}}     @endforeach
                                                        @endif>{{$cat->en_name}}</option>
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
                                        <select class="form-control select" id="sub_cats" name="cat_ids[]" title="Select a category first,please" multiple @if(isset($technician) == false) required @endif>
                                                {{-- <option disabled selected>{{ __('language.Select a category first,please') }} !</option> --}}
                                                @if(old('cat_ids'))
                                                @foreach(old('cat_ids') as $key => $value)
                                                    @php
                                                    $old_en_name = \App\Models\Category::where('id', $value)->first()->en_name;
                                                    @endphp
                                                    <option value ="{{ $value }}"  selected>{{ $old_en_name }} </option>
                                                    @endforeach
                                                @endif

                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                    </div>
                                    @if(isset($technician))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'cat_ids'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('company_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Company') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" id="company_id" name="company_id">
                                            <option selected>Select Company</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}" @if(isset($technician) && $technician->company_id == $company->id) selected @endif @if(!empty(old('company_id')) && (old('company_id') == $company->id)) selected @endif>{{$company->en_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('sub_company') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Sub Company') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" id="sub_company" name="sub_company[]" multiple title="Select a company first,please" @if(isset($technician) == false) required @endif style="height: 150px;">
                                            {{-- <option disabled selected>Select a company first,please !</option> --}}
                                            @if(old('sub_company'))
                                                @foreach(old('sub_company') as $key => $value)
                                                    @php
                                                    $old_en_name = \App\Models\SubCompany::where('id', $value)->first()->en_name;
                                                    @endphp
                                                    <option value ="{{ $value }}"  selected>{{ $old_en_name }} </option>
                                                    @endforeach
                                                @endif
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                    </div>
                                    @if(isset($technician))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'sub_company'])
                                </div>
                            </div>

                            {{--<div class="form-group {{ $errors->has('sub_company') ? ' has-error' : '' }}">--}}
                                {{--<label class="col-md-3 col-xs-12 control-label">Sub Company</label>--}}
                                {{--<div class="col-md-6 col-xs-12">--}}
                                    {{--<div class="input-group">--}}
                                        {{--<select class="form-control" id="sub_company" name="sub_company">--}}
                                            {{--@if(isset($technician->sub_company))--}}
                                                {{--<option selected disabled>{{$technician->sub_company->en_name}} </option>--}}
                                            {{--@else--}}
                                                {{--<option selected disabled>Select Company first </option>--}}
                                            {{--@endif--}}
                                        {{--</select>--}}
                                        {{--<span class="input-group-addon"><span class="fa fa-clock-o"></span></span>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="form-group {{ $errors->has('work_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Assign role') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                    <select class="form-control  select" name="technician_role_id"  id="role">
                                            <option selected disabled>Select a role</option>
                                            @foreach($technicianRoles as $technicianRole)
                                                <option @if(isset($technicianRole))
                                                        {{   isset($technician) && ( $technicianRole->id ==  $technician->technician_role_id) ? 'selected' : '' }}
                                                        @endif value="{{$technicianRole->id}}"  {{  old('technician_role_id') == $technicianRole->id ? 'selected' : ''}}  >{{  str_replace(' ', '-',ucWords(str_replace('-', ' ',$technicianRole->name))) }}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-id-badge"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'technician_role_id'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('work_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Badge ID') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="badge_id" value="{{isset($technician) ? $technician->badge_id : old('badge_id')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-id-badge"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'badge_id'])
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


                            <div class="form-group {{ $errors->has('rotation_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">Rotation</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" name="rotation_id">
                                            <option selected disabled>Select a rotation</option>
                                            @foreach($rotations as $rotation)
                                                <option value="{{$rotation->id}}" @if(isset($technician) && $technician->rotation_id == $rotation->id) selected @endif
                                                    {{ !empty( old('rotation_id')) && old('rotation_id') == $rotation->id ? 'selected' : ''}}
                                                >{{$rotation->en_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="email" required @if(isset($technician)) value="{{$technician->email}}" @else value="{{old('email')}} @endif" />
                                        <span class="input-group-addon"><span class="fa fa-envelope-o"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'email'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="phone" @if(isset($technician)) value="{{$technician->phone}}" @else value="{{old('phone')}} @endif" required/>
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
                                        <input type="password" class="form-control" name="password" {{isset($technician) ? : 'required' }}/>
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
                                        <input type="password" class="form-control" name="password_confirmation" {{isset($technician) ? : 'required' }}/>
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

        @if(isset($technician))

        $(document).ready(function () {
	    var sub_category = "{{ implode(',',$technician->get_parent_cat($technician->cat_ids)) }}";
            sub_category ?
                $.ajax({
                    url: '/provider/tech_get_sub_cats/'+sub_category,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $.each(data, function (i, sub_cat) {
                            var selected = '{{$technician->cat_ids}}';
                            var includes = selected.includes(sub_cat.id);

                            if(includes == true)
                                $('#sub_cats').append('<option value="' + sub_cat.id + '" selected>' + sub_cat.en_name + '</option>');
                            else
                                $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.en_name + '</option>');

                        });
                        $('.select').selectpicker('refresh');

                    }
                }) : '';

	    var sub_company = "{{ implode(',',$technician->get_company_id_from_sub_company($technician->sub_company_id)) }}";
            sub_company ?
                $.ajax({
                    url: '/provider/get_sub_company/'+sub_company,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $.each(data, function (i, sub_company) {
                            var selected =  '{{isset($technician->sub_company) ? $technician->sub_company_id : ""}}' ;
                            var includes = selected.includes(sub_company.id);

                            if(includes == true)
                                $('#sub_company').append('<option value="' + sub_company.id + '" selected>' + sub_company.en_name + '</option>');
                            else
                                $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');

                        });
                        $('.select').selectpicker('refresh');
                    }
                }) : '';

        });

        @endif


        $('#category').on('change', function () {
            var parent_id = parent_id = $('#category').val();
            if (parent_id) {
                $.ajax({
                    url: '/provider/get_sub_cats/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats ').children('option:not(:first)').remove();
                        $.each(data, function (i, sub_cat) {
                            $('#sub_cats').append('<option value="' + sub_cat.id + '">' + sub_cat.parent.en_name+' - '+ sub_cat.en_name + '</option>');
                        });
                        $('.select').selectpicker('refresh');
                    }
                });
            }
        });

        $('#company_id').on('change', function () {
            var parent_id = $('#company_id').val();

	    if (parent_id) {
                $.ajax({
                    url: '/provider/get_sub_company/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#sub_company').empty();
                        // $('#sub_company').append('<option selected disabled> Select a Sub Company </option>');
                        $.each(data, function (i, sub_company) {
                            $('#sub_company').append('<option value="' + sub_company.id + '">' + sub_company.en_name + '</option>');
                        });
                        $('.select').selectpicker('refresh');
                    }
                });

            }
        });

    </script>
@endsection
