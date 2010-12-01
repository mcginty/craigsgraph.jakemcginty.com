<?php
	//header("Content-type: text/json");

	function clean_up($string) {
		for ($i = 0; $i < strlen ($string); $i++)
			if (ord($string[$i]) > 127)
				$string[$i] = " ";
		$string = mb_convert_case($string, MB_CASE_LOWER, "UTF-8");
		$string = preg_replace("/(@{2,}|1{4,}|\*{2,}|#{2,}|:{3,}|~{2,}|-{2,}|\{{2,}|\}{2,})/","",$string);
		return $string;
	}

	$mysqli = new mysqli('localhost', 'root', 'JACOB98631', 'craigsgraph');
	if ($mysqli->connect_error) {
	    die('Connect Error (' . $mysqli->connect_errno . ') '
	            . $mysqli->connect_error);
	}

	$info_q = "SELECT MAX(price) AS maxprice, MIN(price) AS minprice FROM items WHERE MATCH(title,description) AGAINST ('{$_GET['q']}' )";
	$info_row = $mysqli->query($info_q)->fetch_assoc();
	$pmax = $info_row['maxprice'];
	$pmin = $info_row['minprice'];


	$smartq = "SELECT COUNT(*) AS count, FLOOR(AVG(pmin)) AS avg_pmin, FLOOR(AVG(pmax)) AS avg_pmax FROM searches WHERE `query` = '{$_GET['q']}';";
	$smartrow = $mysqli->query($smartq)->fetch_assoc();
	if (isset($smartrow['avg_pmin']) && $smartrow['count']>3) $pmin = $smartrow['avg_pmin'];
	if (isset($smartrow['avg_pmax']) && $smartrow['count']>3) $pmax = $smartrow['avg_pmax'];


	if (isset($_GET['pmin']) && isset($_GET['pmax'])) {
		$pmin = $_GET['pmin'];
		$pmax = $_GET['pmax'];
	}

	$q =  "SELECT AVG(MATCH(title, description) AGAINST ('{$_GET['q']}' ) + MATCH(title) AGAINST ('{$_GET['q']}' )) AS avgscore FROM items WHERE MATCH(title, description) AGAINST ('{$_GET['q']}') AND price > {$pmin} AND price < {$pmax}";

	$row = $mysqli->query($q)->fetch_assoc();
	$avgscore = $row['avgscore']/2;

	$q =  "SELECT *, (MATCH(title, description) AGAINST ('{$_GET['q']}' ) + MATCH(title) AGAINST ('{$_GET['q']}' )) AS score FROM items WHERE MATCH(title, description) AGAINST ('{$_GET['q']}' ) HAVING score > {$avgscore} AND price > {$pmin} AND price < {$pmax} ORDER BY score DESC";

	$res = $mysqli->query($q);
	//$jsonOut = array();
	
	$pmax = 0;
	$pmin = PHP_INT_MAX;
	while ($row = $res->fetch_assoc()) {
		$row['title'] = clean_up(preg_replace("/&#?[a-z0-9]{2,8};/i","",$row['title']));
		$row['id'] = (strtotime($row['date'])).$row['price'];
		$jsonOut[$row['id']] = array('title'=>$row['title'], 'link'=>$row['link'], 'price'=>$row['price'], 'location'=>$row['location']);
		$pmax = $row['price']>$pmax ? $row['price'] : $pmax;
		$pmin = $row['price']<$pmin ? $row['price'] : $pmin;
	}
	if ($pmin == PHP_INT_MAX) $pmin = 0;
	if (isset($_GET['sid'])) {
		$sid = $_GET['sid'];
		$updt_query = "UPDATE `craigsgraph`.`searches` SET  `pmin` = '{$_GET['pmin']}', `pmax` = '{$_GET['pmax']}' WHERE  `searches`.`idsearch` ={$sid};";
		$mysqli->query($updt_query);
	}
	else {
		$insert_query = "INSERT INTO `craigsgraph`.`searches` (`idsearch` ,`time` ,`query` ,`pmin` ,`pmax`) VALUES (NULL , NOW( ) ,  '{$_GET['q']}',  '{$pmin}',  '{$pmax}' );";
		$mysqli->query($insert_query);
		$sid = $mysqli->insert_id;
	}
	$jsonOut['properties'] = array('pmax'=>$pmax, 'pmin'=>$pmin, 'sid'=>$sid);
	echo json_encode($jsonOut);



?>