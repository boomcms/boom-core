<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Class for Boom template plugins.
 * Plugins are essentially just calls to controllers to add particular content to a template.
 * This class simplifies the calls to these controllers so that a frontend developer doesn't have to remember how to make Kohana internal request calls.
 * Therefore instead of doing Request::factory('boom/slfjsa/slfajf')->post( array( ...))->execute();
 * The developer can just do Plugin::insert( name, options);
 *
 *
 * @package	BoomCMS
 * @category	Plugins
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
abstract class Boom_Plugin
{
	/**
	 * Holds an array of registered plugins.
	 */
	protected static $_plugins = array();

	public static function insert($name, $options = array())
	{
		// Check that the plugin has been register.
		if ( ! Plugin::is_registered($name))
		{
			throw new Exception("Attempting to include unregistered plugin: " . $name);
		}

		// Execute the request and get the response
		$response = Request::factory(
				Kohana::$config
					->load('plugins')
					->$name
			)
			->post($options)
			->execute();

		// Was there an error?
		if ($response->status() === 500)
		{
			// If the site is in development throw an exception so that the error can be debugged.
			if (Kohana::$environment === Kohana::DEVELOPMENT)
			{
				return $response
					->body();
			}
		}
		else
		{
			// Return the response body.
			return $response
				->body();
		}
	}

	/**
	 * Check whether a plugin has been registered.
	 *
	 * @param string $name The name of the plugin.
	 * @return bool
	 */
	public static function is_registered($name)
	{
		return isset(Kohana::$config->load('plugins')->$name);
	}
}