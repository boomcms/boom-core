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
	protected $_belongs_to = array(
		'version'	=> array( 'model' => 'version_tag', 'foreign_key' => 'active_vid' ),
		'mptt'		=> array( 'model' => 'tag_mptt', 'foreign_key' => 'id' ),
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
	
	/**
	* Find a tag by the given route.
	*
	* @example ORM::factory( 'tag' )->find_by_route( 'pages/categories' )
	* @param string $route The route to the tag.
	* @return Model_Tag
	*/
	public function find_by_route( $route )
	{
		// This could be done as a single query, but I think it's probably quicker to do it as individual queries.
		// Something to look at though.
		$parent = null;
		$tags = explode( '/', $route );
		
		foreach( $tags as $tag )
		{
			$query = DB::select( 'tag.id' )
					->from( 'tag' )
					->join( 'tag_v', 'inner' )
					->on( 'active_vid', '=', 'tag_v.id' )
					->join( 'tag_mptt', 'inner' )
					->on( 'tag.id', '=', 'tag_mptt.id' )
					->where( 'parent_id', '=', $parent )
					->where( 'name', '=', $tag )
					->execute()->as_array();
					
			if (count( $query ))
			{
				$parent = $query[0]['id'];
			}
			else
			{
				return false;
			}
		}
		
		return ORM::factory( 'tag', $parent );
	}
}

?>
