<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Class for Sledge template plugins.
 * Plugins are essentially just calls to controllers to add particular content to a template.
 * This class simplifies the calls to these controllers so that a frontend developer doesn't have to remember how to make Kohana internal request calls.
 * Therefore instead of doing Request::factory('sledge/slfjsa/slfajf')->post( array( ...))->execute();
 * The developer can just do Plugin::insert( name, options);
 *
 *
 * @package Sledge
 * @category Plugins
 * @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
 * @copyright 2012, Hoop Associates
 *
 */
abstract class Sledge_Plugin
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

		// Prepare the request.
		$request = Request::factory(Kohana::$config->load('plugins')->$name)->post($options);

		// Excute the request and return the output.
		try
		{
			return $request->execute();
		}
		catch (Exception $e)
		{
			// If the site is in production then don't allow a failure in a plugin to bring down the whole page, just log the error.
			// If the environment isn't production then rethrow the error so that it can be debugged.
			if (Kohana::$environment == Kohana::PRODUCTION OR Kohana::$environment == Kohana::STAGING)
			{
				Log::instance()->add(Log::ERROR, $e->getMessage() . "\n" . $e->getTraceAsString());
			}
			else
			{
				throw $e;
			}
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