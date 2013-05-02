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
	 * Adds a tag with a given name to the object.
	 *
	 * If the tag doesn't exist then it will be created.
	 *
	 *
	 * @param string $name
	 * @param integer $type
	 * @return \Boom_Model_Taggable
	 *
	 * @throws Exception
	 */
	public function add_tag_with_name($name, array $ids = array())
	{
		// Determine the type of tag to add.
		$type = $this->tag_type();

		// If the current page isn't loaded then we can't add a tag to it.
		if ( ! $this->_loaded AND empty($ids))
		{
			// Throw an exception
			throw new Exception("Cannot add a tag to an unloaded object");
		}

		// Attempt to load a tag with the given path.
		$tag = ORM::factory('Tag', array('name' => $name, 'type' => $type));

		// If the tag wasn't found then call create it.
		if ( ! $tag->loaded())
		{
			// Create the tag.
			$tag = ORM::factory('Tag')
				->values(array(
					'name'	=>	$name,
					'type'	=>	$type,
				))
				->create();
		}

		// Add the tag to the objects?
		try
		{
			// Were we called with an array of object IDs?
			if (empty($ids))
			{
				// No, add the tag to the current object.
				$this->add('tags', $tag);
			}
			else
			{
				// An array of object IDs was given - add the tag to all of the given objects.
				$query = DB::insert($this->_object_plural.'_tags', array($this->_object_name.'_id', 'tag_id'));

				foreach ($ids as $id)
				{
					$query->values(array($id, $tag->id));
				}

				// Run the query.
				$query->execute($this->_db);
			}
		}
		catch (Database_Exception $e) {}

		// Return the current object.
		return $this;
	}

	public function get_tags_with_name_like($name)
	{
		return $this->tags
				->where('name', 'like', $name)
				->find_all()
				->as_array();
	}

	/**
	 * Get the tags which are applied to a group of objects.
	 *
	 * @param array $object_ids
	 * @return Database_Result
	 */
	public function list_tags(array $object_ids)
	{
		return ORM::factory('Tag')
			->join($this->_object_plural.'_tags', 'inner')
			->on('tag_id', '=', 'tag.id')
			->where($this->_object_name.'_id', 'in', $object_ids)
			->order_by('name', 'asc')
			->distinct(TRUE)
			->find_all();
	}

	/**
	 * Removes a tag with the given name from an object.
	 *
	 * @param string $name
	 * @param integer $type
	 *
	 * @return \Boom_Model_Taggable
	 * @throws Exception
	 */
	public function remove_tag_with_name($name, array $ids = array())
	{
		// Determine the type of tag to remove.
		$type = $this->tag_type();

		// Object has to be loaded to remove a tag from it.
		if ( ! $this->_loaded AND empty($ids))
		{
			throw new Exception("An object has to be loaded to remove a tag from it");
		}

		// Get the tag that has the specified path.
		$tag = new Model_Tag(array('name' => $name, 'type' => $type));

		// If the tag doesn't exist then don't continue.
		if ( ! $tag->_loaded)
		{
			return $this;
		}

		// Remove the tag.
		if (empty($ids))
		{
			// Remove the tag from a single object.
			$this->remove('tags', $tag);
		}
		else
		{
			// Remove the tag from multiple objects.
			$query = DB::delete($this->_object_plural.'_tags')
				->where('tag_id', '=', $tag->id)
				->where($this->_object_name.'_id', 'in', $ids)
				->execute($this->_db);
		}

		// Return the current object.
		return $this;
	}

	/**
	 * Return the value for the tag type column so that page and asset tags can be kept seperate.
	 *
	 * @return type
	 */
	public function tag_type()
	{
		return ($this instanceof Model_Asset)? Model_Tag::ASSET : Model_Tag::PAGE;
	}
}