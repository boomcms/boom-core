<?php

class Model_Chunk_Asset extends \ORM
{
	protected $_belongs_to = array(
		'target' => array('model' => 'Asset', 'foreign_key' => 'asset_id'),
	);

	protected $_table_columns = array(
		'id'			=>	'',
		'asset_id'		=>	'',
		'title'			=>	'',
		'caption'		=>	'',
		'url'			=>	'',
		'slotname'		=>	'',
		'page_vid' => '',
	);

	protected $_table_name = 'chunk_assets';

	public function filters()
	{
		return array(
			'title'	=> array(
				array('strip_tags'),
			),
			'caption'	=> array(
				array('strip_tags'),
			),
		);
	}
}