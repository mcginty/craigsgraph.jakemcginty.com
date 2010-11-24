<?php

$mysqli = new mysqli('localhost', 'root', 'JACOB98631', 'craigsgraph');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
?>
<html>
<head>
<title>Items so far</title>
<link rel="stylesheet" type="text/css" href="styles/search.css" />
<script src="http://www.google.com/jsapi?key=ABQIAAAA6Ennl4qFhvWkOoljdQVBbRQvSXdoFQAhOQR8gZI1KPGpFPYSXBTMVL6PKL92Sqr-MrY_r412cvLbcQ" type="text/javascript"></script>
<script type="text/javascript">
	google.load("jquery, 1.4.3");
	google.load("jqueryui, 1.8.5");
</script>
<script type="text/javascript" src="js/highcharts.js"></script>
<script type="text/javascript">
var chart;
$(document).ready(function() {

	// define the options
	var options = {
		chart: {
			renderTo: 'container',
			defaultSeriesType: 'area'
		},
		title: {
			text: 'Craigsgraph results'
		},
		subtitle: {
			text: 'Source: chambana.craigslist.org'
		},
		xAxis: {
		},
		yAxis: {
			title: {
				text: 'Price'
			}
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
			spline: {
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
				}
			}
		},
		series: []
	}
	
	// Load data asynchronously using jQuery. On success, add the data
	// to the options and initiate the chart.
	// http://api.jquery.com/jQuery.getJSON/
	jQuery.getJSON('res_json.php?q=<?php echo $_GET['q']; ?>', null, function(data) {
		options.series.push({
			name: 'Tokyo',
			data: data
		});
		
		chart = new Highcharts.Chart(options);
	});
	
});
	

if (empty($_GET['q'])) die('death');
//file_get_contents("http://craigsgraph.jakemcginty.com/search.php?query={$_GET['q']}");
$res = $mysqli->query("SELECT * FROM items WHERE MATCH(title,description) AGAINST ('+({$_GET['q']})' IN BOOLEAN MODE);");
if ($row = $res->fetch_assoc()) {
	$row['title'] = addslashes($row['title']);
	$row['date'] = date('Y, n, j, H, i', strtotime($row['date']));
	echo "[new Date({$row['date']}), {$row['price']}, '{$row['title']}', undefined]";
}
while ($row = $res->fetch_assoc()) {
	$row['title'] = addslashes($row['title']);
	echo ",\n";
	$row['date'] = date('Y, n, j, H, i', strtotime($row['date']));
	echo "[new Date({$row['date']}), {$row['price']}, '{$row['title']}', undefined]";
}

</script>
</head>

<body>
<div id="container">
	<div id="header">
		<div id="title">craigsgraph</div>
		<div id="searchbar">
			<input type="text" id="query" name="q" />
			<input type="submit" name="" id="go" value=" " />
		</div>
	</div>
	<div id="container" style="width: 100%; height:400px;"></div>

	
</div>
</body>
</html>
