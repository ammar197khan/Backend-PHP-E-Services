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
          <div class="col-md-3 col-xs-3" style="color:grey"> {{ __('language.Billed From') }}:</div>
          <div class="">{{ $company->en_name }}</div>
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
              <th>{{ __('language.QTY') }}</th>
              <th>{{ __('language.Rate Count') }}</th>
            </tr>
            @foreach ($data['elements'] as $element)

              <tr>
                <td rowspan="3">{{$element->service}}</td>
                <td>{{ __('language.Urgent Callout') }}</td>
                <td>{{ $element->urgent_count }}</td>
                <td>{{ $element->urgent_orders_amount }}</td>
              </tr>
              <tr>
                <td>{{ __('language.Scheduled Callout') }}</td>
                <td>{{ $element->scheduled_count }}</td>
                <td>{{ $element->scheduled_orders_amount }}</td>
              </tr>
              <tr>
                <td>Re{{ __('language.Scheduled Callout') }}</td>
                <td>{{ $element->rescheduled_count }}</td>
                <td>{{ $element->rescheduled_orders_amount }}</td>
              </tr>
            @endforeach
            <tr>
              <th colspan="3">{{ __('language.Subtotal') }}</th>
              <th>{{ $data['total_orders_amount'] }} SAR</th>
            </tr>
            <tr>
              <th colspan="3">{{ __('language.VAT') }}</th>
              <th>{{ $data['orders_vat'] }} %</th>
            </tr>
            <tr>
              <th colspan="4">{{ __('language.Total Due Commission') }}</th>
            </tr>
        </table>
        <div class="pull-right" style="font-size:50px; color:#2E82DD"><b>{{ $data['total_orders_amount'] + $data['total_orders_amount'] *  $data['orders_vat'] /100 }} SAR</b></div>
      </div>
  </div>
</body>
</html>
