<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Holds a list of images which belong to a slideshow slot.
 *
 * **Table name**: chunk_slideshow_slides
 *
 * Name      	| Type		| Description
 * ---------------|-----------------|------------------------------------------------
 * id			|	integer	|	Primary key, auto increment
 * asset_id	|	integer	|	ID in the asset table of the image.
 * url			|	string	|	URL that the image should link to.
 * chunk_id	|	integer	|	The ID of the slideshow chunk that this slide belongs to.
 * caption		|	string	|	
 * title		|	string	|
 *
 *
 * @package	Sledge
 * @category	Chunks
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Sledge_Model_Chunk_Slideshow_Slide extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_one = array(
		'asset'	=>	array('model' => 'asset', 'foreign_key' => 'id')
	);
	protected $_table_columns = array(
		'id'		=>	'',
		'asset_id'	=>	'',
		'url'		=>	'',
		'chunk_id'	=>	'',
		'caption'	=>	'',
		'title'		=>	'',
	);
}