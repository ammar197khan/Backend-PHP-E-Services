@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    @php
        if($provider->active == 1)
        {
            $state = 'active';
            $name = 'Active';
        }
        elseif($provider->active == 0)
        {
            $state = 'suspended';
            $name = 'Suspended';
        }
    @endphp

    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{$provider->en_name}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- PAGE TITLE -->
    <div class="page-title">
        <h2><span class="fa fa-eye"></span> {{ __('language.View Provider Info') }}</h2>
    </div>
    <!-- END PAGE TITLE -->
    @include('admin.layouts.message')
    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-5">

                <form action="#" class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-industry"></span> {{$provider->en_name}} </h3>
                            <p>
                                @if($provider->active == 1)
                                    <span class="label label-success label-form"> Active Provider </span>
                                @elseif($provider->active == 0)
                                    <span class="label label-primary label-form"> Suspended Provider </span>
                                @endif
                            </p>
                            <div class="text-center" id="user_image">
                                <img src="/providers/logos/{{$provider->logo}}" class="img-thumbnail" width="300px" height="300px"/>
                            </div>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">#{{ __('language.ID') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->id}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-md-6 col-sm-8 col-xs-7">
                <form method="post" action="/provider/info/update" class="form-horizontal" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-pencil"></span> {{ __('language.Profile') }}</h3>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Location') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$provider->address->parent->en_name}} - {{$provider->address->en_name}} </span>
                                    <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    <br/>
                                    <br/>
                                    <select class="form-control select" id="country">
                                        <option selected disabled>Select a country</option>
                                        @foreach($addresses as $address)
                                            <option value="{{$address->id}}" @if(isset($provider) && $provider->parent_id == $address->id) selected @endif>{{$address->en_name}}</option>
                                        @endforeach
                                    </select>
                                    <br/>
                                    <br/>
                                    <select class="form-control" id="city" name="address_id">
                                        <option selected disabled>Select a country first</option>
                                    </select>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="en_name" value="{{$provider->en_name}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="ar_name" value="{{$provider->ar_name}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <textarea class="form-control" name="en_desc" rows="5">{{$provider->en_desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <textarea class="form-control" rows="5" name="ar_desc">{{$provider->ar_desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="email" value="{{$provider->email}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Phones') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <div id="field">
                                    @foreach(unserialize($provider->phones) as $phone)
                                        <input class="form-control" name="phones[]" value="{{$phone}}" style="margin-bottom: 2px;"/>
                                    @endforeach
                                    </div>
                                <a><button type="button" onclick="add_phone();" class="btn btn-primary" style="margin-top: 5px;">{{ __('language.Add one more phone field') }}</button></a>
                            </div>
                        </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.logo') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="logo" id="cp_photo" data-filename-placement="inside" title="Select Image"/>
                                    </div>
                                    <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @include('admin.layouts.error', ['input' => 'logo'])
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-3">
                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3><span class="fa fa-info-circle"></span> {{ __('language.Quick Info') }}</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Registration') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{$provider->created_at}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label line-height-30">{{ __('language.Technicians') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{$provider->technicians->count()}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label line-height-30">{{ __('language.Orders') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{ $provider->orders->count() }}</div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- END PAGE CONTENT WRAPPER -->

    <script>
        function add_phone()
        {
            var row = '<input type="text" class="form-control phone" placeholder="Phone No." name="phones[]" style="margin-bottom: 2px;"/>';
            $('#field').append(row);
        }


        $('#country').on('change', function (e) {
            var parent_id = e.target.value;
            if (parent_id) {
                $.ajax({
                    url: '/provider/get_cities/'+parent_id,
                    type: "GET",

                    dataType: "json",

                    success: function (data) {
                        $('#city').empty();
                        $('#city').append('<option selected disabled> Select a city </option>');
                        $.each(data, function (i, city) {
                            $('#city').append('<option value="' + city.id + '">' + city.en_name + '</option>');
                        });
                    }
                });
            }
        });

    </script>

@endsection
