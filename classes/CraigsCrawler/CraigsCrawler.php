<?php

class CraigsCrawler {
	// Important Variables
	private static $_instance;

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

	private static $content = '';
	private static $uri = '';
	private static $results = array();
	private static $totals = array();
	private static $useful_total = 0;
	private static $MAX_PAGES = 10;
	private static $pages_traversed = 0;
	private static $DEBUG = 1;

	private function __construct() {
		if (self::$DEBUG) echo "CraigsCrawler initialized.<br />";
	}
	
	public static function init() {
		if (!isset(self::$_instance))
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		return self::$_instance;
	}

	// Wrapper function for fetching specific content items
	public function process($type) {
		$func = "_process_{$type}";
		if (method_exists($this, $func)) {
			return call_user_func(array('CraigsCrawler', $func));
		}
	}
	
	public function retrieve($type) {
		$func = "_retrieve_{$type}";
		if (method_exists($this, $func)) {
			return call_user_func(array('CraigsCrawler', $func));
		}
	}

	public function set($type, $value) {
		$func = "_set_{$type}";
		if (method_exists($this, $func)) {
			return call_user_func(array('CraigsCrawler', $func), $value);
		}
	}

	private function getContent($s) {
		self::$content = file_get_contents(self::$uri."&s={$s}");
	}

	protected function _process_results() {
		$i=0;
		self::$totals['found'] = 1;
		self::$totals['min'] = 0;
		self::$totals['max'] = 0;
		self::$useful_total = 0;
		while (self::$totals['max'] < self::$totals['found'] && self::$pages_traversed < self::$MAX_PAGES) {
			
			self::getContent(self::$totals['max']);

			preg_match(self::$totals_regex, self::$content, self::$totals);
			$res = preg_match_all(self::$results_regex, self::$content, $resultset, PREG_SET_ORDER);
			self::$results = array_merge(self::$results, $resultset);
			self::$useful_total += $res;

			self::$pages_traversed++;
		}
	}

	protected function _retrieve_results() {
		return self::$results;
	}

	protected function _retrieve_totals() {
		return self::$totals;
	}

	protected function _retrieve_useful_total() {
		return self::$useful_total;
	}

	protected function _retrieve_pages_traversed() {
		return self::$pages_traversed;
	}

	protected function _set_max_pages($value) {
		self::$MAX_PAGES = $value;
		if (self::$DEBUG) echo "set max_pages to: ".self::$MAX_PAGES."<br />";
	}

	protected function _set_uri($uri) {
		self::$uri = $uri;
		if (self::$DEBUG) echo "set uri to: ".self::$uri."<br />";
	}

	protected function _set_debug($debug) {
		self::$DEBUG = $debug;
		if (self::$DEBUG) echo "set debug to: ".self::$DEBUG."<br />";
	}

	public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
}
