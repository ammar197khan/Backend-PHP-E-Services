@php
    $rate = (int)substr($count,0);
    $grey_star = "<i style='font-size:15px' class='fa fa-star-o' aria-hidden='true'></i>";
    $gold_star = "<i style='color: #ffa800;font-size: 15px' class='fa fa-star' aria-hidden='true'></i>";

    $data = '';
    for ($i =0; $i < $rate; $i++){
        $data = $data . $gold_star;
    }
    for ($i =0; $i < 5-$rate; $i++){
        $data = $data . $grey_star;
    }
$count_month_orders = (int)substr($count_month_orders,0);
$count_year_orders = (int)substr($count_year_orders,0);
@endphp
<div class="widget widget-default widget-item-icon">
    <div class="widget-item-left">
        <span class="fa fa-{{ $icon }}"></span>
    </div>
    <div class="widget-data">
        <div style="font-size: 17px">
            <b>{{$slot}}</b>
        </div>
        <div class="widget-int" style="font-size: 17px">
            @include('admin.home.components.stars', ['rate' => (int)substr($count,0)])
            <small style="font-size:12px">
              ({{ $count_month_orders == 0 ||  $count_month_orders == 1 ? $count_month_orders.' Order' : $count_month_orders.' Orders' }})
            </small>
            MTD
        </div>
        <div class="widget-int" style="font-size: 17px">
            @include('admin.home.components.stars', ['rate' => (int)substr($count_year,0)])
            <small style="font-size:12px">
              ({{ $count_year_orders == 0 ||  $count_year_orders == 1 ? $count_year_orders.' Order ' : $count_year_orders.' Orders ' }})
            </small>
            {{ $subTitle }}
        </div>
    </div>
</div>



{{--<div class="row panel" style="padding: 25px 0 25px 25px">--}}
{{--    <div class="col-md-3"><span class="fa fa-{{ $icon }}" style="font-size: 30px"></span></div>--}}
{{--    <div class="col-md-9">--}}
{{--        <div><span  style="font-size: 15px">{{ $count }}</span> <b>{{ $slot }}</b></div>--}}
{{--        <div><span  style="font-size: 15px">{{ $count_year }}</span> <b>{{ $subTitle }}</b></div>--}}

{{--    </div>--}}

{{--</div>--}}
