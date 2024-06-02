@php
    $sorter     = isset($_GET['sort']) ? explode('.', $_GET['sort'])[0] : 'id';
    $direction  = isset($_GET['sort']) ? explode('.', $_GET['sort'])[1] : 'asc';
    $dirIcon    = $direction == 'asc' ? 'desc' : 'asc';
@endphp

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
                    <div class="panel-body">

                        <table class="gap" style="border-collapse: collapse; width: 100%; background-color:#FFFFFF;">
                            <tr style="text-align: center;font-size: 12px;">
                                <th style="font-weight: 900;background-color:#DCDCDC;padding: 5px;border: 2px solid #D3D3D3; text-align: left;">
                                    Service Category</th>
                                <th style="font-weight: 900;background-color:#DCDCDC;padding: 5px;border: 2px solid #D3D3D3;">Service Sub-Category
                                </th>
                                <th style="font-weight: 900;background-color:#DCDCDC;padding: 5px;border: 2px solid #D3D3D3;">Request Type
                                </th>
                                <th style="font-weight: 900;background-color:#DCDCDC;padding: 5px;border: 2px solid #D3D3D3;">Response Time <br> (HH:MM)
                                </th>
                                <th style="font-weight: 900;background-color:#DCDCDC;padding: 5px;border: 2px solid #D3D3D3;">Assesment Time <br>(HH:MM)
                                </th>
                                <th style="font-weight: 900;background-color:#DCDCDC;padding: 5px;border: 2px solid #D3D3D3;">Rectification Time <br> (HH:MM)
                                </th>
                                <th style="font-weight: 900;background-color:#DCDCDC;padding: 5px;border: 2px solid #D3D3D3;">Action
                                </th>
                            </tr>
                            @foreach ($slas as  $sla)

                             <input type="hidden" name="id" id="id-{{ $sla['id'] }}" value="{{ $sla['id'] }}"/>
                            <tr>
                                <td style="border: 1px solid #cfcfcf;" rowspan="1">{{  $sla['sub_cats']['parent']['en_name'] }}</td>
                                <td style="border: 1px solid #cfcfcf;" rowspan="1">{{  $sla['sub_cats']['en_name'] }}</td>
                                <td style="border: 1px solid #cfcfcf;">
                                    <div fxLayout="row " fxLayoutAlign="center">
                                        {{$sla['request_type']  }}        </div>
                                </td>
                                @php
                                $time = 24;
                                if($sla['request_type'] == 'scheduled') {
                                    $time =  1;
                                }

                                @endphp
                                <td style="border: 1px solid #cfcfcf;">

                                    <span>
                                        <select id="response-hour-{{ $sla['id'] }}" class="" name="hour" >
                                            {{-- <option value="00" disabled selected >hh</option> --}}
                                            <option value="00">00</option>
                                            @php
                                            $response_time =  explode(":",$sla['response_time']);
                                          @endphp
                                            @for($i = 1; $i <= $time; $i++)
                                            <option value="{{ $i < 10 ? '0'.$i :$i }}" {{ !empty($response_time['0']) && $response_time['0'] == $i ? 'selected': ''  }} >{{ $i < 10 ? '0'.$i :$i }}</option>
                                          @endfor
                                        </select>
                                      </span>
                                      <span>
                                        <label for="minute">:</label>
                                        <select id="response-minute-{{ $sla['id'] }}" class="" name="minute">
                                            {{-- <option value="00"disabled selected>mm</option> --}}
                                            <option value="00" >00</option>
                                            @for($i = 1; $i <= 60; $i++)
                                                <option value="{{ $i < 10 ? '0'.$i :$i }}" {{  !empty($response_time['1']) && $response_time['1'] == $i ? 'selected': ''  }}>{{ $i < 10 ? '0'.$i :$i }}</option>
                                              @endfor
                                        </select>
                                      </span>
                                </td>
                                <td style="border: 1px solid #cfcfcf;">

                                    <span>
                                        <select id="assessment-hour-{{ $sla['id'] }}" class="" name="hour">
                                            {{-- <option value="" disabled selected >hh</option> --}}
                                            <option value="00" >00</option>
                                            @php
                                              $assessment_time =  explode(":",$sla['assessment_time']);
                                            @endphp
                                            @for($i = 1; $i <= $time; $i++)
                                            <option value="{{ $i < 10 ? '0'.$i :$i }}" {{ !empty($assessment_time['0']) && $assessment_time['0'] == $i ? 'selected': ''  }}>{{ $i < 10 ? '0'.$i :$i }}</option>
                                          @endfor
                                        </select>
                                      </span>
                                      <span>
                                        <label for="minute">:</label>
                                        <select id="assessment-minute-{{ $sla['id'] }}" class="" name="minute">
                                            {{-- <option value="00"disabled selected>mm</option> --}}
                                            <option value="00">00</option>
                                            @for($i = 1; $i <= 60; $i++)
                                                <option value="{{ $i < 10 ? '0'.$i :$i }}" {{ !empty($assessment_time['1']) && $assessment_time['1'] == $i ? 'selected': ''  }}>{{ $i < 10 ? '0'.$i :$i }}</option>
                                              @endfor
                                        </select>
                                      </span>
                                </td>
                                <td style="
                                    border: 1px solid #cfcfcf;">
                                    <span>
                                        <select id="rectification-hour-{{ $sla['id'] }}" name="hour" class="">
                                            {{-- <option value="00"disabled selected>hh</option> --}}
                                            <option value="00">00</option>
                                            @php
                                              $rectification_time =  explode(":",$sla['rectification_time']);
                                            @endphp
                                            @for($i = 1; $i <= $time; $i++)
                                            <option value="{{ $i < 10 ? '0'.$i :$i }}" {{ !empty($rectification_time['0']) && $rectification_time['0'] == $i ? 'selected': ''  }}>{{ $i < 10 ? '0'.$i :$i }}</option>
                                          @endfor
                                        </select>
                                      </span>
                                      <span>
                                        <label for="minute">:</label>
                                        <select id="rectification-minute-{{ $sla['id'] }}" name="minute" class="">
                                            {{-- <option value="00"disabled selected>mm</option> --}}
                                            <option value="00">00</option>
                                            @for($i = 1; $i <= 60; $i++)
                                                <option value="{{ $i < 10 ? '0'.$i :$i }}" {{ !empty($rectification_time['1']) && $rectification_time['1'] == $i ? 'selected': ''  }}>{{ $i < 10 ? '0'.$i :$i }}</option>
                                              @endfor
                                        </select>
                                      </span>                                       </td>
                                      <td style="
                                      border: 1px solid #cfcfcf;">
                                        <button type="button" class="btn btn-primary"  onclick="updateSlaRow({{ $sla['id'] }});">Save</button>
                                      </td>
                            </tr>
                            @endforeach
                          </table>
                        </div>
                </div>
            </div>
        </div>
    </div>
    @push('custom-scripts')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
     function updateSlaRow(id) {
        $('.ajax-alert-message').hide();
         var Cid =  $('#id-'+id).val();
         var response_hour =  $('#response-hour-'+id).val();
         var response_minute =  $('#response-minute-'+id).val();
         var assessment_hour =  $('#assessment-hour-'+id).val();
         var assessment_minute =  $('#assessment-minute-'+id).val();
         var rectification_hour =  $('#rectification-hour-'+id).val();
         var rectification_minute =  $('#rectification-minute-'+id).val();
        $.ajax({
                url: "{{ url('company/sla/update') }}",
                type: 'POST',
                cache: false,
                data: {
                    'id' : id,
                    'response_hour': response_hour,
                    'response_minute': response_minute,
                    'assessment_hour': assessment_hour,
                    'assessment_minute': assessment_minute,
                    'rectification_hour': rectification_hour,
                    'rectification_minute': rectification_minute,
                },
                dataType: "json",
                success: function(data) {
                     $('.ajax-alert-message').show();
                     $('.ajax-alert-message').html('<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> SLA successfully updated!');

                },
                error: function(xhr,textStatus,thrownError) {
                     $('.ajax-alert-message').show();
                     $('.ajax-alert-message').html('<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' + xhr + "\n" + textStatus + "\n" + thrownError);
                    alert(xhr + "\n" + textStatus + "\n" + thrownError);
                }
            });
       }
    </script>
    @endpush
@endsection


