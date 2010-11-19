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
<link rel="stylesheet" type="text/css" href="styles/pretty.css" />
</head>

<body>
<?php

$res = $mysqli->query("SELECT * FROM items ORDER BY  `items`.`date` DESC;");

echo "total results: {$res->num_rows}<br />";

echo '<table id="hor-minimalist-b">'
	.'<thead><tr>'
	.'<th scope="col">Date</th>'
	.'<th scope="col">Title</th>'
	.'<th scope="col">Price</th>'
	.'<th scope="col">Location</th>'
	.'<th scope="col">cat</th>'
	.'<th scope="col">hash</th>'
	.'</tr></thead><tbody>';
	
while ($row = $res->fetch_assoc()) {
	$row['date'] = date('M jS, H:i', strtotime($row['date']));
	echo "<tr>"
		."<td>{$row['date']}</td><td>{$row['title']}</td><td>{$row['price']}</td><td>{$row['location']}</td><td>{$row['cat']}</td><td>{$row['description']}</td>"
		."</tr>";
	if (preg_match('/\b(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?\b/i', $row['description'], $number))
		$numbers .= $number[0]."<br />";
	if (preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $row['description'], $email))
		$emails	.= $email[0]."<br />";
}
echo "</tbody></table>";
echo $numbers;
echo $emails;
?>

</body>
</html>
