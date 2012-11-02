<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'topbar'	=>	array(
		'home'		=> array(
			'title'		=>	'Home',
			'url'		=>	'/',
			'priority'	=>	1,
		),
		'assets'	=> array(
			'title'		=>	'Assets',
			'url'		=>	'/cms/assets',
			'action'	=>	'manage_assets',
			'priority'	=>	3,
		),
		'people'	=>	array(
			'title'		=>	'People',
			'url'		=>	'/cms/people',
			'action'	=>	'manage_people',
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
			'action'	=>	'manage_templates',
			'priority'	=>	5,
		),
	),
);
