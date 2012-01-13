<?php

/**
* Model for the tagged_objects table.
* This table is used to apply tags to various CMS objects (e.g. assets).
*
* The table has 3 columns which together act as the primary key:
* tag_id - The ID of the tag which is being applied.
* object_type - an integer representing the type of object. These numbers are defined as class constants within this model.
* object_id - The id of the the object that the tag is being applied to.
*
* When a tag is applied to an object that object also 'belongs' to the tag's parent tags through the tag mptt tree.
*
* At present we don't use Kohana's ORM relationships to define the relationships between tags and CMS objects. 
* Doing so would be nice as it would allow us to do things like $page->tags to retrieve a pages tags.
* The reason we don't do this is that we need more flexibility that Kohana provides due to:
* An object inheriting parent tags.
* This table defining relationships between the tag table and many other tables through the object_type column.
*
* @todo The second hurdle could be overcome by having models which inherit from this one, but we'd still have the first problem. Having models inherit from this one would definitely be cool though.
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Tagged_Object extends ORM {
	
	/**
	* The value of the object_type column for relationships with assets.
	*/
	const OBJECT_TYPE_ASSET = 1;
	
}

?>