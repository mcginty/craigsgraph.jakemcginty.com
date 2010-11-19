<html>
<head>
<title>Test Crawler</title>
<link rel="stylesheet" type="text/css" href="styles/pretty.css" />
</head>

<body>

<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
$DEBUG = 1;
$MAX_PAGES = 10;
require_once "classes/CraigsCrawler/CraigsCrawler.php";

if ($DEBUG) echo "Starting...<br />";

$start = (float) array_sum(explode(' ',microtime())); 
//$uri = "http://chambana.craigslist.org/search/?areaID=190&subAreaID=&query={$_GET['query']}&catAbb=sss";
$uri = "http://chambana.craigslist.org/sss/";
$crawler = CraigsCrawler::init();

$crawler->set("debug", $DEBUG);
$crawler->set("uri", $uri);
$crawler->set("max_pages", $MAX_PAGES);


$crawler->process("results");

$results = $crawler->retrieve("results");
$totals	 = $crawler->retrieve("totals");
$useful_total = $crawler->retrieve("useful_total");
$pages_traversed = $crawler->retrieve("pages_traversed");

$end = (float) array_sum(explode(' ',microtime()));


if ($DEBUG) {
	echo "total craigslist results: {$totals['found']}<br />";
	echo "loaded up until: {$totals['max']}<br />";
	echo "useful results: ".($useful_total === FALSE ? "FALSE" : $useful_total)."<br />";
	echo "pages traversed: $pages_traversed<br />";
	echo "Processing time: ". sprintf("%.4f", ($end-$start))." seconds<br /><br />";
}
echo '<table id="hor-minimalist-b">'
	.'<thead><tr>'
	.'<th scope="col">Date</th>'
	.'<th scope="col">URI</th>'
	.'<th scope="col">Title</th>'
	.'<th scope="col">Price</th>'
	.'<th scope="col">Location</th>'
	.'<th scope="col">cat</th>'
	.'<th scope="col">Category</th>'
	.'<th scope="col">pic?</th>'
	.'</tr></thead><tbody>';
foreach ($results as $result) {
	$pic = (isset($result['pic']) ? "yes" : "no");
	echo "<tr>"
		."<td>{$result['date']}</td><td>{$result['uri']}</td><td>{$result['title']}</td><td>{$result['price']}</td><td>{$result['location']}</td><td>{$result['shortcat']}</td><td>{$result['category']}</td><td>$pic</td>"
		."</tr>";
	
}
echo "</tbody></table>";

?>

</body>
</html>
