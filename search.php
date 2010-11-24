<?php
require_once("results.json.php");

?>
<html>
<head>
<title>Items so far</title>
<link rel="stylesheet" type="text/css" href="styles/search.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script> 
<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/base/jquery-ui.css" type="text/css" media="all" />
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript">

$(function() {
	$( "#priceslider" ).slider({
		range: true,
		min: 0,
		max: 100000,
		values: [ 300, 50000 ],
		slide: function( event, ui ) {
			$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
			
		}
	});
	$( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
		" - $" + $( "#slider-range" ).slider( "values", 1 ) );
});

var chart;
$(document).ready(function() {

	// define the options
	var options = {
		chart: {
			renderTo: 'graph-container',
			defaultSeriesType: 'line',
		},
		title: {
			text: 'Craigsgraph Results'
		},
		subtitle: {
			text: 'Source: chambana.craigslist.org'
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
	                return '<b>'+ this.series.name +'</b><br/>'+
					this.x +': $'+ this.y;
			}
		},
		plotOptions: {
			line: {
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
				lineWidth: 1,
				shadow: false
			}
		},
		series: [{
			name: 'Craigslist Results',
			marker: {
				radius: 2
			},
			data: eval('<?php echo generateJSON($_GET['q'], array('min'=>0, 'max'=>100000)); ?>')
		}]
	}
	
	// Load data asynchronously using jQuery. On success, add the data
	// to the options and initiate the chart.
	// http://api.jquery.com/jQuery.getJSON/

		
	chart = new Highcharts.Chart(options);
});

</script>
</head>

<body>
<div id="main-container">
	<div id="header">
		<div id="title">craigsgraph</div>
		<div id="searchbar">
			<input type="text" id="query" name="q" />
			<input type="submit" name="" id="go" value=" " />
		</div>
	</div>
	<div id="graph-container" style="width: 800px; height:400px;"></div>
	<div id="priceslider"></div>

	
</div>
</body>
</html>
