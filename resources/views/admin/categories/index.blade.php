@php
  $sorter     = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
  $direction  = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
  $dirIcon    = $direction == 'asc' ? 'desc' : 'asc';
@endphp
@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{ __('language.Categories') }}</li>
    </ul>

    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div style="height:auto" class="panel panel-default">
{{--                    @if(admin()->hasPermissionTo('categories_operate'))--}}
                        <div class="panel-heading">
                            @if(admin()->hasPermissionTo('Add category'))
                                <a href="{{ route('admin.categories.create', ['level' => 1]) }}"><button type="button" class="btn btn-info"><i class="fa fa-plus" aria-hidden="true"></i> {{ __('language.New Category') }} </button></a>
                            @endif
                            @if(admin()->hasPermissionTo('Add sub category'))
                                <a href="{{ route('admin.categories.create', ['level' => 2]) }}"><button type="button" class="btn btn-info"><i class="fa fa-plus" aria-hidden="true"></i> {{ __('language.New Sub Category') }} </button></a>
                            @endif
                            @if(admin()->hasPermissionTo('Add third category'))
                                <a href="{{ route('admin.categories.create', ['level' => 3]) }}"><button type="button" class="btn btn-info"><i class="fa fa-plus" aria-hidden="true"></i> {{ __('language.New Problem Category') }} </button></a>
                            @endif

                            <a href="/admin/categories/export?level=3" style="float: right; margin-right: 3px;"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> {{ __('language.Export Problem Categories') }} </button></a>
                            <a href="/admin/categories/export?level=2" style="float: right; margin-right: 3px;"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> {{ __('language.Export Sub Categories') }} </button></a>
                            <a href="/admin/categories/export?level=1" style="float: right; margin-right: 3px;"><button type="button" class="btn btn-success"> <i class="fa fa-file-excel-o"></i> {{ __('language.Export categories') }}  </button></a>
                        </div>
{{--                    @endif--}}
                    <a href="/admin/categories" class="btnprn pull-right" style="font-size: 20px; padding-right: 10px; text-decoration:none"> <i class="fa fa-print"></i> {{ __('language.PRINT') }}</a>
                    <script>
                        $(document).ready(function () {
                            $('.btnprn').printPage();
                        });
                    </script>
                    {{-- <form class="form-horizontal" method="get" action="/admin/categories/search">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by name" style="margin-top: 1px;"/>
                                    <span class="input-group-addon btn btn-default">
                                            <button class="btn btn-default">Search now</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form> --}}
                    <div class="panel-body" id="cats">

                        <table class="table-condensed">
                          <thead>
                            <tr style="border-bottom-style:solid; border-width:thin; border-color: #bfbfbf; background-color:default;">
                                <td></td>
                                <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.ID') }}  </b></a></td>
                                <td><a href="?sort=en_name.{{$sorter == 'en_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.English Name') }}</b></a></td>
                                <td><a href="?sort=ar_name.{{$sorter == 'ar_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'ar_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Arabic Name') }}</b></a></td>
                                <td><a href="?sort=sub_count.{{$sorter == 'sub_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'sub_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Sub Categories') }}</b></a></td>
                                <td><a href="?sort=orders_count.{{$sorter == 'orders_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'orders_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Orders Count') }}</b></a></td>
                                <td><a href="?sort=services_sales.{{$sorter == 'services_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'services_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Services Sales') }}</b></a></td>
                                <td><a href="?sort=items_sales.{{$sorter == 'items_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'items_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Items Sales') }}</b></a></td>
                                <td><a href="?sort=total_sales.{{$sorter == 'total_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'total_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Total Sales') }}</b></a></td>
                                <td><a href="?sort=rate_count.{{$sorter == 'rate_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Rate Count') }}</b></a></td>
                                <td><a href="?sort=rate_average.{{$sorter == 'rate_average' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_average' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Rate Average') }}</b></a></td>
                                <td><b>{{ __('language.Image') }}</b></td>
                                <td><b>{{ __('language.Operations') }}</b></td>
                            </tr>
                          </thead>
                          {{-- <tbody> --}}
                            @foreach ($categories as $category)
                                <tr style="border-style: solid none solid none; border-color: #bfbfbf; border-width:thin;" name="cat_{{$category->id}}" state="closed">
                                    <td class="" style="text-align:left"><a href="#" onclick="event.preventDefault();toggleTR({{$category->id}});"><i class="fa fa-plus-square fa-lg text-info" aria-hidden="true" onclick="togglePlusSquare(this);"></i></a></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>#{{ $category->id }}</b></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>{{ $category->en_name }}</b></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>{{ $category->ar_name }}</b></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>{{ $category->sub_count }}</b></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>{{ $category->orders_count }}</b></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b> @readable_int($category->services_sales)</b></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b> @readable_int($category->items_sales)</b></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b> @readable_int($category->total_sales)</b></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>{{ $category->rate_count }}</b></td>
                                    <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>@include('admin.categories.components.stars', ['rate' => $category->rate_average ?: 0])</b></td>
                                    <td class="text-center"><a href="/categories/{{$category->image}}" target="_blank"><img src="/categories/{{$category->image}}" class="img-circle" style="height:30px; filter: invert(100%) "></a></td>
                                    <td class="text-center">
                                          @if(admin()->hasPermissionTo('Edit category'))<a title="{{ __('language.Edit') }}" href="{{ route('admin.categories.edit', $category->id) }}"><i class="fa fa-edit  {{ $category->active ? '' : 'text-warning' }}"></i></a>@endif
                                          @if(admin()->hasPermissionTo('Delete category'))<a href="#" title="{{ __('language.Delete') }}" onclick="showDeleteModal({{$category->id}});" class="mb-control"><i class="fa fa-trash {{ $category->active ? '' : 'text-warning' }}"></i></a>@endif
                                    </td>
                                </tr>

                                {{-- START OF SUB-CATS --}}
                                @foreach ($category->sub_categories as $subCat)
                                  <tr name="sub_{{$category->id}}" id="sub_{{$subCat->id}}" style="display:none" loaded="false">
                                      <td><a style="cursor:pointer" onclick="event.preventDefault();toggleTR({{$subCat->id}}); loadSubCats({{$subCat->id}})"><i class="fa fa-plus text-info" aria-hidden="true" onclick="togglePlus(this);"></i></a></td>
                                      <td class="text-center">#{{ $subCat->id }}</td>
                                      <td class="text-center">{{ $subCat->en_name }}</td>
                                      <td class="text-center">{{ $subCat->ar_name }}</td>
                                      <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>{{ $subCat->sub_count }}</b></td>
                                      <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>{{ $subCat->orders_count }}</b></td>
                                      <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b> @readable_int($subCat->services_sales)</b></td>
                                      <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b> @readable_int($subCat->items_sales)</b></td>
                                      <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b> @readable_int($subCat->total_sales)</b></td>
                                      <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>{{ $subCat->rate_count }}</b></td>
                                      <td class="text-center {{ $category->active ? '' : 'text-warning' }}"><b>@include('admin.categories.components.stars', ['rate' => $subCat->rate_average ?: 0])</b></td>
                                      <td class="text-center"><a href="/categories/{{$category->image}}" target="_blank"><img src="/categories/{{$subCat->image}}" class="img-circle" style="height:30px; filter: invert(100%) "></a></td>
                                      <td class="text-center">
                                          @if(admin()->hasPermissionTo('Edit category'))<a title="{{ __('language.Edit') }}" href="{{ route('admin.categories.edit', $subCat->id) }}"><i class="fa fa-edit"></i></a>@endif
                                          @if(admin()->hasPermissionTo('Delete category'))<a href="#" title="{{ __('language.Delete') }}" onclick="showDeleteModal({{$subCat->id}})"class="mb-control"><i class="fa fa-trash"></i></a>@endif
                                      </td>
                                  </tr>
                                @endforeach
                                {{-- END OF SUB-CATS --}}
                            @endforeach
                            {{-- END OF CATS --}}

                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="deleteCategory">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                <div class="mb-content">
                    <p>{{ __("language.Your are about to delete a category,and you wont be able to restore its data again like providers,companies,individuals under this category.") }}</p>
                    <br/>
                    <p>{{ __('language.Are you sure?') }} </p>
                </div>
                <div class="mb-footer buttons">
                    <button onclick="$('#deleteCategory').modal('hide');" class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                    <form method="post" action="/admin/category/delete" class="buttons" id="delete-form">
                        {{csrf_field()}}
                        <input type="hidden" id="delete-cat-id" name="cat_id" value="">
                        <button type="submit" class="btn btn-danger btn-lg pull-right">{{ __('language.Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal(cat_id) {
            event.preventDefault();
            $("#delete-form #delete-cat-id").val(cat_id);
            $('#deleteCategory').modal('show');
        }

        function toggleTR(tr_name) {
          $("[name='sub_"+ tr_name +"']").toggle();

          var catState = $("[name='cat_" + tr_name + "']").attr('state');

          if (catState == 'opened') {
              $("[name='cat_" + tr_name + "']").attr('state', 'closed');
              $("[grand='" + tr_name + "']").hide();
              $("[name='sub_"+ tr_name +"'] i.fa-minus").addClass('fa-plus').removeClass('fa-minus');
          }else if (catState == 'closed') {
              $("[name='cat_" + tr_name + "']").attr('state', 'opened');
          }
        }

        function loadSubCats(id) {
            var loaded = $('#sub_' + id).attr('loaded');
            if(loaded == 'false'){
                $('#sub_' + id).attr('loaded', 'true');
                $.ajax({url: "/admin/categories/"+ id +"/html", success: function(result){
                  $('#sub_' + id).after(result);
                }});
            }
        }

        function togglePlusSquare(icon) {
            $(icon).toggleClass("fa-minus-square-o fa-plus-square");
        }

        function togglePlus(icon) {
            $(icon).toggleClass("fa-minus fa-plus");
        }
    </script>


@endsection
