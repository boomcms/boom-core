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
			'priority'	=>	2,
		),
		'people'	=>	array(
			'title'		=>	'People',
			'url'		=>	'/cms/people',
			'action'	=>	'manage_people',
			'priority'	=>	3,
		),
		'templates'	=>	array(
			'title'		=>	'Templates',
			'url'		=>	'/cms/templates',
			'action'	=>	'manage_templates',
			'priority'	=>	4,
		),
	),
);
