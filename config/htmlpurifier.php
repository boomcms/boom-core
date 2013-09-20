<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'AutoFormat.AutoParagraph'				=>	TRUE,
	'AutoFormat.RemoveEmpty.RemoveNbsp'		=>	TRUE,
	'AutoFormat.RemoveEmpty'				=>	TRUE,
	'AutoFormat.RemoveSpansWithoutAttributes'	=>	TRUE,
	'Core.RemoveInvalidImg'					=>	FALSE,
	'Cache.SerializerPath'					=>	APPPATH.'cache',
	'CSS.AllowedProperties'					=>	array(),
	'URI.AllowedSchemes'					=>	array (
		'http'		=> TRUE,
		'https'	=> TRUE,
		'mailto'	=> TRUE,
		'tel'		=> TRUE,
		'hoopdb'	=> TRUE,
		'ftp'		=> TRUE,
	),
);