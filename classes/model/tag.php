<?php

/**
*
* Table name: tag
* This table is versioned!
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description		
****	id				****	integer		****	Primary key. auto increment.			
****	active_vid		****	integer		****	The ID of the current version.
******************************************************************
*
* @see Model_Version_Tag
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Tag extends ORM_Versioned {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'tag';
	protected $_has_one = array( 
		'mptt'		=> array( 'model' => 'tag_mptt' ),
	);

	protected $_belongs_to = array(
		'version'  => array( 'model' => 'version_tag', 'foreign_key' => 'active_vid' ), 
	);
	
	protected $_load_with = array('version');
	
	/**
	* Finds the tag with a given name, creates the tag if it doesn't exist.
	*
	* @param string Tag name
	* @return Model_Tag A tag with the given name.
	*/
	public function find_or_create( $name )
	{
		// Make sure we're not screwing around with an object loaded from the database.
		if ($this->loaded())
		{
			$this->clear();
		}
		
		$this->where( 'version.name', '=', $name )->find();
		
		if (!$this->loaded())
		{
			$this->name = $name;
			$this->save();
		}
		
		return $this;
	}
}

?>
