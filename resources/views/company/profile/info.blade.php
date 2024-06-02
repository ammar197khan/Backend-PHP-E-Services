@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    @php
        if($company->active == 1)
        {
            $state = 'active';
            $name = 'Active';
        }
        elseif($company->active == 0)
        {
            $state = 'suspended';
            $name = 'Suspended';
        }
    @endphp

    <ul class="breadcrumb">
        <li><a href="{{route('company.home')}}">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{$company->en_name}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- PAGE TITLE -->
    <div class="page-title">
        <h2><span class="fa fa-eye"></span> {{ __('language.View Company Info') }}</h2>
    </div>
    <!-- END PAGE TITLE -->
    @include('company.layouts.message')
    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">
    <form method="post" action="{{route('company.profile.info.update')}}" class="form-horizontal" enctype="multipart/form-data">

        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-5">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-industry"></span> {{$company->en_name}} </h3>
                            <p>
                                @if($company->active == 1)
                                    <span class="label label-success label-form"> {{ __('language.Active company') }} </span>
                                @elseif($company->active == 0)
                                    <span class="label label-primary label-form"> {{ __('language.Suspended company') }} </span>
                                @endif
                            </p>
                                 <div class="text-center" id="user_image">
                                <img src="/companies/logos/{{$company->logo}}" class="img-thumbnail" width="300px" height="300px"/>
                            </div>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">#{{ __('language.ID') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$company->id}} </span>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="col-md-6 col-sm-8 col-xs-7">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-pencil"></span> {{ __('language.Profile') }}</h3>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Location') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$company->address->parent->en_name}} - {{$company->address->en_name}} </span>
                                    <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    <br/>
                                    <br/>
                                    <select class="form-control select" id="country">
                                        <option selected disabled>{{ __('language.Select a country') }}</option>
                                        @foreach($addresses as $address)
                                            <option value="{{$address->id}}" @if(isset($company) && $company->parent_id == $address->id) selected @endif>{{$address->en_name}}</option>
                                        @endforeach
                                    </select>
                                    <br/>
                                    <br/>
                                    <select class="form-control" id="city" name="address_id">
                                        <option selected disabled>{{ __('language.Select a country first') }}</option>
                                    </select>
                                </div>

                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="en_name" value="{{$company->en_name}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="ar_name" value="{{$company->ar_name}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <textarea class="form-control" name="en_desc" rows="5">{{$company->en_desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <textarea class="form-control" rows="5" name="ar_desc">{{$company->ar_desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Organization Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="en_organization_name" value="{{$company->en_organization_name}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Organization Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="ar_organization_name" value="{{$company->ar_organization_name}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" name="email" value="{{$company->email}}"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Phone') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <div id="field">
                                        @foreach(unserialize($company->phones) as $phone)
                                            <input class="form-control" name="phones[]" value="{{$phone}}" style="margin-bottom: 2px;"/>
                                        @endforeach
                                    </div>
                                    <a><button type="button" onclick="add_phone();" class="btn btn-primary" style="margin-top: 5px;">{{ __('language.Add one more phone field') }}</button></a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">Vat%</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" type="number" min="0" name="vat" value="{{$company->vat}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">PO Box</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" type="text"  name="po_box" value="{{$company->po_box}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">Vat Registration</label>
                                <div class="col-md-9 col-xs-7">
                                    <input class="form-control" type="text"    name="vat_registration" value="{{ $company->vat_registration }}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.logo') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <div class="input-group">
                                        <input type="file" class="fileinput btn-info" name="logo" id="cp_photo" data-filename-placement="inside" title="{{ __('language.Select Image') }}"/>
                                    </div>
                                    <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                    @include('admin.layouts.error', ['input' => 'logo'])
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">{{ __('language.Update') }}</button>
                        </div>
                    </div>
            </div>

            <div class="col-md-3">


                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3><span class="fa fa-info-circle"></span> {{ __('language.Quick Info') }}</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Registration') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{$company->created_at}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label line-height-30">{{ __('language.Employees') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{$company->users->count()}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label line-height-30">{{ __('language.Orders') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">
                                @if(isset($company->orders))
                                    <a href="/company/orders/all">{{ $company->orders->count() }} </a>
                                @else
                                    <span class="label label-default">{{ __('language.No updates yet') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-6 col-xs-6 control-label">CR Upload</label>
                            <div class="col-md-6 col-xs-6">
                                <div class="input-group">
                                    <input type="file" class="fileinput btn-info" name="cr_upload" id="cr_upload" data-filename-placement="inside" title="Select File"/>
                                </div>
                                <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                @include('admin.layouts.error', ['input' => 'cr_upload'])
                            </div>

                        </div>
                        <div class="form-group">
                        @if(!empty($company->cr_upload))
                        <div class="col-md-12 col-xs-12">
                            <a href="{{ route('company.get.download', [ "file_name" => $company->cr_upload]) }}"> Download Previouse CR</a>
                        </div>
                        @endif
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 col-xs-6 control-label">VAT Certificate  Upload</label>
                            <div class="col-md-6 col-xs-6">
                                <div class="input-group">
                                    <input type="file" class="fileinput btn-info" name="vat_certificate_upload" id="vat_certificate_upload" data-filename-placement="inside" title="Select File"/>
                                </div>
                                <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                @include('admin.layouts.error', ['input' => 'vat_certificate_upload'])
                            </div>

                        </div>
                        <div class="form-group">
                        @if(!empty($company->vat_upload))
                        <div class="col-md-12 col-xs-12">
                            <a href="{{ route('company.get.download', [ "file_name" => $company->vat_upload]) }}"> Download Previouse VAT</a>
                        </div>
                        @endif
                        </div>

                        <div class="form-group">
                            <label class="col-md-6 col-xs-6 control-label">Agreement Upload</label>
                            <div class="col-md-6 col-xs-6">
                                <div class="input-group">
                                    <input type="file" class="fileinput btn-info" name="agreement_upload" id="agreement_upload" data-filename-placement="inside" title="Select File"/>
                                </div>
                                <span class="label label-warning">{{ __('language.Leave it there if no changes') }}</span>
                                @include('admin.layouts.error', ['input' => 'agreement_upload'])
                            </div>

                        </div>
                        <div class="form-group">
                        @if(!empty($company->agreement_upload))
                        <div class="col-md-12 col-xs-12">
                            <a href="{{ route('company.get.download', [ "file_name" => $company->agreement_upload]) }}"> Download Previouse Agreement</a>
                        </div>
                        @endif
                        </div>


                    </div>
                </div>
            </div>
            <div class="col-md-3">


                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3><span class="fa fa-info-circle"></span> Order Process</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        @foreach($orderProcessTypes as $orderProcessType)
                    <div class="form-group">
                                <label class="col-md-9 col-xs-7 control-label" style="text-align:left">{{ $orderProcessType->name }}</label>
                                <div class="col-md-3 col-xs-5">
                                   <input type="radio" class="form-check-input order-process-id-{{ $orderProcessType->id }}" id="order-process-id-{{ $orderProcessType->id }}" name="order_process_id" onclick="orderPrccessId( {{ $orderProcessType->id }});" value = "{{ $orderProcessType->id}}"   @if( isset($company) && isset( $company->order_process_id)  && ($company->order_process_id == $orderProcessType->id))  checked
                                    @endif
                                    >
                                </div>

                            </div>
                          @endforeach



                            <!-- <div class="form-group">

                                <div class="col-md-3 col-xs-5">
                                   <input type="radio" class="form-check-input" name="order_process_id" value="1"  @if(isset($company) && isset( $company->orderProcessType) &&  isset($company->orderProcessType->name) && ($company->orderProcessType->name == 'supervisor'))  checked
                                    @endif  >
                                </div>
                                <label class="col-md-9 col-xs-7 control-label">Supervisor Assessment</label>

                            </div> -->

                    </div>

                </div>
            </div>
        </div>
    </form>
    </div>
    <!-- END PAGE CONTENT WRAPPER -->


    <script>


       ​
        function add_phone()
        {
            var row = '<input type="text" class="form-control phone" placeholder="Phone No." name="phones[]" style="margin-bottom: 2px;"/>';
            $('#field').append(row);
        }


        $('#country').on('change', function (e) {
            var parent_id = e.target.value;
            if (parent_id) {
                $.ajax({
                    url: '/company/get_cities/'+parent_id,
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
@push('custom-scripts')

<script>
var lastCheckedId = "{{ $company->order_process_id  }}";
function orderPrccessId( Id){
        var id = Id;
    var checked = $('#order-process-id-'+id).is(':checked');
    if(checked) {
        if(!confirm('Are you sure?')){
              $("#order-process-id-"+lastCheckedId).prop("checked", true);
        }else{
            lastCheckedId = id;
            $('#order-process-id-'+id).attr("checked", "checked");
        }
    }
}

    </script>
@endpush

