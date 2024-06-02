@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Users') }}</li>
        <li class="active">
            {{ __('language.Make An Order') }}
        </li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="/company/user/order/store" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{ __('language.Make An Order') }}
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group {{ $errors->has('mso') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.MSO') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="smo" value="{{old('smo')}}"/>
                                        <span class="input-group-addon"><span class="fa fa-hashtag"></span></span>
                                    </div>
                                    <span class="label label-primary">{{ __('language.Optional') }}</span>
                                    @include('admin.layouts.error', ['input' => 'mso'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Type') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" name="type" id="type" data-style="btn-success" required>
                                            <option selected disabled>Select a type</option>
                                            @foreach($types as $key => $type)
                                                <option value="{{$key}}" @if($key == 'urgent') selected @endif>{{$type}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-mail-forward"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'type'])
                                </div>
                            </div>


                            <div class="form-group {{ $errors->has('scheduled_at') ? ' has-error' : '' }} timed" style="display: none;">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Date') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="date" class="form-control" name="date" value="{{\Carbon\Carbon::now()->toDateString()}}" value="{{old('date')}}"/>
                                        <span class="input-group-addon"><span class="fa fa-calendar"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'scheduled_at'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('scheduled_at') ? ' has-error' : '' }} timed" style="display: none;">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Time') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="time" class="form-control" name="time" value="{{\Carbon\Carbon::now()->toTimeString()}}" value="{{old('time')}}"/>
                                        <span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'scheduled_at'])
                                </div>
                            </div>


                            <div class="form-group {{ $errors->has('user_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.User') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <label class="form-control">
                                            {{$user->en_name}}
                                        </label>
                                        <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                    </div>
                                    <input type="hidden" name="user_id" value="{{$user->id}}">
                                    @include('admin.layouts.error', ['input' => 'user_id'])
                                </div>
                            </div>


                            <div class="form-group {{ $errors->has('cat_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Main Category') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" id="main_cats" name="main_cat" required>
                                            <option selected disabled>Select A Main Category</option>
                                            @foreach($cats as $cat)
                                                <option value="{{$cat->id}}">{{$cat->en_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cubes"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'cat_id'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('cat_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Sub Category') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control" name="cat_id" data-style="btn-success" id="sub_cats" required>
                                            <option selected disabled>{{ __('language.Select A Category First') }}</option>
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'cat_id'])
                                </div>
                            </div>
                            <input type="hidden" class="form-control" name="roleName" value="{{  $companyProcessType['order_process_id'] }}" id="role-name" />
                            <div class="form-group {{ $errors->has('tech_id') ? ' has-error' : '' }} tech-sup">
                                <label class="col-md-3 col-xs-12 control-label">{{    $companyProcessType['order_process_id'] == '1' ? 'Supervisors': "Technicians"  }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control" name="tech_id" data-style="btn-success" id="technician" required>
                                            <option selected value="" >  Select an {{    $companyProcessType['order_process_id']== '1' ? 'Supervisors': "Technicians"  }}</option>
                                            @foreach($technicians as $tech)
                                                <option value="{{$tech->id}}">{{ $tech->en_name }} {{ !empty($tech->technicainRole) && !empty($tech->technicainRole->name) ? Illuminate\Support\Str::ucfirst($tech->technicainRole->name) : ''  }}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'tech_id'])
                                </div>
                            </div>
{{--
                            <div class="form-group {{ $errors->has('service_type') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Service Type') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control" name="service_type" data-style="btn-success" required>
                                            <option selected value="1">Preview</option>
                                            <option @if(request('service_type') == 2) selected @endif value="2">Preview & Maintenance</option>
                                            <option @if(request('service_type') == 3) selected @endif value="3">Preview & Maintenance & Structure</option>
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-cube"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'service_type'])
                                </div>
                            </div> --}}

                            <div class="form-group {{ $errors->has('place') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Problem place') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="place" maxlength="190" value="{{old('place')}}"/>
                                        <span class="input-group-addon"><span class="fa fa-hashtag"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'place'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('part') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Part to be fixed') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="part" maxlength="190" value="{{old('part')}}"/>
                                        <span class="input-group-addon"><span class="fa fa-hashtag"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'part'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('desc') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Description') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <textarea class="form-control" name="desc">{{ old('desc') }}</textarea>
                                        <span class="input-group-addon"><span class="fa fa-hashtag"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'desc'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('images') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Image') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="form-control" name="images[]" multiple/>
                                        <span class="input-group-addon"><span class="fa fa-hashtag"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'images'])
                                </div>
                            </div>


                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                              {{ __('language.Order') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        $('#type').on('change', function(e)
        {
            if(e.target.value == 'scheduled')
            {
                $('.timed').show();
                $('.tech-sup').hide();
            }
            else
            {
                $('.timed').hide();
                $('.tech-sup').show();
            }
        });

        $('#main_cats').on('change', function (e) {
            var parent_id = e.target.value;
            if (parent_id) {
                $.ajax({
                    url: '/company/get_sub_cats_company/'+parent_id,
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

        $('#sub_cats').on('change', function (e) {
            var parent_id = e.target.value;
            var role_name = $('#role-name').val();
            if (parent_id) {
                $.ajax({
                    url: '/company/get_technician/'+parent_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#technician').empty();
                        if(role_name == 1){
                            $('#technician').append('<option selected disabled> Select a Supervisor </option>');

                        }else if(role_name == 2){
                            $('#technician').append('<option selected disabled> Select a Technician </option>');
                        }
                        $.each(data, function (i, tech) {
                            $('#technician').append('<option value="' + tech.id + '">' + tech.en_name + '  '+( !empty(tech.technicain_role) && !empty(tech.technicain_role.name) ? capitalize(tech.technicain_role.name) : ''  ) + '</option>');
                        });
                    }
                });

            }
        });


    </script>

@endsection
