<?php

/**
* Slideshow Image model
* Holds a list of images which belong to a slideshow slot.
*
* Table name: slideshowimages
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	asset_id		****	integer		****	ID in the asset table of the image.
****	url				****	string		****	URL that the image should link to.
****	chunk_id		****	integer		****	The ID of the slideshow chunk that this slide belongs to.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Slideshowimage extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'slideshowimages';	
	
	protected $_has_one = array(
		'asset'	=> array( 'model' => 'asset', 'foreign_key' => 'id')
	);
}

?>