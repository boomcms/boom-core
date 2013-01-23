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
			'assets'	=> array(
				'title'		=>	'Assets',
				'url'		=>	'/cms/assets',
				'role'		=>	'manage_assets',
				'priority'	=>	3,
			),
			'people'	=>	array(
				'title'		=>	'People',
				'url'		=>	'/cms/people',
				'role'		=>	'manage_people',
				'priority'	=>	4,
			),
			'templates'	=>	array(
				'title'		=>	'Templates',
				'url'		=>	'/cms/templates',
				'role'		=>	'manage_templates',
				'priority'	=>	5,
			),
		),
	),
);
