

<style>
	canvas {
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
</style>

<div style="width:100%">
	<canvas id="chart1"></canvas>
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

	var data = {!! json_encode($data) !!};

	var ctx = document.getElementById('chart1').getContext('2d');
	ctx.canvas.width = 1000;
	ctx.canvas.height = 500;

	var color = Chart.helpers.color;
	var cfg = {
		type: 'bar',
		data: {
			datasets: [{
				@if( strpos($_SERVER['REQUEST_URI'],'company') )
				label: "{{ __('language.Monthly Cost') }}",
				@else
				label: "{{ __('language.Monthly Sales') }}",
				@endif
				backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
				borderColor: window.chartColors.blue,
				data: data,
				type: 'line',
				pointRadius: 0,
				fill: false,
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
						display: true,
						labelString: 'Sales (SAR)'
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

	var chart = new Chart(ctx, cfg);

	// document.getElementById('update').addEventListener('click', function() {
	// 	var type = document.getElementById('type').value;
	// 	chart.config.data.datasets[0].type = type;
	// 	chart.update();
	// });
</script>
