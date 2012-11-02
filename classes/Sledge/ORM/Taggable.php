<?php defined('SYSPATH') OR die('No direct script access.');

/**
* ORM functions for taggable objects.
* @package Sledge
* @category ORM
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Sledge_ORM_Taggable extends ORM_Versioned
{	
	/**
	* Adds a tag to an object.
	* Kohana relationships can't be used for the tag relationships because the tags_applied table holds relationships with tags and assets, tags and pages etc.
	* This function therefore creates a relationship using the tags_applied table if $alias = 'tag'.
	* If not it sends it to Kohana as normal.
	*/
	public function add($alias, $far_keys)
	{
		if ($alias == 'tag')
		{
			if ($far_keys instanceof ORM)
			{
				$far_keys = $far_keys->pk();
			}

			$query = DB::insert('tags_applied', array('object_type', 'object_id', 'tag_id'));
			$object_type = $this->get_object_type_id();
			
			foreach ( (array) $far_keys as $key)
			{		
				$query->values( array($object_type, $this->pk(), $key));
			}

			$query->execute();
			return $this;
		}
		else
		{
			return parent::add($alias, $far_keys);
		}
	}
	
	/**
	 * Find all tags associates with this object.
	 *
	 * @param string $parent Path of a parent tag.
	 * @param boolean $root Whether to resrict tags to the root level.
	 */
	public function get_tags($parent = NULL, $root = TRUE)
	{
		$query = ORM::factory('Tag')
			->join('tags_applied', 'inner')
			->on('tags_applied.tag_id', '=', 'tag.id')
			->where('tags_applied.object_type', '=', $this->get_object_type_id())
			->where('tags_applied.object_id', '=', $this->id);

		if ($parent !== NULL)
		{
			$query->where('tag.path', 'like', $parent . '/%');
		}
		elseif ($root === TRUE)
		{
			$query->where('tags.parent_id', '=', NULL);
		}

		return $query
			->find_all()
			->as_array();
	}

	/**
	* Provides a shortcut to searching for an object by tag.
	*/
	public function where($column, $op, $value)
	{
		if ($column == 'tag' AND $op == '=')
		{
			$this->where_tag($value);

		}
		else
		{
			parent::where($column, $op, $value);
		}
		
		return $this;
	}
	
	/**
	* Sets up the necessary joins / wheres to find objects by what they're tagged.
	*/
	public function where_tag($value)
	{
		// Get the tag ID we're searching for.
		if ($value instanceof Model_Tag)
		{
			$tag = $value;
		}
		elseif (ctype_digit($value))
		{
			$tag = ORM::factory('Tag', $value);
		}
		else
		{
			$tag = ORM::factory('Tag', array('path' => $value));
		}
		
		// Get the tag's mptt values.
		// Find by tag inherits from children.
		// So if something is tagged with a child of the tag we're called with
		// We should also find that object.
		$this->join('tags_applied', 'inner')
			->on('tags_applied.object_id', '=', $this->_table_name . "." . $this->_primary_key)
			->join('tag', 'inner')
			->on('tags_applied.tag_id', '=', 'tags.id')
			->where('tags_applied.object_type', '=', $this->get_object_type_id())
			->where('tags.path', 'like', $tag->path . '%');
			
		return $this;
	}
	
	/**
	* Works out the object ID to be used in the tagged_object table.
	*
	* @return int
	*/
	public function get_object_type_id()
	{
		if ($this instanceof Model_Page)
		{
			$object_type =Model_Tag_Applied::OBJECT_TYPE_PAGE;
		}
		elseif ($this instanceof Model_Asset)
		{
			$object_type =Model_Tag_Applied::OBJECT_TYPE_ASSET;
		}
		else
		{
			// Non-taggable object. This will cause us to find nothing.
			$object_type = 0;
		}
		
		return $object_type;
	}
}
