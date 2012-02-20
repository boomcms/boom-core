<?php

/*
* Interface for taggle items.
* Forces all CMS objects (pages, assets, etc.) which may have tags applied to them to share a common interface for working with tags.
*
* @package Interface
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
interface Interface_Taggable
{
	
	/**
	* Method to retrieve all tags which have been applied to the object.
	*
	* @param string $under Find the tags in a specific sub-tree.
	* @return array Array of Model_Tag objects.
	*/
	public function get_tags( $under = null );
	
	/**
	* As above but limits results to certain columns
	*
	* @param array $columns Array of column names to return.
	* @param string $under Find the tags in a specific sub-tree.
	* @return array Array of tag names.
	*/
	public function get_tag_columns( array $columns, $under );	
	
	/**
	* Method to apply a tag to an object.
	*
	* @param Model_Tag $tag The tag to be applied.
	* @return bool True on success, false on failure
	*/
	public function apply_tag( Model_Tag $tag );
}

?>