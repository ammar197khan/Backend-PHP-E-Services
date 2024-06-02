@extends('provider.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/provider/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li><a href="/provider/collaborations">{{ __('language.Collaborations') }}</a></li>
        <li class="active">Update Services Fees</li>
    </ul>
    <!-- END BREADCRUMB -->
    @include('provider.layouts.message')
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="/provider/collaboration/third/fees/update" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Update Services Fees
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                @foreach($categories as $grandParent => $categories)
                                    <div class="col-md-12" >
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <label class="switch" >
                                                        <h3 class="panel-title" style="float: left;">{{$grandParent}}</h3>
                                                        <input type="hidden" value="{{$company_id}}" name="company_id" />
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                @foreach($categories->groupBy('parentName') as $parentName => $categories)
                                                  <div class="col-md-12">

                                                    <label class="switch">
                                                      <h3 class="panel-title col-md-12" style="float: left;background-color: #1F4661; color: #fff;
                                                      padding: 7px; border-radius: 5px;margin-bottom: 10px;margin-top: 10px">{{ $parentName }}</h3>
                                                    </label>
                                                    <ul class="list-group border-bottom">
                                                      @foreach($categories as $category)
                                                        <div class="col-md-12">
                                                          <div class="col-md-8" style="margin-bottom: 3px;">
                                                            <li class="list-group-item">
                                                              {{$category->en_name}}
                                                            </li>
                                                          </div>
                                                          <div class="col-md-4">
                                                            <input class="form-control" type="number" name="fees[{{$category->id}}]" value="{{ $category->third_fee }}" style="width: 80px;" required>
                                                          </div>
                                                        </div>
                                                      @endforeach
                                                    </ul>
                                                  </div>

                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <div class="panel-footer">
                                <button type="reset" class="btn btn-default">{{ __('language.Reset') }}</button> &nbsp;
                                <button class="btn btn-primary pull-right">
                                    Update
                                </button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
