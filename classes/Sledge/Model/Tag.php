<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Tag extends ORM
{
	protected $_table_columns = array(
		'id'			=>	'',
		'path'		=>	'',
		'name'		=>	'',
		'parent_id'	=>	'',
		'type'		=>	'',
	);

	protected $_cache_columns = array('path');

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
		return ORM::factory('Tag', $this->parent_id);
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
