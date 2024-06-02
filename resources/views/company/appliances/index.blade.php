@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{ __('language.Appliances') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    @if(company()->hasPermissionTo('Add admin'))
                        <div class="panel-heading">
                            <a href="/company/appliances/create" class="btn btn-info" style="text-decoration:none">
                              <i class="fa fa-plus"></i> {{ __('language.New Appliance') }}
                            </a>
                        </div>
                    @endif
                    <form class="form-horizontal" method="get" action="/company/appliances/index?search=" id="searchForm">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" id="search" placeholder="Search ..." style="margin-top: 1px;"/>
                                    <span class="input-group-addon btn btn-default">
                                            <button class="btn btn-default">{{ __('language.Search now') }}</button>
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
                                    <th>#</th>
                                    <th>{{ __('language.Name') }}</th>
                                    <th>{{ __('language.Munufucturer') }}</th>
                                    <th>{{ __('language.Model') }}</th>
                                    <th>{{ __('language.QTY') }}</th>
                                    <th>{{ __('language.Serial Number') }}</th>
                                    <th>{{ __('language.Warranty Deadline') }}</th>
                                    <th>{{ __('language.Photo') }}</th>
                                    <th>{{ __('language.Condition') }}</th>
                                    <th>{{ __('language.Description') }}</th>
                                    <th>{{ __('language.Location') }}</th>
                                    <th style="width:150px">{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->munufucturer }}</td>
                                        <td>{{ $item->model }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->serial_number }}</td>
                                        <td>{{ $item->warranty_deadline }}</td>
                                        <td><img src="{{asset($item->photo)}}" style="height:50px" class="Responsive image"/></td>
                                        <td>{{ ucfirst($item->condition) }}</td>
                                        <td class="col-md-3">{{ $item->description }}</td>
                                        <td>{{ $item->location }}</td>
                                        <td>
                                            <a title="View" href="/company/appliances/{{$item->id}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                            @if(company()->hasPermissionTo('Edit admin'))
                                                <a title="Edit" href="/company/appliances/{{$item->id}}/edit"><button class="btn btn-warning btn-condensed"><i class="fa fa-edit"></i></button></a>
                                            @endif
                                            @if(company()->hasPermissionTo('Edit admin'))
                                                <button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$item->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>
                                                <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$item->id}}">
                                                    <div class="mb-container">
                                                        <div class="mb-middle warning-msg alert-msg">
                                                            <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                            <div class="mb-content">
                                                                <p>{{ __('language.Your are about to activate a individual,it will now be available for orders and search.') }}</p>
                                                                <br/>
                                                                <p>{{ __('language.Are you sure?') }}</p>
                                                            </div>
                                                            <div class="mb-footer buttons">
                                                                <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                                <form method="post" action="/company/appliances/{{ $item->id }}/delete" class="buttons">
                                                                    {{csrf_field()}}
                                                                    @method("DELETE")
                                                                    <input type="hidden" name="tech_id" value="{{$item->id}}">
                                                                    <input type="hidden" name="state" value="1">
                                                                    <button type="submit" class="btn btn-danger btn-lg pull-right">Activate</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- activate with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-activate-{{$item->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>Alert !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to activate a admin,and will be able to do business in the admin panel.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                    <form method="post" action="/company/admin/change_status" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="admin_id" value="{{$item->id}}">
                                                        <input type="hidden" name="state" value="1">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">Activate</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end activate with sound -->

                                    <!-- suspend with sound -->
                                    <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-suspend-{{$item->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to suspend a admin,and will not be able to do business in the admin panel any more.') }}</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                    <form method="post" action="/company/admin/change_status" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="admin_id" value="{{$item->id}}">
                                                        <input type="hidden" name="state" value="0">
                                                        <button type="submit" class="btn btn-primary btn-lg pull-right">{{ __('language.Suspend') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end suspend with sound -->
                                @endforeach

                                </tbody>
                            </table>
                            {{$data->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        const urlParams = new URLSearchParams(window.location.search);
        const myParam = urlParams.get('search');
        document.getElementById('search').value = urlParams.get('search');

        document.getElementById('searchForm').action += document.getElementById('search').value;

        // x = $('#searchForm').attr("action") += 'aa';
        // console.log(x);

        function changeQueryParam(query) {

        }
    </script>
@endsection
