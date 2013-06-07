<?php

/**
* Defines a shortcut for /cms/account pages (login, logout, etc.) so that account doesn't have to be used in the URL.
*
*/
Route::set('auth', 'cms/<action>',
	array(
		'action' => 'logout'
	))
	->defaults(array(
		'controller' => 'Cms_Auth'
	));

Route::set('login', 'cms/login(/<controller>)')
	->defaults(array(
		'directory' => 'Cms_Auth',
		'controller' => ucfirst(Kohana::$config->load('auth')->get('login_method')),
		'action' => 'begin',
	))
	->filter(function(Route $route, $params, Request $request)
		{
			if ($request->query('openid_mode'))
			{
				$params['controller'] = 'Openid';
			}

			if ($request->query('openid_mode') OR ($params['controller'] == 'Password' AND $request->method() == Request::POST))
			{
				$params['action'] = 'process';
			}

			return $params;
		});