<?php

/**
* Defines a shortcut for /cms/account pages (login, logout, etc.) so that account doesn't have to be used in the URL.
*
*/
Route::set('auth', 'cms/logout')
	->defaults(array(
		'controller' => 'Cms_Auth_Logout',
		'action' => 'index',
	));

Route::set('recover', 'cms/recover')
	->defaults(array(
		'controller' => 'Cms_Auth_Recover',
		'action' => 'create_token',
	))
	->filter(function(Route $route, $params, Request $request)
		{
			if ($request->query('token') AND $request->query('email'))
			{
				$params['action'] = 'set_password';
				return $params;
			}
		}
	);

Route::set('profile', 'cms/profile')
	->defaults(array(
		'controller' => 'Cms_Profile',
		'action' => 'view',
	))
	->filter(function(Route $route, $params, Request $request)
		{
			if ($request->method() == Request::POST)
			{
				$params['action'] = 'save';
				return $params;
			}
		}
	);

Route::set('login', 'cms/login(/<controller>)')
	->defaults(array(
		'directory' => 'Cms_Auth_Login',
		'controller' => ucfirst(Kohana::$config->load('auth')->get('default_method')),
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