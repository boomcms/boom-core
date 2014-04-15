<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'AutoFormat.AutoParagraph'				=>	true,
	'AutoFormat.RemoveEmpty.RemoveNbsp'		=>	true,
	'AutoFormat.RemoveEmpty'				=>	true,
	'AutoFormat.RemoveSpansWithoutAttributes'	=>	true,
	'Core.RemoveInvalidImg'					=>	FALSE,
	'Cache.SerializerPath'					=>	APPPATH.'cache',
	'CSS.AllowedProperties'					=>	array(),
	'URI.AllowedSchemes'					=>	array (
		'http'		=> true,
		'https'	=> true,
		'mailto'	=> true,
		'tel'		=> true,
		'hoopdb'	=> true,
		'ftp'		=> true,
	),
);