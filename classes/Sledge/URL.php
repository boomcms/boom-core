<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package Sledge
* @category Helpers
*/
class Sledge_URL extends Kohana_URL
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
			$base = $base . "/";
		}

		$start_uri = $base . $title;
		$append = 0;

		// Get a unique URI.
		do
		{
			$uri = ($append > 0)? ($start_uri. $append) : $start_uri;
			$append++;

			$page_uri = ORM::factory('Page_URI', array('uri' => $uri));
		}
		while ($page_uri->loaded() == TRUE);

		return $uri;
	}

	/**
	 * Generate a gravatar URL.
	 *
	 * @param	string	$email	Emailaddress of the gravater.
	 * @param	integer	$size
	 * @param	string	$default
	 * @return	string
	 */
	public static function gravatar($email, $size = 80, $default = 'wavatar')
	{
		return "http://www.gravatar.com/avatar/" . md5($email) . "?s=$size&d=" . urlencode($default);
	}

	/**
	* This attempts to fix a (possibly intentional?) bug in Kohana.
	* @link http://kohanaframework.org/3.2/guide/api/URL#site
	* @link http://kohanaframework.org/3.2/guide/api/URL#base
	*
	* In the method declarations for URL::site() and URL::base() the default value for $index is TRUE in site but FALSE in base()
	* This leads to inconsistencies, which this extension aims to fix.
	*/
	public static function site($uri = '', $protocol = NULL, $index = NULL)
	{
		if ($index === NULL)
		{
			$index = FALSE;
		}

		return parent::site($uri, $protocol, $index);
	}
}