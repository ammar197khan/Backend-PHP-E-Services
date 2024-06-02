@extends('admin.layouts.app')
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

    @if($errors->has('password') || $errors->has('password_confirmation'))
        <script>
            $(window).load(function() {
                $('#modal_change_password').modal('show');
            });
        </script>
    @endif
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li> <a href="/admin/companies">{{ __('language.Companies') }}</a></li>
        <li class="active">{{$company->en_name}}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- PAGE TITLE -->
    <div class="page-title">
        <h2><span class="fa fa-eye"></span> {{ __('language.View Profile') }}</h2>
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

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Username') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$company->admin->username}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Email') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$company->email}} </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.View Phone') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    @foreach(unserialize($company->phones) as $phone)
                                        <span class="form-control"> {{$phone}} </span><br/>
                                    @endforeach
                                </div>
                            </div>
                            @if(!empty($company->cr_upload))
                            <div class="form-group">
                                <div class="col-md-12 col-xs-7">
                                    <a href="{{ route('admin.get.download', [ "file_name" => $company->cr_upload]) }}"> Download CR</a>
                                </div>
                            </div>
                            @endif
                            @if(!empty($company->vat_upload))
                            <div class="form-group">
                                <div class="col-md-12 col-xs-7">
                                    <a href="{{ route('admin.get.download', [ "file_name" => $company->vat_upload]) }}"> Download VAT</a>
                                </div>
                            </div>
                            @endif
                            @if(!empty($company->agreement_upload))
                            <div class="form-group">
                                <div class="col-md-12 col-xs-7">
                                    <a href="{{ route('admin.get.download', [ "file_name" => $company->agreement_upload]) }}"> Download Agreement</a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-md-6 col-sm-8 col-xs-7">

                <form action="#" class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3><span class="fa fa-pencil"></span> {{ __('language.Profile') }}</h3>
                        </div>
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$company->en_name}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Name') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$company->ar_name}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Location') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <span class="form-control"> {{$company->address->parent->en_name}} - {{$company->address->en_name}} </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.English Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <textarea class="form-control" rows="5">{{$company->en_desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Arabic Description') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <textarea class="form-control" rows="5">{{$company->ar_desc}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Items Limit') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <label class="form-control">{{$company->item_limit}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{ __('language.Interest Fee') }}</label>
                                <div class="col-md-9 col-xs-7">
                                    <label class="form-control">{{$company->interest_fee}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="panel panel-default tabs">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab1" data-toggle="tab">Organization</a></li>
                    </ul>
                    <div class="tab-content">
                        <form method="post" action="">
                            <div class="tab-pane panel-body active" id="tab1">
                                <div class="form-group">
                                    <label>English Organization Name</label>
                                    <input type="en_organization_name" class="form-control" name="en_organization_name" value="{{$company->en_organization_name}}" required>
                                    @include('admin.layouts.error', ['input' => 'email'])
                                </div>

                                <div class="form-group">
                                    <label>Arabic Organization Name</label>
                                    <input type="text" class="form-control" name="ar_organization_name	" value="{{ $company->ar_organization_name	 }}" required>
                                    @include('admin.layouts.error', ['input' => 'text'])
                                </div>


                                <div class="form-group">
                                    <label>PO Box</label>
                                    <input type="text" class="form-control" name="po_box" value="{{ $company->po_box }}" required>
                                    @include('admin.layouts.error', ['input' => 'text'])
                                </div>

                            </div>
                        </form>
                    </div>

                </div>

                <div class="panel panel-default tabs">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab1" data-toggle="tab">{{ __('language.Send Email') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <form method="post" action="/admin/mail/send">
                            {{csrf_field()}}
                            <div class="tab-pane panel-body active" id="tab1">
                                <div class="form-group">
                                    <label>{{ __('language.E-mail') }}</label>
                                    <input type="email" class="form-control" name="email" value="{{$company->email}}" required>
                                    @include('admin.layouts.error', ['input' => 'email'])
                                </div>

                                <div class="form-group">
                                    <label>{{ __('language.Subject') }}</label>
                                    <input type="text" class="form-control" name="subject" placeholder="Message subject" required>
                                    @include('admin.layouts.error', ['input' => 'subject'])
                                </div>


                                <div class="form-group">
                                    <label>{{ __('language.Message') }}</label>
                                    <textarea class="form-control" name="text" placeholder="Your message" rows="3" required></textarea>
                                    @include('admin.layouts.error', ['input' => 'text'])
                                </div>

                                <button type="submit" class="btn btn-info btn-lg pull-right">{{ __('language.Send') }}</button>
                            </div>
                        </form>
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
                            <label class="col-md-4 col-xs-5 control-label">{{ __('language.Users') }}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">
                                <a href="/admin/company/{{$company->id}}/users">{{isset($company->users) ? $company->users->count() : 0}}</a></div>
                        </div>
                    </div>

                </div>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3><span class="fa fa-cog"></span> {{ __('language.Settings') }}</h3>
                    </div>
                    <div class="panel-body form-horizontal form-group-separated">

                        @if($company->active == 1)
                            <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label">{{ __('language.Suspension') }}</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-primary mb-control" data-box="#message-box-suspend-{{$company->id}}" title="{{ __('language.Suspend')}}">{{ __('language.Suspend') }}</button>
                                </div>
                            </div>
                        @elseif($company->active == 0)
                            <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label">{{ __('language.Suspension') }}</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-success mb-control" data-box="#message-box-activate-{{$company->id}}" title="{{ __('language.Activate') }}">{{ __('language.Activate') }}</button>
                                </div>
                            </div>
                        @endif
                            {{-- <div class="form-group">
                                <label class="col-md-6 col-xs-6 control-label">Delete company</label>
                                <div class="col-md-6 col-xs-6">
                                    <button class="btn btn-danger mb-control" data-box="#message-box-danger-{{$company->id}}" title="Delete">Delete</button>
                                </div>
                            </div> --}}
                    </div>
                </div>
            </div>
            <div class="col-md-3">


                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3><span class="fa fa-info-circle"></span> Order Process</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        <div class="form-group">
                            <label class="col-md-9 col-xs-7 control-label" style="text-align:left">Supervisor Assessment</label>

                            <div class="col-md-3 col-xs-5">
                                <input type="radio" class="form-check-input" name="order_process_id" value="1"  @if(isset($company) && isset( $company->orderProcessType) &&  isset($company->orderProcessType->name) && ($company->orderProcessType->name == 'Supervisor Assessment'))  checked
                                @endif  >
                            </div>

                        </div>
                         <div class="form-group">
                        <label class="col-md-9 col-xs-7 control-label" style="text-align:left">Direct Technician Assignment</label>
                                <div class="col-md-3 col-xs-5">
                                    <input type="radio" class="form-check-input" name="order_process_id" value = "2"   @if( isset($company) && isset( $company->orderProcessType) &&  isset($company->orderProcessType->name) && ($company->orderProcessType->name == 'Direct Technician Assignment'))  checked
                                    @endif
                                    >
                                </div>

                            </div>
                    </div>

                </div>
            </div>

        </div>


    </div>
    <!-- activate with sound -->
    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$company->id}}">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                <div class="mb-content">
                    <p>{{ __('language.Your are about to activate a company,it will now be available for orders and search.') }}</p>
                    <br/>
                    <p>{{ __('language.Are you sure?') }} </p>
                </div>
                <div class="mb-footer buttons">
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                    <form method="post" action="/admin/company/change_state" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="company_id" value="{{$company->id}}">
                        <input type="hidden" name="state" value="1">
                        <button type="submit" class="btn btn-success btn-lg pull-right">{{ __('language.Activate') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end activate with sound -->

    <!-- suspend with sound -->
    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$company->id}}">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                <div class="mb-content">
                    <p>{{ __('language.Your are about to suspend a company,it wont be available for orders nor search.') }}</p>
                    <br/>
                    <p>{{ __('language.Are you sure?') }}</p>
                </div>
                <div class="mb-footer buttons">
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                    <form method="post" action="/admin/company/change_state" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="company_id" value="{{$company->id}}">
                        <input type="hidden" name="state" value="0">
                        <button type="submit" class="btn btn-primary btn-lg pull-right">{{ __('language.Suspend') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end suspend with sound -->

    <!-- danger with sound -->
    {{-- <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-danger-{{$company->id}}">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-times"></span>Alert !</div>
                <div class="mb-content">
                    <p>Your are about to delete a company,and you won't be able to restore its data again like technicians,companies and orders under this provider .</p>
                    <br/>
                    <p>Are you sure ?</p>
                </div>
                <div class="mb-footer buttons">
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                    <form method="post" action="/admin/company/delete" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="company_id" value="{{$company->id}}">
                        <button type="submit" class="btn btn-danger btn-lg pull-right">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- end danger with sound -->

    <!-- END PAGE CONTENT WRAPPER -->
@endsection
