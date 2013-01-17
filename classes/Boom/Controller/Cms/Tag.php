<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tag controller
 * Pages for managing tags.
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Tag extends Boom_Controller
{
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
	 * @example	http://site.com/cms/tag/delete/1
	 * @todo		Only allows for deleting a single tag. Do we need to be able to multi-delete tags?
	 * @uses		Model_Tag::delete()
	 */
	public function action_delete()
	{
		$this->log("Deleted tag " . $this->tag->name . " (ID: " . $this->tag->id . ")");
		$this->tag->delete();
	}

	/**
	 * Display the edit tag form.
	 */
	public function action_edit()
	{
		$this->template = View::factory('boom/tags/edit', array(
			'type'	=>	$this->request->query('type'),
			'tag'		=>	$this->tag,
		));
	}

	/**
	 * Generate a tag tree.
	 * Can be passed a parent tag in the form tag1/tag2/tag3 in the post variables to show only that subtree.
	 * Also accepts a state (collapsed or expanded) in the post variables.
	 */
	public function action_tree()
	{
		$parent = Request::current()->post('parent');

		$query = DB::select('tags.id', 'tags.name', 'tags.parent_id')
			->from('tags');

		if ($parent)
		{
			$parent = new Model_Tag(array('path' => $parent));
			$query->where('path', 'like', $parent->path . '/%');
		}

		if ($this->request->post('type'))
		{
			$query->where('type', '=', $this->request->post('type'));
		}

		$results = $query
			->execute()
			->as_array();

		$tags = array();

		foreach ($results as $result)
		{
			$p = (int) $result['parent_id'];
			$tags[$p][$result['id']] = $result['name'];
		}

		$this->template = View::factory('boom/tags/tree', array(
			'tags'	=>	$tags,
			'root'		=>	(is_object($parent))? $parent->id : 0,
			'state'	=>	Arr::get(Request::current()->post(), 'state', 'collapsed'),
		));

		if ($this->request->param('id'))
		{
			$this->template->current = new Model_Tag($this->request->param('id'));
		}
	}

	/**
	 * Save a tag.
	 * Used when editing an existing tag and adding a new tag.
	 *
	 * **Accepted POST variables:**
	 * Name		|	Type		|	Description
	 * ---------------|-----------------|---------------
	 * name		|	string	|	The name of the tag.
	 * parent_id	|	int		|	The tag ID of this tag's new parent.
	 *
	 */
	public function action_save()
	{
		$this->tag->name = $this->request->post('name');

		if ($this->request->post('type'))
		{
			$this->tag->type = $this->request->post('type');
		}

		$parent = $this->request->post('parent_id');

		if ($parent != $this->tag->parent_id)
		{
			$this->tag->parent_id = $parent;
		}

		$this->tag->save();

		// Update the tags materialised path.
		$parent = new Model_Tag($parent);

		if ($parent->path)
		{
			$this->tag->path($parent->path . "/" . $this->tag->name);
		}
		else
		{
			$this->tag->path($this->tag->name);
		}

		$this->log("Updated tag " . $this->tag->name . " (ID: " . $this->tag->id . ")");
	}
}
