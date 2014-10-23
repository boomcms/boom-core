<?php

use \Boom\Link\Link as Link;

class Model_Chunk_Slideshow_Slide extends \ORM
{
	protected $_belongs_to = array(
		'asset'	=>	array('model' => 'Asset', 'foreign_key' => 'asset_id')
	);
	protected $_table_columns = array(
		'id'		=>	'',
		'asset_id'	=>	'',
		'url'		=>	'',
		'chunk_id'	=>	'',
		'caption'	=>	'',
		'title'		=>	'',
	);

	protected $_table_name = 'chunk_slideshow_slides';

	public function filters()
	{
		return array(
			'caption' => array(
				array('strip_tags'),
			),
			'url' => array(
				array(array($this, 'makeLinkLelative')),
			),
		);
	}

	public function getAsset()
	{
		return \Boom\Asset::factory($this->asset);
	}

	/**
	 * @return Link
	 */
	public function getLink()
	{
		return Link::factory($this->url);
	}

	/**
	 * Whether the current slide has a link associated with it.
	 *
	 * @return boolean
	 */
	public function hasLink()
	{
		return $this->url && $this->url != 'http://';
	}

	public function makeLinkLelative($url)
	{
		return ($base = \URL::base(\Request::current()))? str_replace($base, '/', $url) : $url;
	}
}