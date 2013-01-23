<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'boom'	=>	array(
		'view_filename'		=>	'menu/boom',
		'items'			=>	array(
			'pages'	=>	array(
				'title'		=>	'Pages',
				'url'		=>	'/cms/pages',
				'priority'	=>	2,
				'role'		=>	'manage_pages',
			),
		),
	),
);