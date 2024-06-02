<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
</style>

<canvas id="bars"></canvas>


<script>
    var color = Chart.helpers.color;
    var barChartData = {
        labels: {!! json_encode($data[0]) !!},
        datasets: [{
            label: '{{ strpos($_SERVER['REQUEST_URI'],'company') ? __("language.Monthly Cost")  :  __("language.Monthly Sales") }}',
            backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
            borderColor: window.chartColors.blue,
            borderWidth: 1,
            data: {!! json_encode($data[1]) !!}
        }]
		};

    window.onload = function () {
        var ctx = document.getElementById('bars').getContext('2d');
        ctx.canvas.width = 1000;
      	// ctx.canvas.height = 500;
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
            }
        });

    };
</script>
