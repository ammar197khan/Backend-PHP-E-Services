@php

    $grey_star = "<i style='font-size:15px' class='fa fa-star-o' aria-hidden='true'></i>";
    $gold_star = "<i style='color: #ffa800;font-size: 15px' class='fa fa-star' aria-hidden='true'></i>";

    $data = '';
    for ($i =0; $i < $rate; $i++){
        $data = $data . $gold_star;
    }
    for ($i =0; $i < 5-$rate; $i++){
        $data = $data . $grey_star;
    }

@endphp

{!!  $data  !!}
