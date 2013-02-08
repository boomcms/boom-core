<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Base model for assets and pages which provides functions for adding / removing tags.
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
abstract class Boom_Model_Taggable extends ORM
{
	/**
	 * Adds a tag with a given path to the object.
	 *
	 * If the tag doesn't exist then [Model_Tag::create_from_path()] is called to create it.
	 *
	 *
	 * @param string $path
	 * @param integer $type
	 * @return \Boom_Model_Taggable
	 *
	 * @uses Model_Tag::create_from_path()
	 * @throws Exception
	 */
	public function add_tag_with_path($path, $type)
	{
		// If the current page isn't loaded then we can't add a tag to it.
		if ( ! $this->_loaded)
		{
			// Throw an exception
			throw new Exception("Cannot add a tag to an unloaded page");
		}

		// Attempt to load a tag with the given path.
		$tag = ORM::factory('Tag', array('path' => $path, 'type' => $type));

		// If the tag wasn't found then call [Boom_Model_Tag::create_from_path()] to create it.
		if ( ! $tag->loaded())
		{
			// Create the tag.
			$tag = ORM::factory('Tag')->create_from_path($path, $type);
		}

		// Add the tag to the current object.
		$this->add('tags', $tag);

		// Return the current object.
		return $this;
	}

	/**
	 * Removes a tag with the given path from an object.
	 *
	 * @param string $path
	 * @param integer $type
	 *
	 * @return \Boom_Model_Taggable
	 * @throws Exception
	 */
	public function remove_tag_with_path($path, $type)
	{
		// Object has to be loaded to remove a tag from it.
		if ( ! $this->_loaded)
		{
			throw new Exception("An object has to be loaded to remove a tag from it");
		}

		// Remove the tag.
		$this->remove('tags', new Model_Tag(array('path' => $path, 'type' => $type)));

		// Return the current object.
		return $this;
	}
}