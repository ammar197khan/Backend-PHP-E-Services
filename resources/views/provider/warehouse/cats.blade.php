@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.Warehouse') }}</li>
        @if(Request::is('provider/warehouse/all'))
            <li class="active">{{ __('language.Categories') }}</li>
        @else
            <li><a href="/provider/warehouse/all">{{ __('language.Categories') }}</a></li>
            <li>
                {{\App\Models\Category::get_cat($parent)}}
            </li>
            <li class="active">{{ __('language.Sub Categories') }}</li>
        @endif
    </ul>
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{--@if(provider()->hasPermissionTo('warehouse_operate'))--}}
                            <a href="/provider/warehouse/item/create">
                              <button type="button" class="btn btn-info">
                                <i class="fa fa-plus"></i>
                                New Item
                              </button>
                            </a>
                        {{--@endif--}}
                        {{--@if(provider()->hasPermissionTo('warehouse_file_upload'))--}}
                            <a href="/provider/warehouse/excel/view">
                              <button type="button" class="btn btn-info">
                                <i class="fa fa-upload"></i>
                                {{ __('language.Import Items') }}
                              </button>
                            </a>
                            <a href="/provider/warehouse/images/view">
                              <button type="button" class="btn btn-info">
                                <i class="fa fa-upload"></i>
                                {{ __('language.Import Items Images') }}
                              </button>
                            </a>
                        {{--@endif--}}

                        <a href="/provider/warehouse/excel/parts/export" style="float: right;">
                          <button type="button" class="btn btn-success">
                            <i class="fa fa-file-excel-o"></i>
                            {{ __('language.Export Items') }}
                          </button>
                        </a>
                        <a href="/provider/warehouse/excel/categories/export" style="float: right; margin-right: 3px;">
                          <button type="button" class="btn btn-success">
                            <i class="fa fa-file-excel-o"></i>
                            {{ __('language.Export categories') }}
                          </button>
                        </a>
                    </div>

                    <form class="form-horizontal" method="get" action="/provider/warehouse/search">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by item name or code" style="margin-top: 1px;"/>
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
                                    <th>{{ __('language.English Name') }}</th>
                                    <th>{{ __('language.Arabic Name') }}</th>
                                    @if(Request::is('provider/categories/all'))
                                        <th>{{ __('language.Sub Categories') }}</th>
                                    @endif
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{$category->id}}</td>
                                        <td>{{$category->en_name}}</td>
                                        <td>{{$category->ar_name}}</td>
                                        @if(Request::is('provider/categories/all'))
                                            <td>{{$category->sub_cats->count()}}</td>
                                        @endif
                                        <td>
                                            @if($category->parent_id == NULL)
                                                <a title="View Sub Categories" href="/provider/warehouse/{{$category->id}}"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                            @else
                                                <a title="View Items" href="/provider/warehouse/{{$category->id}}/items"><button class="btn btn-info btn-condensed"><i class="fa fa-cubes"></i></button></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$categories->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
