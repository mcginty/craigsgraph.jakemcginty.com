<?php

	if (!isset($_GET['term'])) {
		die("set a q.");
	}

	$mysqli = new mysqli('localhost', 'root', 'JACOB98631', 'craigsgraph');
	if ($mysqli->connect_error) {
	    die('Connect Error (' . $mysqli->connect_errno . ') '
	            . $mysqli->connect_error);
	}

	$q = "SELECT `query` FROM `craigsgraph`.`searches` WHERE `searches`.`query` LIKE '%{$_GET['term']}%' GROUP BY `query`;";
	$res = $mysqli->query($q);
	while ($row = $res->fetch_assoc()) {
		$list[] = $row['query'];
	}
	echo json_encode($list);
?>