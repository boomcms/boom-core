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
interface taggable {
	
	/**
	* Method to retrieve all tags which have been applied to the object.
	*
	* @return array Array of Model_Tag objects.
	*/
	abstract function tags();
	
	/**
	* Method to apply a tag to an object.
	*
	* @param Model_Tag $tag The tag to be applied.
	* @return bool True on success, false on failure
	*/
	abstract function apply_tag( Model_Tag $tag );
}

?>