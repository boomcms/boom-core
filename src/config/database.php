<?php 

return array(
	'default' => array(
		'type' => 'mysql',
		'connection' => array(
			'hostname' => '@db.host@',
			'username' => '@db.user@',
			'password' => '@db.password@',
			'persistent' => true,
			'database' => '@db.name@',
		),
		'primary_key'  => 'id',   // Column to return from INSERT queries, see #2188 and #2273
		'schema'       => '',
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),

	'old' => array(
 	   'type' => 'postgresql',
	    'connection' =>	array(
	    	'hostname' => 'localhost',
	    	'username' => 'hoopster',
	    	'password' => 'goufotion',
	    	'persistent' => true,
	    	'database' => 'hoop_live',
	    ),

		'primary_key'  => 'id',   // Column to return from INSERT queries, see #2188 and #2273
		'schema'       => '',
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
);
?>
