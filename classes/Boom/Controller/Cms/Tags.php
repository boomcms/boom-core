<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controllers for editing tags through the asset manager.
 *
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Tags extends Boom_Controller
{
	/**
	 * Directory where view files are stored for this class.
	 *
	 * @var string
	 */
	protected $_view_directory = 'boom/tags';

	/**
	 *
	 * @var Model_Tag
	 */
	public $tag;

	/**
	 *
	 * @uses Boom_Controller::authorization()
	 */
	public function before()
	{
		parent::before();

		// Permissions check.
		// The manage assets permission is required because all these functions are accessed from the asset manager.
		$this->authorization('manage_assets');

		$this->tag = new Model_Tag($this->request->param('id'));
	}

	/**
	 * Delete a tag.
	 *
	 * Deletes the current tag identified by the route's ID parameter.
	 *
	 * @example	http://site.com/cms/tags/delete/1
	 * @todo		Only allows for deleting a single tag. Do we need to be able to multi-delete tags?
	 * @uses		Model_Tag::delete()
	 */
	public function action_delete()
	{
		$this->log("Deleted tag " . $this->tag->name . " (ID: " . $this->tag->id . ")");
		$this->tag->delete();
	}

	/**
	 * Save a tag.
	 *
	 * Used when editing an existing tag or adding a new tag.
	 */
	public function action_save()
	{
		// Get the ID of the parent tag.
		$parent_id = $this->request->post('parent_id');

		$this->tag->values(array(
			'name'		=>	 $this->request->post('name'),
			'type'		=>	1,
			'parent_id'	=>	$parent_id,
		));

		// Update the tag's materialised path
		// This needs to be done after changing a tag's parent ID or name.
		$parent = new Model_Tag($parent_id);
		$this->tag->path = ($parent->path)? $parent->path."/".$this->tag->name : $this->tag->name;

		// Save the tag.
		$this->tag->save();

		// Log the action.
		$this->log("Updated tag " . $this->tag->name . " (ID: " . $this->tag->id . ")");
	}
}