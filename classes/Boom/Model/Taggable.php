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
	public function add_tag_with_path($path, $type, array $ids = array())
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
					$query->values($id, $tag->id);
				}

				// Run the query.
				$query->execute($this->_db);
			}
		}
		catch (Database_Exception $e) {}

		// Return the current object.
		return $this;
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
			->order_by('path', 'asc')
			->find_all();
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
	public function remove_tag_with_path($path, $type, array $ids = array())
	{
		// Object has to be loaded to remove a tag from it.
		if ( ! $this->_loaded)
		{
			throw new Exception("An object has to be loaded to remove a tag from it");
		}

		// Get the tag that has the specified path.
		$tag = new Model_Tag(array('path' => $path, 'type' => $type));

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
}