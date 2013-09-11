<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package Boom
 * @category Helpers
 */
class Boom_URL extends Kohana_URL
{
	/**
	 * Generate a unique URI
	 *
	 * @param	string	$base 	URL base, e.g. /path/to/page
	 * @param 	string	$title 	Title of the page.
	 */
	public static function generate($base, $title)
	{
		// Make sure there's no &amps; etc. in the title otherwise these won't be stripped out properly by URL::title()
		$title = html_entity_decode($title);

		// Remove any non-urlable characters.
		$title = URL::title($title);

		// If the base URL isn't empty and there's no trailing / then add one.
		if ($base AND substr($base, -1) != "/")
		{
			$base = $base."/";
		}

		// Only append the base if it's more than just '/'.
		$start_uri = ($base == '/')? $title : $base.$title;
		$append = 0;

		// Get a page URL model which we'll use to call Model_Page_URL::location_available()
		$page_url = new Model_Page_URL;

		// Get a unique URI.
		do
		{
			$uri = ($append > 0)? ($start_uri.$append) : $start_uri;
			$append++;
		}
		while ( ! $page_url->location_available($uri));

		return $uri;
	}
}