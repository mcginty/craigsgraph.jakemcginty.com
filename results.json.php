<?php

function generateJSON($q, $price_filter) {
	$mysqli = new mysqli('localhost', 'root', 'JACOB98631', 'craigsgraph');
	if ($mysqli->connect_error) {
	    die('Connect Error (' . $mysqli->connect_errno . ') '
	            . $mysqli->connect_error);
	}

	$res = $mysqli->query("SELECT * FROM items WHERE MATCH(title,description) AGAINST ('+({$q})' IN BOOLEAN MODE) AND price > {$price_filter['min']} AND price < {$price_filter['max']};");
	$jsonOut = array();
	while ($row = $res->fetch_assoc()) {
		$row['title'] = addslashes($row['title']);
		$row['date'] = strtotime($row['date']);
		$jsonOut[] = array('name'=>$row['id_hash'], 'color'=>"#00FF00", 'y'=>(int)$row['price']);
	}
	return json_encode($jsonOut);
}

?>