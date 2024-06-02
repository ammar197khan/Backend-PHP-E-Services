@php
$provider = isset($provider) ? $provider : NULL;
@endphp
@extends('admin.layouts.app')
@section('content')
    <style>
        .input-group-addon {
            border-color: #33414e00 !important;
            background-color: #33414e !important;
            font-size: 13px;
            padding: 0px 0px 0px 3px;
            line-height: 26px;
            color: #FFF;
            text-align: center;
            min-width: 36px;
        }
    </style>
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Providers') }}</li>
        <li class="active">{{isset($provider) ?  __('language.Update a provider')  : __('language.Create a provider') }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    {{--{{dd($errors)}}--}}
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="{{isset($provider) ? '/admin/provider/update' : '/admin/provider/store'}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($provider) ?  __('language.Update an provider'): __('language.Create an provider')}}
                            </h3>
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">
                                    <h2 style="color: #33414E">
                                     {{  __('language.Provider Info') }}
                                    </h2>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 col-xs-12 control-label"> {{  __('language.Country') }}</label>
                                <div class="col-md-4 col-xs-12 {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <select class="form-control select" name="country" id="country">
                                            <option selected disabled>{{  __('language.Select a country') }}</option>
                                            @foreach($addresses as $address)
                                                <option value="{{$address->id}}"  {{  !empty(old('country')) &&  old('country') == $address->id ? 'selected' : ''}}     @if(isset($provider) && $provider->parent_id == $address->id) selected @endif>{{$address->en_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-flag"></span></span>
                                    </div>
                                    @if(isset($provider))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'parent_id'])
                                </div>


                                <label class="col-md-2 col-xs-12 control-label">{{  __('language.City') }}</label>
                                <div class="col-md-4 col-xs-12 {{ $errors->has('address_id') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <select class="form-control" id="city" name="address_id" required>
                                                <option disabled selected>{{ __('language.Select a country first,please') }} !</option>
                                                @if(old('address_id'))
                                                <option value="{{ old('address_id') }}" selected> {{  \App\Models\Address::where('id', old('address_id'))->first()->en_name }}</option>
                                                @endif
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-flag"></span></span>
                                    </div>
                                    @if(isset($provider))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    @include('admin.layouts.error', ['input' => 'address_id'])
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-4 col-xs-12 {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="en_name" required @if(isset($provider)) value="{{$provider->en_name}}" @else value="{{old('en_name')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-file-text"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'en_name'])
                                </div>


                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-4 col-xs-12 {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="ar_name" required @if(isset($provider)) value="{{$provider->ar_name}}" @else value="{{old('ar_name')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-file-text"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'ar_name'])
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.English Description') }}</label>
                                <div class="col-md-4 col-xs-12 {{ $errors->has('en_desc') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <textarea class="form-control" name="en_desc" rows="5" required>{{isset($provider) ? $provider->en_desc : old('en_desc')}}</textarea>
                                        <span class="input-group-addon"><span class="fa fa-file-text"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'en_desc'])
                                </div>

                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.Arabic Description') }}</label>
                                <div class="col-md-4 col-xs-12 {{ $errors->has('ar_desc') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <textarea class="form-control" name="ar_desc" rows="5" required>{{isset($provider) ? $provider->ar_desc : old('ar_desc')}}</textarea>                                        <span class="input-group-addon"><span class="fa fa-file-text"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'ar_desc'])
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.English Organization Name') }}</label>
                                <div class="col-md-4 col-xs-12">
                                    <input class="form-control" name="en_organization_name" value="{{$provider->en_organization_name}}"/>
                                </div>

                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.Arabic Organization Name') }}</label>
                                <div class="col-md-4 col-xs-12">
                                    <input class="form-control" name="ar_organization_name" value="{{$provider->ar_organization_name}}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-4 col-xs-12 {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="email" required @if(isset($provider)) value="{{$provider->email}}" @else value="{{old('email')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-envelope-o"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'email'])
                                </div>

                                <label class="col-md-2 col-xs-12 control-label">{{  __('language.Phones') }}</label>
                                <div class="col-md-4 col-xs-12 {{ $errors->has('phones') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <div id="field">
                                            @if(isset($provider))
                                                @foreach(unserialize($provider->phones) as $phone)
                                                    <input type="text" class="form-control phone" value="{{$phone}}" name="phones[]"/>
                                                @endforeach
                                            @else
                                            @if(old('phones'))
                                               @foreach(old('phones') as $phone)
                                                    <input type="text" class="form-control phone" value="{{$phone}}" name="phones[]"/>
                                                @endforeach
                                                @else
                                                <input type="text" class="form-control phone" placeholder="Phone No." name="phones[]" required/>
                                                @endif
                                            @endif
                                        </div>
                                        <span class="input-group-addon"><span class="fa fa-phone"></span></span>
                                    </div>
                                    <a><button type="button" onclick="add_phone();" class="btn btn-primary" style="margin-top: 5px;"><i class="fa fa-plus-circle" aria-hidden="true"></i>Add Phone</button></a>
                                    @include('admin.layouts.error', ['input' => 'phones'])
                                </div>
                            </div>

                            @if(isset($provider))
                                <div class="form-group">
                                    <label class="col-md-2 col-xs-12 control-label">{{ __('language.Full Address') }}</label>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="input-group">
                                            @if(isset($provider))
                                                <label class="form-control">
                                                    {{$provider->address->parent->en_name}} -  {{$provider->address->en_name}}
                                                </label>
                                            @endif
                                            <span class="input-group-addon"><span class="fa fa-map-marker"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'en_name'])
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label class="col-md-2 col-xs-12 control-label">Vat %</label>
                                <div class="col-md-4 col-xs-12">
                                    <input type="number" class="form-control" min="0"  name="vat" value="{{$provider->vat}}"/>
                                </div>
                                <label class="col-md-2 col-xs-12 control-label">Vat Registration</label>
                                <div class="col-md-4 col-xs-12">
                                    <input type="text" class="form-control" name="vat_registration"  value="{{ $provider->vat_registration }}"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-2 col-xs-12 control-label">CR Upload</label>
                                <div class="col-md-4 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="cr_upload" id="cr_upload" data-filename-placement="inside" title="Select File"/>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'cr_upload'])
                                    @if(!empty($provider->cr_upload))
                                    <span class="label label-warning"><a href="{{ route('provider.get.download', [ "file_name" => $provider->cr_upload]) }}"> Download Previouse CR</a></span>
                                     @endif
                                </div>




                                <label class="col-md-2 col-xs-12 control-label">VAT Certificate  Upload</label>
                                <div class="col-md-4 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="vat_certificate_upload" id="vat_certificate_upload" data-filename-placement="inside" title="Select File"/>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'vat_certificate_upload'])
                                    @if(!empty($provider->vat_upload))
                                    <span class="label label-warning"><a href="{{ route('provider.get.download', [ "file_name" => $provider->vat_upload]) }}"> Download Previouse VAT</a></span>
                                    @endif
                                </div>



                        </div>
                        <div class="form-group">

                                <label class="col-md-2 col-xs-12  control-label">Agreement Upload</label>
                                <div class="col-md-4 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="agreement_upload" id="agreement_upload" data-filename-placement="inside" title="Select File"/>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'agreement_upload'])
                                    @if(!empty($provider->agreement_upload))
                                    <span class="label label-warning"><a href="{{ route('provider.get.download', [ "file_name" => $provider->agreement_upload]) }}"> Download Previouse Agreement</a></span>
                                    @endif
                                </div>




                            <label class="col-md-2 col-xs-12 control-label">{{ __('language.PO Box') }}</label>
                            <div class="col-md-4 col-xs-12">
                                <input class="form-control" name="po_box" value="{{ $provider->po_box }}" style="margin-bottom: 2px;"/>
                            </div>

                            </div>



                            <div class="form-group">
                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.Type') }}</label>
                                <div class="col-md-4 col-xs-12 {{ $errors->has('type') ? ' has-error' : '' }}">
                                    <div class="input-group">
                                        <select id='type' name="type" class="form-control select" onchange="updateTypeInput()">
                                            <option value="percentage" {{ optional($provider)->type == 'percentage' ? 'selected' : '' }}>{{__('language.Percentage')}} </option>
                                            <option value="cash" {{ optional($provider)->type == 'cash' ? 'selected' : '' }}>{{__('language.Cash')}}</option>
                                            <option value="categorized" {{ optional($provider)->type == 'categorized' ? 'selected' : '' }}>{{__('language.Categorized')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" id="commission_array">
                                <div class="row" id="first_commission_row">
                                    <div class="col-md-offset-2 col-md-2 col-xs-12">
                                        <h6>{{ __('language.From') }}</h6>
                                      <input type="number" placeholder="From" class="form-control" name="commission_from[]" value="0" required>
                                    </div>
                                    <div class="col-md-2 col-xs-12">
                                        <h6>{{  __('language.To') }}</h6>
                                      <input type="number" placeholder="To" class="form-control" name="commission_to[]" value="0" required>
                                    </div>
                                    <div class="col-md-2 col-xs-12">
                                        <h6>{{ __('language.Price') }}</h6>
                                      <input type="number" placeholder="Commission" class="form-control" name="commission_value[]" value="0" required>
                                    </div>
                                    <div class="col-md-1">
                                        <i onclick="removeCommissionSection(this)" class="fa fa-minus-circle fa-2x text-danger" aria-hidden="true" style="cursor:pointer"></i>
                                    </div>
                                </div>
                                @if (optional($provider)->type == 'categorized')
                                  @foreach ($provider->commission_categories as $catKey => $catValue)
                                      <div class="row" id="first_commission_row">
                                          <div class="col-md-offset-2 col-md-2 col-xs-12">
                                              <h6>{{ __('language.From') }}</h6>
                                            <input type="number" placeholder="From" class="form-control" name="commission_from[]" value="{{ explode(':', $catKey)[0] }}" required>
                                          </div>
                                          <div class="col-md-2 col-xs-12">
                                              <h6>To</h6>
                                            <input type="number" placeholder="To" class="form-control" name="commission_to[]" value="{{ explode(':', $catKey)[1] }}" required>
                                          </div>
                                          <div class="col-md-2 col-xs-12">
                                              <h6>{{ __('language.Price') }}</h6>
                                            <input type="number" placeholder="Commission" class="form-control" name="commission_value[]" value="{{ $catValue }}" required>
                                          </div>
                                          <div class="col-md-1">
                                              <i onclick="removeCommissionSection(this)" class="fa fa-minus-circle fa-2x text-danger" aria-hidden="true" style="cursor:pointer"></i>
                                          </div>
                                      </div>
                                  @endforeach
                                @endif

                                <div class="col-md-12 col-xs-12">
                                  <button type="button" onclick="addCommissionSection(this)" class="btn btn-primary col-md-offset-3" style="margin-top: 5px;">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    Add Section
                                  </button>
                                </div>

                            </div>

                            <div class="form-group">

                                <div id="commission_value">
                                    <label class="col-md-2 col-xs-12 control-label">{{ __('language.Interest Fee') }}</label>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="input-group {{ $errors->has('interest_fee') ? ' has-error' : '' }}">
                                            <input type="number" min="0" class="form-control" name="interest_fee" required @if(isset($provider)) value="{{$provider->interest_fee}}" @else value="{{ !empty(old('interest_fee')) ? old('interest_fee'): 0  }}" @endif/>
                                            <span class="input-group-addon"><span class="fa fa-dollar"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'interest_fee'])
                                    </div>
                                </div>

                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.Warehouse Fee') }}</label>
                                <div class="col-md-4 col-xs-12">
                                    <div class="input-group {{ $errors->has('warehouse_fee') ? ' has-error' : '' }}">
                                        <input type="number" min="0" class="form-control" name="warehouse_fee" required @if(isset($provider)) value="{{$provider->warehouse_fee}}" @else value="{{old('warehouse_fee')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-dollar"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'warehouse_fee'])
                                </div>

                            </div>

                            <div class="form-group {{ $errors->has('logo') ? ' has-error' : '' }}">
                                <label class="col-md-2 col-xs-12 control-label">{{ __('language.logo') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="logo" id="cp_photo" data-filename-placement="inside" title="Select file" @if(isset($provider) == false) required @endif/>
                                    </div>
                                    @if(isset($provider))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    <br/>
                                    @include('admin.layouts.error', ['input' => 'logo'])
                                    @if(isset($provider))
                                        <img src="/providers/logos/{{$provider->logo}}" style="width: 300px; height: 300px; margin-top: 3px; border: #33414E solid 1px;">
                                    @endif
                                </div>
                            </div>


                                <div class="form-group">
                                    <label class="col-md-3 col-xs-12 control-label">
                                        <h2 style="color: #33414E">
                                            {{ __('language.Super Admin Info') }}
                                        </h2>
                                    </label>
                                </div>

                                <div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
                                    <label class="col-md-2 col-xs-12 control-label">{{ __('language.Username') }}</label>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="username" value="{{isset($provider) ? $provider->admin->username : old('username')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'username'])
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 col-xs-12 control-label">{{ __('language.Password') }}</label>
                                    <div class="col-md-4 col-xs-12  {{ $errors->has('password') ? ' has-error' : '' }}">
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password" @if(isset($provider) == false) required @endif/>
                                            <span class="input-group-addon"><span class="fa fa-asterisk"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'password'])
                                    </div>


                                    <label class="col-md-2 col-xs-12 control-label">{{ __('language.Re-Type Password') }}</label>
                                    <div class="col-md-4 col-xs-12 {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password_confirmation" @if(isset($provider) == false) required @endif/>
                                            <span class="input-group-addon"><span class="fa fa-asterisk"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'password_confirmation'])
                                    </div>
                                </div>

                            @if(isset($provider) == false)
                                <div class="form-group {{ $errors->has('mobile') ? ' has-error' : '' }}">
                                    <label class="col-md-2 col-xs-12 control-label">{{ __('language.Mobile') }}</label>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="mobile" value="{{old('mobile')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-mobile"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'mobile'])
                                    </div>
                                    <label class="col-md-2 col-xs-12 control-label">{{ __('language.Badge ID') }}</label>
                                    <div class="col-md-4 col-xs-12 {{ $errors->has('badge_id') ? ' has-error' : '' }}">
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="badge_id" value="{{old('badge_id')}}"required/>
                                            <span class="input-group-addon"><span class="fa fa-id-badge"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'badge_id'])
                                    </div>
                                </div>

                            @endif


                            @if(isset($provider))
                                <input type="hidden" name="provider_id" value="{{$provider->id}}">
                            @endif
                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                {{isset($provider) ?  __('language.Update') : __('language.Create') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        var type = $('#type').val();

        if (type == 'categorized') {
          $('#commission_array').show();
          $('#commission_value').hide();
        } else {
          $('#commission_array').hide();
          $('#commission_value').show();
        }

        function updateTypeInput() {
            var type = $('#type').val();
            if (type == 'categorized') {
              $('#commission_array').show();
              $('#commission_value').hide();
            } else {
              $('#commission_array').hide();
              $('#commission_value').show();
            }
        }

        function removeCommissionSection(x) {
          $(x).parent().parent().remove();
        }

        var commission_row_clone =  $('#first_commission_row').clone();
        @if (optional($provider)->type == 'categorized')
          $('#first_commission_row').remove();
        @endif
        function addCommissionSection(x) {
            $(commission_row_clone).clone().insertBefore($(x).parent());
        }

        function add_phone() {
            var row = '<input type="text" class="form-control phone" placeholder="Phone No." name="phones[]" style="margin-top: 5px;"/>';
            $('#field').append(row);
        }

        $('#country').on('change', function (e) {
            var parent_id = e.target.value;
            if (parent_id) {
                $.ajax({
                    url: '/admin/get_cities/'+parent_id,
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
