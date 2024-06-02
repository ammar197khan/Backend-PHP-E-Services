@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li>{{ __('language.House Type') }}s</li>
    </ul>

    <!-- END BREADCRUMB -->
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('company.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="{{route('company.house_types.create')}}"><button type="button" class="btn btn-info"><i class="fa fa-plus"></i> {{ __('language.New House Type') }} </button></a>
                    </div>

                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('language.Arabic Name') }}</th>
                                    <th>{{ __('language.English Name') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($house_types as $house_type)
                                    <tr>
                                        <td>{{$house_type->id}}</td>
                                        <td>{{$house_type->ar_name}}</td>
                                        <td>{{$house_type->en_name}}</td>
                                        <td>
                                            <a title="Edit" href="{{route('company.house_types.edit',$house_type->id)}}"><button class="btn btn-warning btn-condensed"><i class="fa fa-edit"></i></button></a>
                                            <button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-delete-{{$house_type->id}}" title="Delete"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-delete-{{$house_type->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>Your are about to delete house type .</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                    <form method="post" action="{{route('company.house_types.destroy',$house_type->id)}}" class="buttons">
                                                        {{csrf_field()}}
                                                        {{method_field('DELETE')}}
                                                        <button type="submit" class="btn btn-danger btn-lg pull-right">{{ __('language.Delete') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->

                                @endforeach
                                </tbody>
                            </table>
                            {{$house_types->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
