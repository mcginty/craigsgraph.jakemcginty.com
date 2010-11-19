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
google.load('visualization', '1', {'packages':['annotatedtimeline']});
google.setOnLoadCallback(drawChart);
	
	function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('datetime', 'Date Time');
		data.addColumn('number', 'Price');
		data.addColumn('string', 'Title');
		data.addColumn('string', 'Description');
		
		data.addRows([
		<?php 
			if (empty($_GET['q'])) die('death');
			file_get_contents("http://craigsgraph.jakemcginty.com/search.php?query={$_GET['q']}");
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
		?>
		]);

		var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
		chart.draw(data, {displayAnnotations: false, displayExactValues: true, displayRangeSelector: false});
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
	<div id="chart_div" style="width: 500px; height:400px;"></div>

	
</div>
</body>
</html>
