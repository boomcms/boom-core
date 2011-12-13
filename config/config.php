<?php 

$docroot = explode('/', getcwd());
$instance = explode('.', $docroot[count($docroot)-1]);

return array(
	'core'	=> array (
		'client_name'		=> 'Hoop Associates',
		'environment'		=> 'dev',
		'sledge_name'		=> $instance[0],
		'sledge_instance'	=> $instance[1]
	)
);

?>
