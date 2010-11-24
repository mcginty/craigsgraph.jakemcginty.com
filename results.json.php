<?php
header("Content-type: text/json");

if (empty($_GET['q'])) die('Please provide a query.');

$res = $mysqli->query("SELECT * FROM items WHERE MATCH(title,description) AGAINST ('+({$_GET['q']})' IN BOOLEAN MODE);");

if ($row = $res->fetch_assoc()) {
	$row['title'] = addslashes($row['title']);
	$row['date'] = date('Y, n, j, H, i', strtotime($row['date']));
	echo "[{ date: '{$row['date']}', price: '{$row['price']}', title: '{$row['title']}' }";
}

while ($row = $res->fetch_assoc()) {
	$row['title'] = addslashes($row['title']);
	echo ",\n";
	$row['date'] = date('Y, n, j, H, i', strtotime($row['date']));
	echo ", { date: '{$row['date']}', price: '{$row['price']}', title: '{$row['title']}' }";
}
echo "]";

?>