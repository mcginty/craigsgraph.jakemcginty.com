<?php
$DEBUG=1;
require "classes/simplepie.inc";
$feed = new SimplePie();
if (isset($_GET['query'])) {
	echo $_GET['query'];
	$feed->set_feed_url("http://chambana.craigslist.org/search/?areaID=190&subAreaID=&query={$_GET['query']}&catAbb=sss&format=rss");
}
else {
	$feed->set_feed_url("http://chambana.craigslist.org/sss/index.rss");
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
$total_added = 0;
$total_grabbed = 0;
foreach ($feed->get_items() as $result) {
	$useful = preg_match('/(?P<title>.*) \((?P<location>.*)\) \$(?P<price>\d+)/i', $result->get_title(), $parsed);
	if ($useful) {
		$total_grabbed++;
		$parsed['title'] = $mysqli->escape_string( strip_tags($parsed['title']) );
		$parsed['description'] = $mysqli->escape_string( strip_tags( $result->get_description() ) );
		$parsed['location'] = $mysqli->escape_string( strip_tags( $parsed['location'] ) );
		
		preg_match('/http:\/\/\w+\.craigslist\.org\/(?P<cat>\w{3})\/(?P<id>\d+)\.html/i', $result->get_link(0), $uri);
		$mysql_timestamp = date('Y-m-d H:i:s', strtotime($result->get_date()));
		$hash = sha1($mysql_timestamp.$result->get_title());
	
	
		
		
		$ret = $mysqli->query("INSERT INTO items(id_hash, date, link, title, description, location, price, cat) VALUES(\"{$hash}\", \"{$mysql_timestamp}\", \"{$result->get_link(0)}\", \"{$parsed['title']}\", \"{$parsed['description']}\", \"{$parsed['location']}\", {$parsed['price']}, \"{$uri['cat']}\");");
		if ($ret != FALSE) {
			$total_added++;
		}
	}
		
	
}
echo "Total items added on ". date('Y-m-d H:i') ." = {$total_added}. Total grabbed = {$total_grabbed}.\n"; 
?>
