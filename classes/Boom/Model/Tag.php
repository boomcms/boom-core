<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package	BoomCMS
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Model_Tag extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'path'		=>	'',
		'name'		=>	'',
		'parent_id'	=>	'',
		'type'		=>	'',
	);

	protected $_table_name = 'tags';

	/**
	 * Creates a tag with a given path.
	 *
	 * @param string $path
	 * @param integer $type
	 * @return \Boom_Model_Tag
	 */
	public function create_from_path($path, $type)
	{
		// If the currnent object is loaded then clear it so that we can create a new tag.
		if ($this->_loaded)
		{
			$this->clear();
		}

		// Split the path into bits so we can find the parent and name of the tag.
		$parts = explode('/', $path);

		// Name of the tag is the last section of the path.
		$name = array_pop($parts);

		// Put the remaining parts back together to get the path of the parent tag.
		$parent_path = implode("/", $parts);

		// Find the parent tag.
		$parent = new Model_Tag(array('path' => $parent_path, 'type' => $type));

		// Set the tag's values.
		$this->values(array(
			'path'	=>	$path,
			'name'	=>	$name,
			'type'	=>	$type,
		));

		// If the parent tag was found then set the parent ID of the current tag.
		// We don't recursively create tags.
		if ($parent->loaded())
		{
			$this->set('parent_id', $parent->id);
		}

		// Create the tag.
		$this->create();

		// Return the current tag.
		return $this;
	}

	/**
	 * Delete a tag.
	 * Ensures child tags are deleted and that the tags are deleted from the MPTT tree.
	 *
	 * @return ORM
	 */
	public function delete()
	{
		// Delete child tags.
		foreach (ORM::factory('Tag')->where('parent_id', '=', $this->id)->find_all() as $t)
		{
			$t->delete();
		}

		// Delete any relationships with this tag.
		DB::delete("delete from tags_applied where tag_id = " . $this->id);

		// Delete the tag.
		return parent::delete();
	}

	/**
	* Filters for the versioned person columns
	* @link http://kohanaframework.org/3.2/guide/orm/filters
	*/
	public function filters()
	{
	    return array(
			'name' => array(
				array('trim'),
			),
	   );
	}

	/**
	 * Return the parent tag of the current tag.
	 *
	 * @return 	Model_Tag
	 */
	public function parent()
	{
		return new Model_Tag($this->parent_id);
	}

	/**
	 * Set the materialised path for a tag.
	 * This function should be called when a tag is reparented or has it's name changed.
	 * When a tag's path is updated the materialised path for it's children also needs to be updated.
	 *
	 * @param 	string	The new materialised path for the tag.
	 * @return 	Model_Tag
	 */
	public function path($path)
	{
		// Set the new path for the current tag.
		$this->path = $path;
		$this->update();

		// Get the child tags of the current tag.
		$kids = ORM::factory('Tag')
			->where('parent_id', '=', $this->id)
			->find_all();

		// Update the materialised path for all the child tags.
		foreach ($kids as $tag)
		{
			$tag->path($path . "/" . $tag->name);
		}
	}

	/**
	* ORM Validation rules
	* @link http://kohanaframework.org/3.2/guide/orm/examples/validation
	*/
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
			),
		);
	}
}
