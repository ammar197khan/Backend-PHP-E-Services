@php
  $subTotal = 0;
  if($provider->type == 'cash'){
      $subTotal = $provider->commissionValue($data['total_count']);
  } elseif ($provider->type == 'percentage') {
      $subTotal = $provider->commissionValue($data['total_count']) / 100 * $data['total_orders_amount'];
  } elseif ($provider->type == 'categorized') {
      $subTotal = $provider->commissionValue($data['total_count']) * $data['total_count'];
  }

@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ __('language.INVOICE') }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet"> --}}
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

  <style media="screen">
    .container {
      width:148mm;
      /* height:200mm; */
      background-color:#fcfcfc;
      padding:0px;
    }
    .header{
      background-color:#2E82DD;
      vertical-align: middle;
    }
    .upper{
      padding: 20px;
    }
    h1 {
      color: #fff;
      font-size: 70px;
      text-align: center;
      vertical-align: middle;
      font-weight: bold;
    }

  </style>
</head>
<body>

  <div class="container">
      <div class="header">
        <br>
        <br>
        <h1>{{ __('language.INVOICE') }}</h1>
        <br>
        <br>
      </div>
      <div class="upper">
        <div class="row">
          <div class="col-md-3 col-xs-3" style="color:grey">{{ __('language.Billed To') }} :</div>
          <div class="">{{ $provider->en_name }}</div>
        </div>
        <div class="row">
          <div class="col-md-3 col-xs-3" style="color:grey">{{ __('language.Billed From') }} :</div>
          <div class="">{{ __('language.Qreeb') }}</div>
        </div>
        <div class="row">
          <div class="col-md-3 col-xs-3" style="color:grey">{{ __('language.From') }} :</div>
          <div class="">{{ $from?: 'First Order' }}</div>
        </div>
        <div class="row">
          <div class="col-md-3 col-xs-3" style="color:grey">{{ __('language.To') }} :</div>
          <div class="">{{ $to ?: date('d M Y')}}</div>
        </div>
        <br>
        <br>
        <table class="table">
            <tr>
              <th colspan="2">{{ __('language.Work Performance Information') }}</th>
              <th></th>
              <th>{{ __('language.QTY') }}</th>
            </tr>
            @foreach ($data['elements'] as $element)
              <tr>
                <td rowspan="3">{{$element->service}}</td>
                <td>{{ __('language.Urgent Callout') }}</td>
                <td></td>
                <td>{{ $element->urgent_count }}</td>
              </tr>
              <tr>
                <td>{{ __('language.Scheduled Callout') }}</td>
                <td></td>
                <td>{{ $element->scheduled_count }}</td>
              </tr>
              <tr>
                <td>{{ __('language.ReScheduled ') }}</td>
                <td></td>
                <td>{{ $element->rescheduled_count }}</td>
              </tr>
            @endforeach
            <tr>
              <th colspan="3">{{ __('language.Total Orders Count') }}</th>
              <th>{{ $data['total_count'] }} {{ __('language.Order') }}</th>
            </tr>
            <tr>
              <th colspan="3">{{ __('language.Total Orders Amount') }}</th>
              <th>{{ $data['total_orders_amount'] }} {{ __('language.SAR') }}</th>
            </tr>
            <tr>
              <th colspan="3">{{ __('language.Commission') }}</th>
              <th>
                  @if($provider->type == 'cash')
                    {{ $provider->interest_fee }} {{ __('language.SAR/Month') }}
                  @elseif ($provider->type == 'percentage')
                    {{ $provider->interest_fee }}{{ __('language.% of Total Transactions') }}
                  @elseif ($provider->type == 'categorized')
                    {{ $provider->commissionValue($data['total_count']) }} {{ __('language.SAR/Transaction') }}
                  @endif
              </th>
            </tr>
            <tr>
              <th colspan="3">{{ __('language.Subtotal') }}</th>
              <th> {{ $subTotal }} {{ __('language.SAR') }}</th>
            </tr>
            <tr>
              <th colspan="3">{{ __('language.VAT') }}</th>
              <th>{{ $data['orders_vat'] }} %</th>
            </tr>
            <tr>
              <th colspan="4">{{ __('language.Total Due Commission') }}</th>
            </tr>
        </table>
        <div class="pull-right" style="font-size:50px; color:#2E82DD"><b>{{ $subTotal * $data['orders_vat'] / 100 + $subTotal }} SAR</b></div>
      </div>
  </div>
</body>
</html>
