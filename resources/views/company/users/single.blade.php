@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/company/users/active">{{ __('language.Users') }}</a></li>
        <li class="active">{{isset($user) ? __('language.Update an user') : __('language.Create a user')}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    {{--{{dd($errors)}}--}}
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                @include('company.layouts.message')
                <form class="form-horizontal" method="post" action="{{isset($user) ? '/company/user/update' : '/company/user/store'}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($user) ?  __('language.Update an user'): __('language.Create an user') }}
                            </h3>
                        </div>
                        <div class="panel-body">


                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Sub Companies') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <select class="form-control select" name="sub_company_id" required>
                                                <option selected disabled>{{ __('language.Please Choose a sub company') }}</option>
                                                @foreach($subs as $sub)
                                                    <option value="{{$sub->id}}" @if(isset($user) && $user->sub_company_id == $sub->id) selected @endif>{{$sub->en_name}}</option>
                                                @endforeach
                                            </select>
                                            <span class="input-group-addon"><span class="fa fa-building"></span></span>
                                        </div>
                                    </div>
                            </div>

                            <div class="form-group {{ $errors->has('badge_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Badge ID') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="badge_id" value="{{isset($user) ? $user->badge_id : old('badge_id')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-id-badge"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'badge_id'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="en_name" value="{{isset($user) ? $user->en_name : old('en_name')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'en_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="ar_name" value="{{isset($user) ? $user->ar_name : old('ar_name')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'ar_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="email" value="{{isset($user) ? $user->email : old('email')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-envelope-o"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'email'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="phone" value="{{isset($user) ? $user->phone : old('phone')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'phone'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('camp') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Camp') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="camp" value="{{isset($user) ? $user->camp : old('camp')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'camp'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('street') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Street') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="street" value="{{isset($user) ? $user->street : old('street')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'street'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('plot_no') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Plot No') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="plot_no" value="{{isset($user) ? $user->plot_no : old('plot_no')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'plot_no'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('block_no') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Block No') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="block_no" value="{{isset($user) ? $user->block_no : old('block_no')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'block_no'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('building_no') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Building No') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="building_no" value="{{isset($user) ? $user->building_no : old('building_no')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'building_no'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('apartment_no') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Apartment No') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="apartment_no" value="{{isset($user) ? $user->apartment_no : old('apartment_no')}}" required/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'apartment_no'])
                                </div>
                            </div>

                            @if( isset($house_types) )
                            <div class="form-group {{ $errors->has('house_type') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.House Type') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                            <select class="form-control select" name="house_type" required>
                                                <option selected disabled>{{ __('language.Please Choose house type') }}</option>
                                                @foreach($house_types as $house_type)
                                                    <option @if(isset($user->house_type) == $house_type->en_name) value="{{$house_type->en_name}}" selected
                                                            @else value="{{$house_type->en_name}}" @endif>{{$house_type->en_name}}</option>
                                                @endforeach
                                            </select>
                                        {{--<input type="text" class="form-control" name="house_type" value="{{isset($user) ? $user->house_type : old('house_type')}}" required/>--}}

                                            <span class="input-group-addon"><span class="fa fa-info-circle"></span>
                                    @include('admin.layouts.error', ['input' => 'house_type'])
                                </div>
                            </div>
                            </div>
                            @endif

                            <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Image') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="image" id="cp_photo" data-filename-placement="inside" title="{{ __('language.Select Image') }}"/>
                                    </div>
                                    @if(isset($user))
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
                                    @if(isset($user))
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
                                    @if(isset($user))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'password_confirmation'])
                                </div>
                            </div>

                            @if(isset($user))
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                            @endif
                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                {{isset($user) ?  __('language.Update'): __('language.Create') }}
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
                    url: '/company/get_sub_cats/'+parent_id,
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
