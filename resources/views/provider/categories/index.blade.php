@php
    $sorter     = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
    $direction  = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
    $dirIcon    = $direction == 'asc' ? 'desc' : 'asc';
@endphp
@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{ __('language.Categories') }}</li>
    </ul>

    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div style="height:auto" class="panel panel-default">

                    <a href="/provider/categories" class="btnprn pull-right" style="font-size: 20px; padding-right: 10px; text-decoration:none"> <i class="fa fa-print"></i> {{ __('language.PRINT') }}</a>
                    <script>
                        $(document).ready(function () {
                            $('.btnprn').printPage();
                        });
                    </script>
                    <form class="form-horizontal" method="get" action="/provider/categories/search">
                        <div class="form-group">
                            <div class="col-md-6 col-xs-12">
                                {{-- <div class="input-group" style="margin-top: 10px;">
                                    <input type="text" class="form-control" name="search" value="{{isset($search) ? $search : ''}}" placeholder="Search by name" style="margin-top: 1px;"/>
                                    <span class="input-group-addon btn btn-default">
                                            <button class="btn btn-default">Search now</button>
                                    </span>
                                </div> --}}
                            </div>
                        </div>
                    </form>
                    <div class="panel-body" id="cats">

                        <table class="table-condensed">
                            <thead>
                            <tr style="border-bottom-style:solid; border-width:thin; border-color: #bfbfbf; background-color:default;">
                                <td></td>
                                <td><a href="?sort=id.{{$sorter == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'id' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>ID</b></a></td>
                                <td><a href="?sort=en_name.{{$sorter == 'en_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'en_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.English Name') }}</b></a></td>
                                <td><a href="?sort=ar_name.{{$sorter == 'ar_name' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'ar_name' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Arabic Name') }}</b></a></td>
                                <td><a href="?sort=sub_count.{{$sorter == 'sub_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'sub_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Sub Categories') }}</b></a></td>
                                <td><a href="?sort=orders_count.{{$sorter == 'orders_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'orders_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Orders Count') }}</b></a></td>
                                <td><a href="?sort=services_sales.{{$sorter == 'services_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'services_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Services Sales') }}</b></a></td>
                                <td><a href="?sort=items_sales.{{$sorter == 'items_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'items_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Items Sales') }}</b></a></td>
                                <td><a href="?sort=total_sales.{{$sorter == 'total_sales' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'total_sales' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Total Sales') }}</b></a></td>
                                <td><a href="?sort=rate_count.{{$sorter == 'rate_count' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_count' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Rate Count') }}</b></a></td>
                                <td><a href="?sort=rate_average.{{$sorter == 'rate_average' && $direction == 'asc' ? 'desc' : 'asc' }}" style="text-decoration:none"><i class="fa fa-sort{{ $sorter == 'rate_average' ? '-'.$dirIcon : '' }}" aria-hidden="true"></i> <b>{{ __('language.Rate Average') }}</b></a></td>
                                <td><b>Image</b></td>
                            </tr>
                            </thead>
                            {{-- <tbody> --}}
                            @foreach ($categories as $category)
                                <tr style="border-style: solid none solid none; border-color: #bfbfbf; border-width:thin;" name="cat_{{$category->id}}" state="closed">
                                    <td class="" style="text-align:left"><a href="#" onclick="toggleTR({{$category->id}});"><i class="fa fa-plus-square fa-lg text-info" aria-hidden="true" onclick="togglePlusSquare(this);"></i></a></td>
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
                                </tr>

                                {{-- START OF SUB-CATS --}}
                                @foreach ($category->sub_categories as $subCat)
                                    <tr name="sub_{{$category->id}}" id="sub_{{$subCat->id}}" style="display:none" loaded="false">
                                        <td></td>
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

    <script>
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

        function togglePlusSquare(icon) {
            $(icon).toggleClass("fa-minus-square-o fa-plus-square");
        }

    </script>

@endsection
