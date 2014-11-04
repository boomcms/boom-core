<?php

return array(
	'auth' => array(
		'disabled' => Kohana::$environment === Kohana::DEVELOPMENT,
	),
	'menu'	=>	array(
		'view_filename'		=>	'menu/boom',
		'items'			=>	array(
			'home'		=>	array(
				'title'		=>	'Site',
				'url'		=>	'/',
				'priority'	=>	1,
			),
            'pages' => array(
                'title' => 'Pages',
                'url' => '/cms/pages',
                'priority' => 5,
                'role' => 'manage_pages',
            ),
			'approvals'	=>	array(
				'title'		=>	'Pages pending approval',
				'url'		=>	'/cms/approvals',
				'priority'	=>	10,
				'role'		=>	'manage_approvals',
			),
			'templates'	=>	array(
				'title'		=>	'Templates',
				'url'		=>	'/cms/templates',
				'role'		=>	'manage_templates',
				'priority'	=>	6,
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
		),
	),
);