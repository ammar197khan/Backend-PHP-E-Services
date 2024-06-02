@extends('admin.layouts.app')
@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">Home</a></li>
        <li>{{ __('language.Application Settings') }}</li>
        <li class="active">Complain and Suggestions</li>
    </ul>
    <!-- END BREADCRUMB -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12  col-xs-12">
            @include('admin.layouts.message')
            <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-body" style="overflow: auto;">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="rtl_th">{{ __('language.Title') }}</th>
                                    <th class="rtl_th">{{ __('language.Username') }}</th>
                                    <th class="rtl_th">{{ __('language.Email') }}</th>
                                    <th class="rtl_th">{{ __('language.Phone') }}</th>
                                    <th class="rtl_th">{{ __('language.Description') }}</th>
                                    <th class="rtl_th">{{ __('language.Date') }}</th>
                                    <th class="rtl_th">Operation</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($complains as $complain)
                                <tr>
                                    <td>
                                        {!! $complain->title->en_title !!}
                                    </td>
                                    <td>
                                        {!! isset($complain->user->en_name) ? $complain->user->en_name : '-'!!}
                                    </td>
                                    <td>
                                        {!! isset($complain->user->email) ? $complain->user->email : '-'!!}
                                    </td>
                                    <td>
                                        {!! isset($complain->user->phone) ? $complain->user->phone : '-'!!}
                                    </td>
                                    <td>
                                        {!! $complain->desc !!}
                                    </td>
                                    <td>
                                        {!! $complain->created_at->diffForHumans() !!}
                                    </td>
                                    @if(admin()->hasPermissionTo('Edit settings'))
                                        <td>
                                            <button class="btn btn-primary btn-condensed mb-control" data-box="#message-box-decline-{{$complain->id}}" title="Answer"><i class="fa fa-comment"></i></button>
                                        </td>
                                    @endif
                                </tr>

                                <div class="message-box message-box-primary animated fadeIn" data-sound="alert/fail" id="message-box-decline-{{$complain->id}}">
                                    <div class="mb-container">
                                        <div class="mb-middle warning-msg alert-msg">
                                            <div class="mb-title"><span class="fa fa-times"></span>{{ __('language.Alert') }} !</div>
                                            <div class="mb-content">
                                                <p>Your are about to decline an item request .</p>
                                                <br/>
                                                <p>{{ __('language.Are you sure?') }}</p>
                                            </div>
                                            <div class="mb-footer buttons">
                                                <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-left: 5px;">Close</button>
                                                <form method="post" action="/admin/settings/complains" class="buttons">
                                                    {{csrf_field()}}
                                                    <textarea class="col-md-8" rows="10" name="get_problem" style="margin-top: 5px;color: black" placeholder="{{ __('language.Send') }} Response" required></textarea>
                                                    <input type="hidden" name="complain_id" value="{{$complain->id}}">
                                                    <button type="submit" class="btn btn-primary btn-lg pull-right">Send</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
