<?php
	
	/* Run against: Craigslist "for sale" search page.

	   Provides:
		* date						(date the item was posted)
		* uri						(link if you want to crawl or otherwise use specific item page)
		* title						(watch for odd characters)
		* price						(currently DOES NOT MATCH list items without price)
		* location 					(NOT managed by craigslist, can be anything, really)
		* short category name 		(unique 3-char category key)
		* category description		(longer name for the short category key)
		* has picture 				(blank for no, "pic" for yes)
	*/
	private static $results_regex = '/<span class="ih"[^>]*>[^>]*<\/span>[\w]*(?P<date>[^-]+) - <a href="(?P<uri>[^"]*)">(?P<title>[^<]*) -<\/a>[\s]*(?P<price>\$\d+)[\s]*(<font size="-1"> \((?P<location>[^)]+)\)<\/font> )?<small class="gc"><a href="\/(?P<shortcat>\w*)\/">(?P<category>[\w\s&;]*)<\/a><\/small>( <span class="p"> (?P<pic>pic)<\/span>)?<br class="c">/mi';
	

	/* Run against: Craigslist "for sale" search page.

	   Provides:
	  	* total results found
	  	* minimum displayed item
	  	* maximum displayed item
	*/
	private static $totals_regex = '/^\s*<b>Found: (?P<found>\d+) Displaying: (?P<min>\d+) - (?P<max>\d+)<\/b>\s*$/mi';

?>