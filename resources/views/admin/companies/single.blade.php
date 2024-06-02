@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li> <a href="/admin/companies">{{ __('language.Companies') }}</a></li>
        <li class="active">{{isset($company) ? __('language.Update a company') :  __('language.Create a company')}}</li>
    </ul>
    <!-- END BREADCRUMB -->
{{--    {{dd($errors)}}--}}
    <div class="page-content-wrap">

        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="{{isset($company) ? '/admin/company/update' : '/admin/company/store'}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{isset($company) ? __('language.Update an company') : __('language.Create an company')}}
                            </h3>
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">
                                    <h2 style="color: #33414E">
                                        {{ __('language.Company Info') }}
                                    </h2>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Country') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control select" name="country" id="country">
                                            <option disabled selected>{{ __('language.Choose a country') }}</option>
                                            @forelse($countries as $country)
                                                <option value="{{$country->id}}" {{  !empty(old('country')) &&  old('country') == $country->id ? 'selected' : ''}} >{{$country->en_name}}</option>
                                            @empty
                                                <option selected disabled>{{ __('language.Please add a country first in order to select a city.') }}</option>
                                            @endforelse
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-flag"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'parent_id'])
                                    @if(isset($company))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.City') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <select class="form-control" id="city" name="address_id">
                                                <option selected disabled>{{ __('language.Please choose a country first in order to select a city.') }}</option>
                                                @if(old('address_id'))
                                                <option value="{{ old('address_id') }}" selected> {{  \App\Models\Address::where('id', old('address_id'))->first()->en_name }}</option>
                                                @endif
                                        </select>
                                        <span class="input-group-addon"><span class="fa fa-flag"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'address_id'])
                                    @if(isset($company))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                </div>
                            </div>

                            @if(isset($company))
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Full Address') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                            <label class="form-control">
                                                {{$company->address->parent->en_name}} -  {{$company->address->en_name}}
                                            </label>
                                        <span class="input-group-addon"><span class="fa fa-map-marker"></span></span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="en_name" @if(isset($company)) value="{{$company->en_name}}" @else value="{{old('en_name')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'en_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="ar_name" @if(isset($company)) value="{{$company->ar_name}}" @else value="{{old('ar_name')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-info-circle"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'ar_name'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('en_desc') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Description') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <textarea class="form-control" name="en_desc" rows="5" required>{{isset($company) ? $company->en_desc : old('en_desc')}}</textarea>
                                        <span class="input-group-addon"><span class="fa fa-file-text"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'en_desc'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('ar_desc') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Arabic Description') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <textarea class="form-control" name="ar_desc" rows="5" required>{{isset($company) ? $company->ar_desc : old('ar_desc')}}</textarea>                                        <span class="input-group-addon"><span class="fa fa-file-text"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'ar_desc'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="email" required @if(isset($company)) value="{{$company->email}}" @else value="{{old('email')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-envelope-o"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'email'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('phones') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Phones') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <div id="field">
                                            @if(isset($company))
                                                @foreach(unserialize($company->phones) as $phone)
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
                                    <a><button type="button" onclick="add_phone();" class="btn btn-primary" style="margin-top: 5px;">{{ __('language.Add one more phone field') }}</button></a>
                                    @include('admin.layouts.error', ['input' => 'phones'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('item_limit') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Item Limit') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control" name="item_limit" required @if(isset($company)) value="{{$company->item_limit}}" @else value="{{old('item_limit')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-dollar"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'item_limit'])
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('interest_fee') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Interest Fee') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="number" min="0" class="form-control" name="interest_fee" required @if(isset($company)) value="{{$company->interest_fee}}" @else value="{{old('interest_fee')}}" @endif/>
                                        <span class="input-group-addon"><span class="fa fa-dollar"></span></span>
                                    </div>
                                    @include('admin.layouts.error', ['input' => 'interest_fee'])
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">Vat %</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                    <input class="form-control" type="number" min="0" name="vat" value="{{ isset($company)? $company->vat : ''}}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">Vat Registration</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                    <input class="form-control" type="text" name="vat_registration" value="{{ isset($company)? $company->vat_registration : ''}}"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label" >Supervisor Assessment</label>

                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">

                                    <input type="radio" class="form-check-input" name="order_process_id" value="1"  @if(isset($company) && isset( $company->orderProcessType) &&  isset($company->orderProcessType->name) && ($company->orderProcessType->name == 'Supervisor Assessment'))  checked
                                    @endif  >
                                    </div>
                                </div>

                            </div>
                             <div class="form-group">
                            <label class="col-md-3 col-xs-12 control-label">Direct Technician Assignment</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                        <input type="radio" class="form-check-input" name="order_process_id" value = "2"   @if( isset($company) && isset( $company->orderProcessType) &&  isset($company->orderProcessType->name) && ($company->orderProcessType->name == 'Direct Technician Assignment'))  checked
                                        @endif
                                        >
                                        </div>
                                    </div>

                                </div>

                            <div class="form-group {{ $errors->has('logo') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.logo') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="logo" id="cp_photo" data-filename-placement="inside" title="{{ __('language.Select file') }}" @if(isset($company) == false) required @endif/>
                                    </div>
                                    @if(isset($company))
                                        <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @endif
                                    <br/>
                                    @include('admin.layouts.error', ['input' => 'logo'])
                                    @if(isset($company))
                                        <img src="/companies/logos/{{$company->logo}}" style="width: 300px; height: 300px; margin-top: 3px; border: #33414E solid 1px;">
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">CR Upload</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="cr_upload" id="cr_upload" data-filename-placement="inside" title="Select File"/>
                                    </div>
                                    <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @include('admin.layouts.error', ['input' => 'cr_upload'])
                                </div>

                            </div>
                            <div class="form-group">
                            @if(  isset($company) && !empty($company->cr_upload))
                            <div class="col-md-3 col-xs-12">
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <a href="{{ route('company.get.download', [ "file_name" => $company->cr_upload]) }}"> Download Previouse CR</a>
                            </div>
                            @endif
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">VAT Certificate  Upload</label>
                                <div class="col-md-6 col-xs-12">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="vat_certificate_upload" id="vat_certificate_upload" data-filename-placement="inside" title="Select File"/>
                                    </div>
                                    <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @include('admin.layouts.error', ['input' => 'vat_certificate_upload'])
                                </div>

                            </div>
                            <div class="form-group">
                            @if(isset($company) && !empty($company->vat_upload))
                            <div class="col-md-3 col-xs-12">
                            </div>
                            <div class="col-md-9 col-xs-12">
                                <a href="{{ route('company.get.download', [ "file_name" => $company->vat_upload]) }}"> Download Previouse VAT</a>
                            </div>
                            @endif
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">Agreement Upload</label>
                                <div class="col-md-6 col-xs-6">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="agreement_upload" id="agreement_upload" data-filename-placement="inside" title="Select File"/>
                                    </div>
                                    <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @include('admin.layouts.error', ['input' => 'agreement_upload'])
                                </div>

                            </div>
                            <div class="form-group">
                            @if(isset($company) && !empty($company->agreement_upload))
                            <div class="col-md-3 col-xs-12">
                            </div>
                            <div class="col-md-9 col-xs-12">
                                <a href="{{ route('company.get.download', [ "file_name" => $company->agreement_upload]) }}"> Download Previouse Agreement</a>
                            </div>
                            @endif
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">
                                    <h2 style="color: #33414E">
                                        Organization Detail
                                    </h2>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.English Organization Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input class="form-control" name="en_organization_name" value="{{ isset($company) ? $company->en_organization_name : ''}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{ __('language.Arabic Organization Name') }}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input class="form-control" name="ar_organization_name" value="{{ isset($company) ? $company->ar_organization_name : ''}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">PO Box</label>
                                <div class="col-md-6 col-xs-12">
                                    <input class="form-control" type="text"  name="po_box" value="{{isset($company)? $company->po_box : ''}}"/>
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
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Username') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="username" value="{{isset($company) ? $company->admin->username : old('username')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'username'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Password') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password" @if(isset($company) == false) required  @else placeholder = "{{ __('language.Leave it there if no changes') }}" @endif/>
                                            <span class="input-group-addon"><span class="fa fa-asterisk"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'password'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Re-Type Password') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password_confirmation" @if(isset($company) == false) required @else placeholder = "{{ __('language.Leave it there if no changes') }}"@endif/>
                                            <span class="input-group-addon"><span class="fa fa-asterisk"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'password_confirmation'])
                                    </div>
                                </div>

                            @if(isset($company) == false)
                                <div class="form-group {{ $errors->has('mobile') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Mobile') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="mobile" value="{{old('mobile')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-mobile"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'mobile'])
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('badge_id') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{ __('language.Badge ID') }}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="badge_id" value="{{old('badge_id')}}" required/>
                                            <span class="input-group-addon"><span class="fa fa-id-badge"></span></span>
                                        </div>
                                        @include('admin.layouts.error', ['input' => 'badge_id'])
                                    </div>
                                </div>

                            @endif

                            @if(isset($company))
                                <input type="hidden" name="company_id" value="{{$company->id}}">
                            @endif
                        </div>

                        <div class="panel-footer">
                            <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                            <button class="btn btn-primary pull-right">
                                {{isset($company) ? __('language.Update') : __('language.Create') }}
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
