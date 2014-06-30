<?php

namespace Boom\Model;

use \DB as DB;
use \Exception as Exception;
use \ORM as ORM;

abstract class Taggable extends ORM
{
	/**
	 * Adds a tag with a given name to the object.
	 *
	 * If the tag doesn't exist then it will be created.
	 *
	 * @return \Boom_Model_Taggable
	 *
	 */
	public function add_tag_with_name($name, array $ids = array())
	{
		if ( ! $this->_loaded && empty($ids))
		{
			throw new Exception("Cannot add a tag to an unloaded object");
		}

		$tag = new Tag(array('name' => $name));

		if ( ! $tag->loaded())
		{
			$tag = ORM::factory('Tag')
				->values(array(
					'name'	=>	$name,
				))
				->create();
		}

		if (empty($ids))
		{
			$this->add('tags', $tag);
		}
		else
		{
			foreach ($ids as $id)
			{
				try
				{
					// Have to do this as individual queries rather than a single query with multiple values incase the tag is already applied to some of the objects.
					DB::insert($this->_object_plural.'_tags', array($this->_object_name.'_id', 'tag_id'))
						->values(array($id, $tag->id))
						->execute($this->_db);
				}
				catch (Database_Exception $e) {}
			}
		}

		return $this;
	}

	public function has_tags()
	{
		$result = DB::select(DB::expr('1'))
			->from($this->_object_plural.'_tags')
			->where($this->_object_name.'_id', '=', $this->id)
			->limit(1)
			->execute()
			->as_array();

		return ! empty($result);
	}

	/**
	 * Get the tags which are applied to a group of objects.
	 *
	 * @param array $object_ids
	 * @return Database_Result
	 */
	public function list_tags(array $object_ids)
	{
		$join_table = $this->_object_plural.'_tags';
		$join_table_id_column = $this->_object_name.'_id';

		return ORM::factory('Tag')
			->join(array($join_table, 't1'), 'inner')
			->on('t1.tag_id', '=', 'tag.id')
			->join(array($join_table, 't2'), 'inner')
			->on('t1.'.$join_table_id_column, '=', 't2.'.$join_table_id_column)
			->where('t2.'.$join_table_id_column, 'IN', $object_ids)
			->group_by('tag.id')
			->having(DB::expr('count(distinct t2.'.$join_table_id_column.')'), '>=', count($object_ids))
			->distinct(true)
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
		// Object has to be loaded to remove a tag from it.
		if ( ! $this->_loaded && empty($ids))
		{
			throw new Exception("An object has to be loaded to remove a tag from it");
		}

		// Get the tag that has the specified path.
		$tag = new Model_Tag(array('name' => $name));

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