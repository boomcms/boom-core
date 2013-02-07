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
		'type'		=>	'',
	);

	protected $_table_name = 'tags';

	// The value for the 'type' property for asset tags.
	const ASSET = 1;

	// The value for the 'type' property for page tags.
	const PAGE = 2;

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
	 *
	 * Ensures that all child tags are also deleted.
	 *
	 * @return Model_Tag
	 */
	public function delete()
	{
		// Delete child tags.
		foreach (ORM::factory('Tag')->where('parent_id', '=', $this->id)->find_all() as $tag)
		{
			$tag->delete();
		}

		// Delete any relationships with this tag.
		DB::delete('tags_applied')
			->where('tag_id', '=', $this->id)
			->execute($this->_db);

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
	 * Returns an associative array of tag IDs and names
	 *
	 * The returned array can be passed to Form::select();
	 *
	 * @param integer $type
	 * @return array
	 */
	public function names($type)
	{
		return DB::select('id', 'name')
			->from('tags')
			->where('type', '=', $type)
			->order_by('name', 'asc')
			->execute($this->_db)
			->as_array('id', 'name');
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
	 * Updates the materialized path for child tags.
	 *
	 * This function should be called when a tag is reparented or has it's name changed.
	 * When a tag's path is updated the materialised path for it's children also needs to be updated.
	 *
	 * @return Model_Tag
	 */
	public function update_child_paths()
	{
		// Get the child tags of the current tag.
		$kids = ORM::factory('Tag')
			->where('parent_id', '=', $this->id)
			->find_all();

		// Update the materialised path for all the child tags.
		foreach ($kids as $tag)
		{
			$tag
				->set('path', $path . "/" . $tag->name)
				->update();
		}
	}

	/**
	 * ORM Validation rules
	 *
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

	public function update(\Validation $validation = NULL)
	{
		// Save the changes to the current tag.
		parent::update($validation);

		// Update the materialized path of this tag's child tags.
		$this->update_child_paths();

		// Return the current object.
		return $this;
	}
}
