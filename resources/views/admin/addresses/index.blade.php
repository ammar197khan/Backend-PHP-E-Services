@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        @if(Request::is('admin/addresses/all'))
            <li class="active">{{ __('language.Countries') }}</li>
        @else
            <li><a href="/admin/addresses/all">{{ __('language.Countries') }}</a></li>
            @if(isset($parent))
                <li> {{\App\Models\Address::get_address($parent)}}</li>
                <li class="active">{{ __('language.Cities') }}</li>
            @else
                <li class="active">{{ __('language.Search') }}</li>
            @endif
        @endif
    </ul>
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    @if(admin()->hasPermissionTo('Add city'))
                        <div class="panel-heading">
                            <a href="/admin/address/country/create">
                              <button type="button" class="btn btn-info">
                                <i class="fa fa-plus"></i>
                                {{ __('language.New Country') }}
                              </button>
                            </a>
                            <a href="/admin/address/city/create">
                              <button type="button" class="btn btn-info">
                                <i class="fa fa-plus"></i>
                                {{ __('language.New City') }}
                              </button>
                            </a>
                        </div>
                    @endif
                    <form class="form-horizontal" method="get" action="/admin/addresses/search">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by name" style="margin-top: 1px;"/>
                                    <span class="input-group-addon btn btn-default">
                                            <button class="btn btn-default">{{ __('language.Search now')}}</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('language.English Name') }}</th>
                                    <th>{{ __('language.Arabic Name') }}</th>
                                    @if(Request::is('admin/addresses/all'))
                                        <th>{{ __('language.Cities') }}</th>
                                    @endif

                                    @if(admin()->hasPermissionTo('Edit city') || admin()->hasPermissionTo('Delete city'))
                                        <th>{{ __('language.Operations') }}</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($addresses as $address)
                                    <tr>
                                        <td>{{$address->en_name}}</td>
                                        <td>{{$address->ar_name}}</td>
                                        @if(Request::is('admin/addresses/all'))
                                            <td>{{$address->cities->count()}}</td>
                                        @endif
                                        <td>
                                            @if(admin()->hasPermissionTo('View Address'))
                                                @if($address->parent_id == NULL && $address->cities->count() != 0)
                                                    <a title="View Cities" href="/admin/addresses/{{$address->id}}"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                                @endif
                                            @endif

                                            @if(admin()->hasPermissionTo('Edit city'))
                                                <a title="Edit" href="/admin/address/{{$address->id}}/edit"><button class="btn btn-warning btn-condensed"><i class="fa fa-edit"></i></button></a>
                                            @endif
                                            @if(admin()->hasPermissionTo('Delete city'))
                                                <button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$address->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                    <!-- danger with sound -->
                                    <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$address->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to delete an address,and you wont be able to restore its data again like providers,companies,individuals and users in this address .') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close')}}</button>
                                                    <form method="post" action="/admin/address/delete" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="address_id" value="{{$address->id}}">
                                                        <button type="submit" class="btn btn-default btn-lg pull-right">{{ __('language.Delete') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                        <!-- end danger with sound -->
                        @endforeach
                        </tbody>
                        </table>
                        {{$addresses->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
