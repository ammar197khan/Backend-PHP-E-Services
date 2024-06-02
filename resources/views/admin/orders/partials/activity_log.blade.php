@php
  $adminType = [
      'App\Models\CompanyAdmin'  => 'Company',
      'App\Models\ProviderAdmin' => 'Provider',
  ];
@endphp
<button type="button" class="col-md-5 btnprn pull-right btn btn-primary" data-toggle="modal" data-target="#activityLog" style="font-size: 15px; margin:0px 3px 0px 3px">
  <i class="fa fa-history" style="font-size: 15px"></i>
  {{ __('language.History') }}
</button>
<div id="activityLog" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>{{ __('language.Activity Log History') }}</b></h4>
      </div>
      <div class="modal-body">
        @if(count($history_log) == 0)
            No Records
        @else
            <div class="table-responsive">
              <table class="table">
                @foreach ($history_log as $log)
                  <tr>
                    <td style="text-align: left">
                      <i class="fa fa-history" style="margin-right:8px"></i>
                      {{ $log->description}}
                    </td>
                    <td style="text-align: left">
                      @if(active_guard() == 'admin')
                        {{ $log->causer->name }} ({{ $adminType[$log->causer_type] }})
                      @elseif (active_guard() == 'company' && $adminType[$log->causer_type] == 'Company')
                        <a href="{{ url('company/admins/'.$log->causer_id.'/view') }}" style="text-decoration:none; color: blue">
                          {{ $log->causer->name }}
                        </a>
                      @elseif (active_guard() == 'provider' && $adminType[$log->causer_type] == 'Provider')
                        <a href="{{ url('provider/admins/'.$log->causer_id.'/view') }}" style="text-decoration:none; color: blue">
                          {{ $log->causer->name }}
                        </a>
                      @elseif (active_guard() == 'company' && $adminType[$log->causer_type] == 'Provider')
                        Service Provider
                      @elseif (active_guard() == 'provider' && $adminType[$log->causer_type] == 'Company')
                        Company
                      @endif
                    </td>
                    <td style="text-align: left">
                      {{ $log->created_at->diffForHumans() }}
                    </td>
                  </tr>
                @endforeach
              </table>
            </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
