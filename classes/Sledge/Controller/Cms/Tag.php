<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tag controller
 * Pages for managing tags.
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Controller_Cms_Tag extends Sledge_Controller
{
	protected $tag;

	public function before()
	{
		parent::before();

		if ( ! $this->auth->logged_in('manage_tags'))
		{
			throw new HTTP_Exception_403;
		}

		$this->tag = ORM::factory('Tag', $this->request->param('id'));
	}

	public function action_autocomplete()
	{
		$count	=	($this->request->post('count') > 0)? $this->request->post('count') : 10;
		$name	=	$this->request->query('name');
		$type	=	$this->request->query('type');

		$results = DB::select('tags.path')
			->from('tags')
			->where('path', 'like', "%$name%")
			->where('type', '=', $type)
			->order_by('path', 'asc')
			->limit($count)
			->execute()
			->as_array('path');

		$this->response
			->headers('content-type', 'application/json')
			->body(json_encode(array_keys($results)));
	}

	/**
	 * Display the child tags of a given tag.
	 * Very similar to action_tree except that only descendants one level below are displayed.
	 * This function also uses the parent_id column in the tag table rather than the tag_mptt table.
	 * This is intended as an eventual replacement to the tag_mptt approach as the tag_mptt table will eventually be removed.
	 */
	public function action_children()
	{
		$parent = Request::current()->post('parent');

		if ($parent)
		{
			$parent = ORM::factory('Tag', array('path' => $parent));
		}
		else
		{
			$parent = ORM::factory('Tag')
				->where('parent_id', '=', NULL)
				->where('deleted', '=', FALSE)
				->find();
		}

		$tags = DB::select('tags.id')
			->from('tags')
			->where('parent_id', '=', $parent->id)
			->execute()
			->as_array();

		foreach ($tags as & $tag)
		{
			$tag = ORM::factory('Tag', $tag['id']);
		}

		$this->template = View::factory('sledge/tags/children', array(
			'tags'	=>	$tags,
		));
	}

	/**
	 * Delete a tag.
	 * Deletes the current tag identified by the route's ID parameter.
	 *
	 * @example	http://site.com/cms/tag/delete/1
	 * @todo		Only allows for deleting a single tag. Do we need to be able to multi-delete tags?
	 * @uses		Model_Tag::delete()
	 */
	public function action_delete()
	{
		$this->_log("Deleted tag " . $this->tag->name . " (ID: " . $this->tag->id . ")");
		$this->tag->delete();
	}

	/**
	 * Display the edit tag form.
	 */
	public function action_edit()
	{
		$this->template = View::factory('sledge/tags/edit', array(
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
			$parent = ORM::factory('Tag', array('path' => $parent));
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

		$this->template = View::factory('sledge/tags/tree', array(
			'tags'	=>	$tags,
			'root'		=>	(is_object($parent))? $parent->id : 0,
			'state'	=>	Arr::get(Request::current()->post(), 'state', 'collapsed'),
		));

		if ($this->request->param('id'))
		{
			$this->template->current = ORM::factory('Tag', $this->request->param('id'));
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
		$parent = ORM::factory('Tag', $parent);

		if ($parent->path)
		{
			$this->tag->path($parent->path . "/" . $this->tag->name);
		}
		else
		{
			$this->tag->path($this->tag->name);
		}

		$this->_log("Updated tag " . $this->tag->name . " (ID: " . $this->tag->id . ")");
	}
}
