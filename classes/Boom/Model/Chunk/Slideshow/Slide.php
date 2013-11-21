<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Holds a list of images which belong to a slideshow slot.
 *
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Model_Chunk_Slideshow_Slide extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
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
	 * Whether the current slide has a link associated with it.
	 *
	 * @return boolean
	 */
	public function has_link()
	{
		return $this->url AND $this->url != 'http://';
	}

	public function make_link_relative($url)
	{
		return ($base = URL::base(Request::current()))? str_replace($base, '/', $url) : $url;
	}
}