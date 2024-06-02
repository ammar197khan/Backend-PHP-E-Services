@php

    $sorter     = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
    $direction  = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
    $dirIcon    = $direction == 'asc' ? 'desc' : 'asc';
@endphp
<style>

                          .tab-sla{
                            display: flex;
                            align-items: center;
                              justify-content: start;
                              padding: 10px;
                              height: 35px;
                              border-radius: 5px;
                              width: 17%;
                              box-shadow: 2px 2px 2px 2px #888888;
                              /* font-size: 12px; */
                              color: #3C4E5E;
                          }
                          .tab-sla-nmbr{
                            margin-left: 10px;
                            padding-right: 10px;
                            border-right: 1px solid #D3D3D3
                          }
                          .classWithShadow{
                            background-image: none;
                            outline: 0;
                            box-shadow: none;
                          }

</style>
@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>SLA</li>

    </ul>



    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <form class="form-horizontal" method="get" action="/company/sla/filter-order-dashboard">
                        @include('company.sla.search')
                    </form>
                    <div class="panel-body">
                        @include('company.sla.statistics')
                        <div class="table-responsive">
                            <table class="table" style="    font-size: 12px !important; white-space: nowrap;!important">
                                @include('company.sla.table-head')
                                <tbody>
                                @foreach($data as $dt)
                                    <tr>
                                        <td>{{$dt['id']}}</td>
                                        <td>{{ \Carbon\Carbon::parse($dt['date'])->format('d-m-Y')}}</td>
                                        <td>{{$dt['type']}}</td>
                                        <td>
                                            {{$dt['cat_id']}}
                                        </td>
                                        <td>{{ $dt['sub_cat_id'] }}</td>
                                        {{-- <td>{{ $dt['desc'] }}</td> --}}
                                        <td>{{ $dt['response_time'] }}</td>
                                        <td>{{ $dt['assessment_time'] }}</td>
                                        <td>{{ $dt['rectification_time'] }}</td>
                                        <td>{{ $dt['breach_response_time'] }}</td>
                                        <td>{{ $dt['breach_assessment_time'] }}</td>
                                        <td>{{ $dt['breach_rectification_time'] }}</td>
                                        <td>
                                            @if(company()->hasPermissionTo('View details order'))
                                                <a title="View" href="/company/order/{{$dt['id']}}/view"><button class="btn btn-info btn-condensed"><i class="fa fa-eye"></i></button></a>
                                            @endif
                                            {{--<button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$order->id}}" title="Delete"><i class="fa fa-trash-o"></i></button>--}}
                                        </td>
                                    </tr>

                                    <!-- danger with sound -->

                                    <!-- end danger with sound -->

                                    <!-- danger with sound -->

                                    <!-- end danger with sound -->

                                @endforeach
                                </tbody>
                            </table>
                            {{ $data->appends(request()->input())->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    $('#main_cats').on('change', function (e) {
            var parent_id = $('#main_cats').val();
            if (parent_id) {
                $.ajax({
                    url: '/company/get_sub_cat/'+parent_id,
                    type: "GET",
                    dataType: "json",

                    success: function (data) {
                        $('#sub_cats').empty();
                        $('#sub_cats').append('<option selected disabled> Select a Sub Category </option>');
                        $.each(data, function (i, sub_cat) {
                            debugger;
                            var selected = [{{  request('sub_cats') }}]
                            var includes = selected

                            if(includes == true)
                                $('#sub_cats').append('<option value="' + sub_cat.id + '" selected>' + sub_cat.en_name + '</option>');
                            else
                                $('#sub_cats').append('<option value="' + sub_cat.en_name + '">' + sub_cat.en_name + '</option>');
                        });
                    }
                });

            }
        });
        $(function() {

$(".tab-sla").click(function() {
    $(this).addClass("classWithShadow");
    let view = this.getAttribute('data-value');
    window.location='{{ url("company/sla/order-dashboard") }}?view='+view;
});
});
</script>
@endsection
