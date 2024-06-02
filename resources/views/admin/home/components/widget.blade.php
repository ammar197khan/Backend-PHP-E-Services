<div class="widget widget-default widget-item-icon">
    <div class="widget-item-left">
        <span class="fa fa-{{ $icon }}"></span>
    </div>
    <div class="widget-data">
        <div class="widget-int" style="font-size: 17px">
            {{$count}} {{ $slot }}
        </div>
        <div class="widget-int" style="font-size: 17px">
            {{ $count_year }} {{ $subTitle }}
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

