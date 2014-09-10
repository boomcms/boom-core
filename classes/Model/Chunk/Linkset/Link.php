<?php

use \Boom\Link as Link;

class Model_Chunk_Linkset_Link extends ORM
{
	protected $_link;

	protected $_belongs_to = array(
		'target'	=> array('model' => 'page', 'foreign_key' => 'target_page_id')
	);

	protected $_table_columns = array(
		'id'				=>	'',
		'target_page_id'	=>	'',
		'chunk_linkset_id'	=>	'',
		'url'				=>	'',
		'title'				=>	'',
	);

	protected $_table_name = 'chunk_linkset_links';

	public function getLink()
	{
		if ($this->_link === null) {
			// TODO: store internal links in url property and let \Boom\Link do all the work.
			$url = $this->_target_page_id > 0? $this->_target_page_id : $this->_url;
			$this->_link = Link::factory($url);
		}

		return $this->_link;
	}

	public function isInternal()
	{
		return $this->getLink()->isInternal();
	}

	public function isExternal()
	{
		return ! $this->isInternal();
	}
}