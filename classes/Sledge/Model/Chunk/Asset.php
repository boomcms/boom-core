<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Model for the asset chunk table.
*
* @package	Sledge
* @category	Chunks
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Chunk_Asset extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_primary_key = 'chunk_id';
	protected $_belongs_to = array( 
		'asset' => array('model' => 'Asset', 'foreign_key' => 'asset_id'),
	);
	protected $_table_columns = array(
		'chunk_id'	=>	'',
		'asset_id'	=>	'',
		'title'		=>	'',
		'caption'	=>	'',
		'url'		=>	'',
	);

	public function preview($template)
	{
		$v = View::factory("site/slots/asset/$template");
	
		// If the URL is just a number then assume it's the page ID for an internal link.
		if (preg_match('/^\d+$/D', $this->url))
		{
			$target = ORM::factory('Page', $this->url);
			$v->title = $target->title;
			$v->url = $target->url();
		}
		else
		{
			$v->title = $this->title;
			$v->url = $this->url;
		}
	
		$v->asset = $this->asset;
		$v->caption = $this->caption;

		return $v;
	}
}
