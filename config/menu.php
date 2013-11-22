<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'boom'	=>	array(
		'view_filename'		=>	'menu/boom',
		'items'			=>	array(
			'home'		=>	array(
				'title'		=>	'Site',
				'url'		=>	'/',
				'priority'	=>	1,
			),
			'profile'		=>	array(
				'title'		=>	'Profile',
				'url'		=>	'/cms/profile',
				'priority'	=>	99,
			),
			'logout'		=>	array(
				'title'		=>	'Logout',
				'url'		=>	'/cms/logout',
				'priority'	=>	100,
			),
		),
	),
);
