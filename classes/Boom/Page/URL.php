<?php

/**
 * Helper functions for page URLs
 *
 */
abstract class Boom_Page_URL
{
	/**
	 * Generate a unique URL from a page title
	 *
	 * @param string $base
	 * @param string $title
	 */
	public static function from_title($base, $title)
	{
		$url = static::sanitise($title);

		// If the base URL isn't empty and there's no trailing / then add one.
		if ($base AND substr($base, -1) != "/")
		{
			$base = $base."/";
		}

		$url = ($base == '/')? $url : $base.$url;

		return static::make_unique($url);
	}

	/**
	 * Determine whether a URL is already being used by a page in the CMS
	 *
	 * @param string $url
	 */
	public static function is_available($url, $ignore_page = NULL)
	{
		return ! ORM::factory('Page_URL')
			->where('location', '=', $url)
			->where('page_id', '!=', $ignore_page)
			->find()
			->loaded();
	}

	/**
	 * Increments a numeric suffix until the URL is unique
	 *
	 * @param string $url
	 */
	public static function make_unique($url)
	{
		$append = 0;
		$start_url = $url;

		do
		{
			$url = ($append > 0)? ($start_url.$append) : $start_url;
			$append++;
		}
		while ( ! static::is_available($url));

		return $url;
	}

	/**
	 * Remove invalid characters from a URL
	 *
	 * @param string $url
	 */
	public static function sanitise($url)
	{
		$url = trim($url);
		$url = strtolower($url);
		$url = parse_url($url, PHP_URL_PATH);		// Make sure it doesn't contain a hostname
		$url = trim($url, '/');					// Remove '/' from the beginning or end of the link
		$url = preg_replace('|/+|', '/', $url);		// Remove duplicate forward slashes.

		$url = preg_replace('|[^'.preg_quote('-').'\/\pL\pN\s]+|u', '', $url); // Remove all characters that are not a hypeh, letters, numbers, or forward slash
		$url = preg_replace('|['.preg_quote('-').'\s]+|u', '-', $url); // Replace all hypens and whitespace by a single hyphen

		return $url;
	}
}