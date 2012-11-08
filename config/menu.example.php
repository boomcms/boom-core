<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'navigation'	=>	array(
		'view_filename'		=>	'menu/navigation',
		'items'			=>	array(
			'home'		=>	array(
				'title'		=>	'Home',
				'url'		=>	'/',
			),
			'restricted'	=>	array(
				'title'		=>	'Restricted Page',
				'url'		=>	'/goaway',
				'role'		=>	'admin',
			)
		),
	),
);
