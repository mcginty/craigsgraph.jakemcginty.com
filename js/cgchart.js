var dict = {};
var pmin = 0;
var pmax = 0;
function requestData() {
    $.ajax({
        url: (pmin > 0 || pmax > 0) ?  : 'results.json.php?q=<?php echo $_GET['q']; if (isset($_GET['pmin'])&&isset($_GET['pmax'])) echo "&pmin={$_GET['pmin']}&pmax={$_GET['pmax']}"; ?>',
        success: function(points) {
            var series = chart.series[0];

            // add the point
            chart.series[0].setData(eval(points), true);
        },
        cache: false
    });
}
var chart;
$(document).ready(function() {

	// define the options
	var options = {
		chart: {
			renderTo: 'graph-container',
			zoomType: 'x',
			defaultSeriesType: 'area',
			events: {
				load: requestData
			}
		},
		title: {
			enabled: false,
			text: ''
		},
		subtitle: {
			enabled: false,
			text: ''
		},
		xAxis: {
			title: {
				text: 'Date/Time'
			}
		},
		yAxis: {
			title: {
				text: 'Price'
			},
			labels: {
				formatter: function() {
					return '$' + this.value;
				}
			}
		},
		legend: {
			enabled: false
		},
		tooltip: {
			formatter: function() {
					var date = new Date(this.x*1000);
	                var hours = date.getHours();
	                var minutes = date.getMinutes();
	                var month = date.getMonth();
	                var day = date.getDay();
	                var year = date.getYear();

	                return '<b>'+ this.series.name +'</b><br/>'+
					month+'/'+day+' '+ hours+':'+minutes +' - $'+ this.y;
			}
		},
		plotOptions: {
			area: {
				cursor: 'pointer',
				point: {
					events: {
						click: function() {
							hs.htmlExpand(null, {
								pageOrigin: {
									x: this.pageX, 
									y: this.pageY
								},
								headingText: this.series.name,
								maincontentText: 'this.category: '+ this.category +
									'<br/>this.y: '+ this.y,
								width: 200
							});
						}
					}
				},
				states: {
					hover: {
						enabled: false,
						lineWidth: 1
					}
				},
				lineWidth: 0,
				shadow: false
			}
		},
		series: [{
			name: 'Craigslist Results',
			marker: {
				enabled:false,
				radius: 2
			},
			data: []
		}]
	}
	
	// Load data asynchronously using jQuery. On success, add the data
	// to the options and initiate the chart.
	// http://api.jquery.com/jQuery.getJSON/

		
	chart = new Highcharts.Chart(options);
});
