<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'apc' => array(
		'namespace'			 => Arr::get(Kohana::$config->load('database.default.connection' ), 'database'),
		'driver'             => 'apc',
		'default_expire'     => NULL,			
	),
	'memcache' => array(
		'namespace'			 => Arr::get(Kohana::$config->load('database.default.connection' ), 'database'),
		'driver'             => 'memcache',
		'default_expire'     => NULL,
		'compression'        => FALSE,              // Use Zlib compression (can cause issues with integers)
		'servers'            => array(
			array(
				'host'             => 'localhost',  // Memcache Server
				'port'             => 11211,        // Memcache port number
				'persistent'       => FALSE,        // Persistent connection
				'weight'           => 1,
				'timeout'          => 1,
				'retry_interval'   => 15,
				'status'           => TRUE,
			),
		),
		'instant_death'      => TRUE,               // Take server offline immediately on first fail (no retry)
	),
);