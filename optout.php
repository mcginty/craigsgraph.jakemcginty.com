<?php

	if (!isset($_GET['sid'])) {
		die("set an sid.");
	}

	$mysqli = new mysqli('localhost', 'root', 'JACOB98631', 'craigsgraph');
	if ($mysqli->connect_error) {
	    die('Connect Error (' . $mysqli->connect_errno . ') '
	            . $mysqli->connect_error);
	}

	$q = "DELETE FROM `craigsgraph`.`searches` WHERE `searches`.`idsearch` = {$_GET['sid']};";
	$mysqli->query($q);
?>