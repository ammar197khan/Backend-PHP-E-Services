@extends('company.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/company/dashboard">{{ __('language.Dashboard') }}</a></li>
        <li class="active">{{ __('language.Item Requests') }}</li>
    </ul>
    <!-- END BREADCRUMB -->

    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
            @include('provider.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                     <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('language.User') }}</th>
                                    <th>{{ __('language.Order Description') }}</th>
                                    <th>{{ __('language.Provider') }}</th>
                                    <th>{{ __('language.Item') }}</th>
                                    <th>{{ __('language.Image') }}</th>
                                    <th>{{ __('language.Price') }}</th>
                                    <th>{{ __('language.Operations') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td>{{$item->user->en_name}}</td>
                                        <td>{{$item->provider->en_name}}</td>
                                        <td>{{$item->order->desc}}</td>
                                        <td>{{$item->en_name}}</td>
                                        <td>{{$item->item($item->provider_id,$item_id)->image}}</td>
                                        <td>
                                            <img src="/warehouses/{{$item->image}}" class="image"/>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-success-{{$item->id}}" title="Confirm"><i class="fa fa-check-circle"></i></button>
                                            <button class="btn btn-danger btn-condensed mb-control" data-box="#message-box-warning-{{$item->id}}" title="Decline"><i class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-success animated fadeIn" data-sound="alert/fail" id="message-box-success-{{$item->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to approve an item request') }}.</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/company/item/change_status" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="item_id" value="{{$item->item_id}}">
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="btn btn-success btn-lg pull-right">{{ __('language.Confirm') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->

                                    <!-- danger with sound -->
                                    <div class="message-box message-box-danger animated fadeIn" data-sound="alert/fail" id="message-box-warning-{{$item->id}}">
                                        <div class="mb-container">
                                            <div class="mb-middle warning-msg alert-msg">
                                                <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                                <div class="mb-content">
                                                    <p>{{ __('language.Your are about to decline an item request') }}.</p>
                                                    <br/>
                                                    <p>{{ __('language.Are you sure?') }}</p>
                                                </div>
                                                <div class="mb-footer buttons">
                                                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">{{ __('language.Close') }}</button>
                                                    <form method="post" action="/company/item/change_status" class="buttons">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="item_id" value="{{$item->item_id}}">
                                                        <input type="hidden" name="status" value="decline">
                                                        <button type="submit" class="btn btn-danger btn-lg pull-right">{{ __('language.Decline') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end danger with sound -->
                                @endforeach

                                </tbody>
                            </table>


                            {{$items->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
