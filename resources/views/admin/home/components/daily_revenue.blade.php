{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script src="{{ asset("admin/js/Chart.min.js") }}"></script>
<script src="{{ asset("admin/js/utils.js") }}"></script>

<style>
	canvas {
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
</style> --}}

<div style="width:100%">
	<canvas id="chart2"></canvas>
</div>

<script type="text/javascript">
function randomNumber(min, max) {
		return Math.random() * (max - min) + min;
	}

	function randomBar(date, lastClose) {
		var open = randomNumber(lastClose * 0.95, lastClose * 1.05).toFixed(2);
		var close = randomNumber(open * 0.95, open * 1.05).toFixed(2);
		return {
			t: date.valueOf(),
			y: close
		};
	}

	var dailyData = {!! json_encode($data) !!};

	var ctx = document.getElementById('chart2').getContext('2d');
	ctx.canvas.width = 1000;
	ctx.canvas.height = 500;

	var color = Chart.helpers.color;
	var cfg = {
		type: 'bar',
		data: {
			datasets: [{
				@if( strpos($_SERVER['REQUEST_URI'],'company') )
				label: "{{ __('language.Daily Cost') }}",
				@else
				label: "{{ __('language.Daily Sales') }}",
				@endif
				backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
				borderColor: window.chartColors.blue,
				data: dailyData,
				type: 'line',
				pointRadius: 0,
				fill: true,
				lineTension: 0,
				borderWidth: 2
			}]
		},
		options: {
			scales: {
				xAxes: [{
					type: 'time',
					distribution: 'series',
					ticks: {
						source: 'data',
						autoSkip: true
					}
				}],
				yAxes: [{
					scaleLabel: {
						display: false,
						labelString: 'Sales'
					}
				}]
			},
			tooltips: {
				intersect: false,
				mode: 'index',
				callbacks: {
					label: function(tooltipItem, myData) {
						var label = myData.datasets[tooltipItem.datasetIndex].label || '';
						if (label) {
							label += ': ';
						}
						label += parseFloat(tooltipItem.value).toFixed(2);
						return label;
					}
				}
			}
		}
	};

	window.chart = new Chart(ctx, cfg);

</script>
