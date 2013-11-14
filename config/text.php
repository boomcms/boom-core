<?php

/**
 * Config for boom text chunks
 */
return array(
	/**
	 * Rules to apply when cleaning text chunks
	 * See Model_Chunk_Text::clean_text()
	 */
	'clean' => array(
		'slotname' => array(
			'standfirst' => array('strip_tags'),
		),
		'is_block' => array(
			1 => array(function($text) {
				require_once Kohana::find_file('vendor', 'htmlpurifier/library/HTMLPurifier.auto');

				$config = HTMLPurifier_Config::createDefault();
				$config->loadArray(Kohana::$config->load('htmlpurifier'));

				$purifier = new HTMLPurifier($config);
				return $purifier->purify($text);
			}),
			0 => array(function($text) {
				return strip_tags($text, '<b><i><a>');
			}),
		)
	),
);