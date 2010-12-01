<?php
	//header("Content-type: text/json");
	function HSVtoRGB(array $hsv) {
		list($H,$S,$V) = $hsv;
		//1
		$H *= 6;
		//2
		$I = floor($H);
		$F = $H - $I;
		//3
		$M = $V * (1 - $S);
		$N = $V * (1 - $S * $F);
		$K = $V * (1 - $S * (1 - $F));
		//4
		switch ($I) {
			case 0:
				list($R,$G,$B) = array($V,$K,$M);
			break;
			case 1:
				list($R,$G,$B) = array($N,$V,$M);
			break;
			case 2:
				list($R,$G,$B) = array($M,$V,$K);
			break;
			case 3:
				list($R,$G,$B) = array($M,$N,$V);
			break;
			case 4:
				list($R,$G,$B) = array($K,$M,$V);
			break;
			case 5:
			case 6: //for when $H=1 is given
				list($R,$G,$B) = array($V,$M,$N);
			break;
		}
		return array($R, $G, $B);
	}

	function relevance_to_color($score, $minscore, $maxscore) {
		$hsv_end = 120/360;
		$hsv_start   = 0.0;

		$s = 0.88;

		$pct = ($score - $minscore) / ($maxscore - $minscore); // percentage price-wise
		$v = 0.75;
		$h = $hsv_start + ($pct * $hsv_end);
		//echo "pct: $pct, h: $h<br />";
		return HSVtoRGB(array($h, $s, $v));
	}


	$mysqli = new mysqli('localhost', 'root', 'JACOB98631', 'craigsgraph');
	if ($mysqli->connect_error) {
	    die('Connect Error (' . $mysqli->connect_errno . ') '
	            . $mysqli->connect_error);
	}


	$info_q = "SELECT MAX(price) AS maxprice, MIN(price) AS minprice FROM items WHERE MATCH(title, description) AGAINST ('{$_GET['q']}' )";
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

	$q =  "SELECT AVG(MATCH(title, description) AGAINST ('{$_GET['q']}') + MATCH(title) AGAINST ('{$_GET['q']}')) AS avgscore, MAX(MATCH(title, description) AGAINST ('{$_GET['q']}') + MATCH(title) AGAINST ('{$_GET['q']}')) as maxscore FROM items WHERE MATCH(title, description) AGAINST ('{$_GET['q']}' ) AND price > {$pmin} AND price < {$pmax}";

	$row = $mysqli->query($q)->fetch_assoc();
	$avgscore = $row['avgscore']/2;
	$maxscore = $row['maxscore'];

	$q =  "SELECT *, (MATCH(title, description) AGAINST ('{$_GET['q']}')+MATCH(title) AGAINST ('{$_GET['q']}')) AS score FROM items WHERE MATCH(title, description) AGAINST ('{$_GET['q']}' ) HAVING score > {$avgscore} AND price > {$pmin} AND price < {$pmax} ORDER BY score DESC";

	$res = $mysqli->query($q);

	while ($row = $res->fetch_assoc()) {
		$row['date'] = strtotime($row['date']);
		$id = $row['date'].$row['price'];
		$rgbcolor = relevance_to_color($row['score'], $avgscore, $maxscore);
		$rgbstring = "#".str_pad(strtoupper(dechex($rgbcolor[0]*255)),2,"0").str_pad(strtoupper(dechex($rgbcolor[1]*255)),2,"0").str_pad(strtoupper(dechex($rgbcolor[2]*255)),2,"0");
		$radius = 
		$jsonOut[] = array('id'=>$id, 'x'=>$row['date'], 'y'=>(int)$row['price'], 'color'=>$rgbstring, 'marker'=>array('fillColor'=>$rgbstring));
	}
	
	echo json_encode($jsonOut);

?>