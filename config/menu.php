<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'sledge'	=>	array(
		'view_filename'		=>	'menu/sledge',
		'items'			=>	array(
			'home'		=>	array(
				'title'		=>	'Home',
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
			'pages'	=>	array(
				'title'		=>	'Pages',
				'url'		=>	'/cms/pages',
				'priority'	=>	2,
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
