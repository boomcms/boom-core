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
		'action' => 'show_form',
	))
	->filter(function(Route $route, $params, Request $request)
		{
			if ($request->method() === Request::POST && ! $request->query('token'))
			{
				$params['action'] = 'create_token';
				return $params;
			}
			else if ($request->query('token'))
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

Route::set('people-manager', 'cms/people')
	->defaults(array(
		'controller' => 'Cms_PeopleManager',
		'action' => 'index',
	));

Route::set('people-edit', 'cms/<controller>(/<action>(/<id>))', array(
		'controller' => 'person|group',
		'id' => '\d+'
	))
	->defaults(array(
		'action' => 'index',
	))
	->filter(function(Route $route, $params, Request $request) {
		$params['controller'] = 'Cms_'.ucfirst($params['controller'])."_";
		$params['controller'] .= ($request->method() == Request::GET)? 'View' : 'Save';

		return $params;
	});

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

			if ($request->query('openid_mode') || ($params['controller'] == 'Password' && $request->method() == Request::POST))
			{
				$params['action'] = 'process';
			}

			return $params;
		});