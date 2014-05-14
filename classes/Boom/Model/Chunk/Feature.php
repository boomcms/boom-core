<?php

namespace Boom\Model\Chunk;

class Feature extends \ORM
{
	protected $_table_columns = array(
		'id'				=>	'',
		'target_page_id'	=>	'',
		'slotname'			=>	'',
		'page_vid' => '',
	);

	protected $_belongs_to = array('target' => array('model' => 'Page', 'foreign_key' => 'target_page_id'));

	protected $_table_name = 'chunk_features';
}