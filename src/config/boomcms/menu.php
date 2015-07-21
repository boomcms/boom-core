<?php

return [
	'home'  => [
		'title'  => 'Site',
		'url'  => '/',
		'priority' => 1,
        'icon' => 'home',
	],
	'pages' => [
		'title' => 'Pages',
		'url' => '/cms/pages',
		'priority' => 5,
		'role' => 'manage_pages',
        'icon' => 'sitemap',
	],
	'approvals' => [
		'title'  => 'Pages pending approval',
		'url'  => '/cms/approvals',
		'priority' => 10,
		'role'  => 'manage_approvals',
        'icon' => 'thumbs-o-up',
	],
	'templates' => [
		'title'  => 'Templates',
		'url'  => '/cms/templates',
		'role'  => 'manage_templates',
		'priority' => 6,
        'icon' => 'file-o',
	],
	'profile'  => [
		'title'  => 'Manage Account',
		'url'  => '/cms/account',
		'priority' => 99,
        'icon' => 'user',
	],
	'logout'  => [
		'title'  => 'Logout',
		'url'  => '/cms/logout',
		'priority' => 100,
        'icon' => 'sign-out',
	],
	'assets' => [
		'title'  => 'Assets',
		'url'  => '/cms/assets',
		'role'  => 'manage_assets',
		'priority' => 3,
        'icon' => 'picture-o'
	],
	'people' => [
		'title'  => 'People',
		'url'  => '/cms/people',
		'role'  => 'manage_people',
		'priority' => 4,
        'icon' => 'users'
	],
];