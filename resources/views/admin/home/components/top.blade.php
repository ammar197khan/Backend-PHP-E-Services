<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
          @if(isset($link))
            <a href="{!! $link !!}">
              <i class="fa fa-external-link" aria-hidden="true"></i>
              {{ $title }}
            </a>
          @else
            {{ $title }}
          @endif
        </h3>
        <ul class="panel-controls">
            {{-- <li><span class="fa fa-star fa-2x"></span></li> --}}

        </ul>
        <div class="pull-right">
            <a href="#top_asc_{{$title}}" aria-controls="profile" role="tab" data-toggle="tab" title="Ascending"><i class="fa fa-arrow-up"></i></a>
            <a href="#top_desc_{{$title}}" aria-controls="profile" role="tab" data-toggle="tab" title="descending"><i class="fa fa-arrow-down"></i></a>
        </div>
    </div>
    <div class="panel-body">
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="top_asc_{{$title}}">
                @foreach ($data as $key => $value)
                    <div>
                        @php
                            $name = explode(' ', $key);
                            $count = strlen($key)
                        @endphp
                        <h6 class="col-md-8">{{ $name[0] }} {{ optional($name)[1] }} {{ $count < 22 ? optional($name)[2] : '' }}</h6>
                        <div class="col-md-4"><i class="fa fa-money" aria-hidden="true"></i> {{ $value }}</div>
                    </div>
                @endforeach
                @for ($i=0; $i < (4 - count($data)); $i++)
                    <div>
                        <h6 class="col-md-9">-</h6>
                        <div class="col-md-3"></div>
                    </div>
                @endfor
            </div>


            <div role="tabpanel" class="tab-pane" id="top_desc_{{$title}}">
                @foreach ($data_desc as $key => $value)
                    <div>
                        @php
                            $name = explode(' ', $key);
                            $count = strlen($key)
                        @endphp
                        <h6 class="col-md-8">{{ $name[0] }} {{ optional($name)[1] }} {{ $count < 22 ? optional($name)[2] : '' }}</h6>
                        <div class="col-md-4"><i class="fa fa-money" aria-hidden="true"></i> {{ $value }}</div>
                    </div>
                @endforeach
                @for ($i=0; $i < (4 - count($data_desc)); $i++)
                    <div>
                        <h6 class="col-md-9">-</h6>
                        <div class="col-md-3"></div>
                    </div>
                @endfor
            </div>
        </div>

    </div>
</div>
