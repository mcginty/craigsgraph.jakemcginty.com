<?php
$DEBUG=1;
require "classes/simplepie.inc";
$feed = new SimplePie();
if (isset($_GET['query'])) {
	echo "http://chambana.craigslist.org/search/?areaID=190&subAreaID=&query={$_GET['query']}&catAbb=sss&format=rss";
	$feed->set_feed_url("http://chambana.craigslist.org/search/?areaID=190&subAreaID=&query={$_GET['query']}&catAbb=sss&format=rss");
}
else {
	$feed->set_feed_url("http://chambana.craigslist.org/sss/index.rss");
	echo "poo";
}
$feed->set_item_class();
$feed->enable_cache(false);
$feed->init();
$feed->handle_content_type();
$mysqli = new mysqli('localhost', 'root', 'JACOB98631', 'craigsgraph');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}
?>
<html>
<head>
<title>Test Crawler</title>
<link rel="stylesheet" type="text/css" href="styles/pretty.css" />
</head>

<body>
<?php
if ($DEBUG) {
	echo "feed title: {$feed->get_title()}<br />";
	echo "total results: {$feed->get_item_quantity()}<br />";
}
echo '<table id="hor-minimalist-b">'
	.'<thead><tr>'
	.'<th scope="col">Date</th>'
	.'<th scope="col">URI</th>'
	.'<th scope="col">Title</th>'
	.'<th scope="col">Price</th>'
	.'<th scope="col">Location</th>'
	.'<th scope="col">cat</th>'
	.'<th scope="col">hash</th>'
	.'</tr></thead><tbody>';
	
	
foreach ($feed->get_items() as $result) {
	
	$useful = preg_match('/(?P<title>.*) \((?P<location>.*)\) \$(?P<price>\d+)/i', $result->get_title(), $parsed);
	$parsed['title'] = $mysqli->escape_string( strip_tags($parsed['title']) );
	$parsed['description'] = $mysqli->escape_string( strip_tags( $result->get_description() ) );
	$parsed['location'] = $mysqli->escape_string( strip_tags( $parsed['location'] ) );
	
	preg_match('/http:\/\/\w+\.craigslist\.org\/(?P<cat>\w{3})\/(?P<id>\d+)\.html/i', $result->get_link(0), $uri);
	$mysql_timestamp = date('Y-m-d H:i:s', strtotime($result->get_date()));
	$hash = sha1($mysql_timestamp.$result->get_title());
	
	
	if ($useful) {
		$res = $mysqli->query("SELECT id_hash FROM items WHERE id_hash = '{$hash}';");
		
		
		if (!isset($mysql->num_rows) || $mysqli->num_rows == 0) {
			$mysqli->query("INSERT INTO items(id_hash, date, link, title, description, location, price, cat) VALUES(\"{$hash}\", \"{$mysql_timestamp}\", \"{$result->get_link(0)}\", \"{$parsed['title']}\", \"{$parsed['description']}\", \"{$parsed['location']}\", {$parsed['price']}, \"{$uri['cat']}\");");
			//echo "<tr><td>INSERT INTO items(id_hash, date, link, title, description, location, price, cat) VALUES(\"{$hash}\", \"{$mysql_timestamp}\", \"{$result->get_link(0)}\", \"{$parsed['title']}\", \"{$parsed['description']}\", \"{$parsed['location']}\", {$parsed['price']}, \"{$uri['cat']}\");</td></tr>";
			echo "<tr>"
				."<td>{$result->get_date()}</td><td>{$result->get_link()}</td><td>{$parsed['title']}</td><td>{$parsed['price']}</td><td>{$parsed['location']}</td><td>{$uri['cat']}</td><td>{$hash}</td>"
				."</tr>";
		}
		
	}
	
}
echo "</tbody></table>";

?>

</body>
</html>
