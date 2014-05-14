<?php

namespace Boom\Model\Chunk\Slideshow;

use \Boom\Link as Link;

class Slide extends \ORM
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
				array(array($this, 'make_link_relative')),
			),
		);
	}

	/**
	 * @return Link
	 */
	public function get_link()
	{
		return Link::factory($this->url);
	}

	/**
	 * Whether the current slide has a link associated with it.
	 *
	 * @return boolean
	 */
	public function has_link()
	{
		return $this->url && $this->url != 'http://';
	}

	public function make_link_relative($url)
	{
		return ($base = \URL::base(\Request::current()))? str_replace($base, '/', $url) : $url;
	}
}