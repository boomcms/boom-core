<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Model_Page extends ORM
{
	/**
	 * Properties to create relationships with Kohana's ORM
	 */
	protected $_belongs_to = array(
		'mptt'		=>	array('model' => 'Page_MPTT', 'foreign_key' => 'id'),
	);

	protected $_created_column = array(
		'column'	=>	'created_time',
		'format'	=>	TRUE,
	);

	protected $_has_one = array(
		'version'		=>	array('model' => 'Page_Version', 'foreign_key' => 'page_id'),
	);

	protected $_has_many = array(
		'versions'	=> array('model' => 'Page_Version', 'foreign_key' => 'page_id'),
		'urls'		=> array('model' => 'Page_URL', 'foreign_key' => 'page_id'),
		'tags'	=> array('model' => 'Tag', 'through' => 'pages_tags'),
	);

	protected $_table_columns = array(
		'id'						=>	'',
		'sequence'					=>	'',
		'visible'					=>	'',
		'visible_from'				=>	'',
		'visible_to'				=>	'',
		'internal_name'				=>	'',
		'external_indexing'			=>	'',
		'internal_indexing'			=>	'',
		'visible_in_nav'				=>	'',
		'visible_in_nav_cms'			=>	'',
		'children_visible_in_nav'		=>	'',
		'children_visible_in_nav_cms'	=>	'',
		'children_template_id'		=>	'',
		'children_url_prefix'			=>	'',
		'children_ordering_policy'		=>	'',
		'children_prompt_for_template'	=>	'',
		'grandchild_template_id'		=>	'',
		'keywords'				=>	'',
		'description'				=>	'',
		'created_by'				=>	'',
		'created_time'				=>	'',
	);

	protected $_table_name = 'pages';

	/**
	 * Child ordering policy value for manual
	 * @var	integer
	 */
	const CHILD_ORDER_MANUAL = 1;

	/**
	 * Child ordering policy value for alphabetic
	 * @var	integer
	 */
	const CHILD_ORDER_ALPHABETIC = 2;

	/**
	 * Child ordering policy value for date
	 * @var	integer
	 */
	const CHILD_ORDER_DATE = 4;

	/**
	 * Child ordering policy for ascending.
	 * @var	integer
	 */
	const CHILD_ORDER_ASC = 8;

	/**
	 * Child ordering policy for descending.
	 * @var	integer
	 */
	const CHILD_ORDER_DESC = 16;

	/**
	 * Cached result for self::url()
	 *
	 * @access	private
	 * @var		string
	 */
	private $_url;

	/**
	 * Adds a tag with a given path to the page.
	 *
	 * If the tag doesn't exist then [Model_Tag::create_from_path()] is called to create it.
	 *
	 *
	 * @param string $path
	 * @return \Boom_Model_Page
	 *
	 * @uses Model_Tag::create_from_path()
	 * @throws Exception
	 */
	public function add_tag_with_path($path)
	{
		// If the current page isn't loaded then we can't add a tag to it.
		if ( ! $this->_loaded)
		{
			// Throw an exception
			throw new Exception("Cannot add a tag to an unloaded page");
		}

		// Attempt to load a tag with the given path.
		$tag = ORM::factory('Tag', array('path' => $path));

		// If the tag wasn't found then call [Boom_Model_Tag::create_from_path()] to create it.
		if ( ! $tag->loaded())
		{
			// Create the tag.
			$tag = ORM::factory('Tag')->create_from_path($path, Model_Tag::PAGE);
		}

		// Add the tag to the current page.
		$this->add('tags', $tag);

		// Return the current page.
		return $this;
	}

	/**
	 * Updates a page's children with the same values as the current page.
	 *
	 * @param array $columns
	 * @param array $expected
	 *
	 * @return \Boom_Model_Page
	 * @throws Exception
	 */
	public function cascade_to_children(array $columns, array $expected = array())
	{
		// Page must be loaded.
		if ( ! $this->_loaded)
		{
			throw new Exception("Cannot call ".__CLASS__."::".__METHOD__." on an unloaded object.");
		}

		// If no expected columns have been given then use all the columns
		// Except ID and internal_name which must be unique.
		if (empty($expected))
		{
			$expected = array_diff(array_keys($this->_object), array('id', 'internal_name'));
		}

		// Don't update any columns which we aren't expecting.
		$columns = array_intersect($expected, $columns);

		// Get the values from the current object.
		$values = Arr::extract($this->_object, $columns);

		/**
		 *  The template_id property is a special case.
		 *
		 * If the parent has a child_template_id property set then we should use that.
		 * Otherwise we should use the parent's template_id property.
		 */
		if (isset($values['template_id']))
		{
			$values['template_id'] = ($this->_object['children_template_id'] == 0)? $this->version()->template_id : $this->_object['children_template_id'];
		}

		if ( ! empty($values))
		{
			// Run the query to update the children of the current page with their new values.
			DB::update('pages')
				->where('id', 'IN', DB::select('id')
					->from('page_mptt')
					->where('parent_id', '=', $this->id)
				)
				->set($values)
				->execute($this->_db);
		}

		return $this;
	}

	/**
	 * Getter / setter for the children_ordering_policy column.
	 *
	 * When used as a getter converts the integer stored in the children_ordering_policy column to an array of column and direction which can be used when querying the database.
	 *
	 *  When used as a setter converts the column and direction to an integer which can be stored in the children_ordering_policy column.
	 *
	 * @param	string	$column
	 * @param	string	$direction
	 */
	public function children_ordering_policy($column = NULL, $direction = NULL)
	{
		if ($column === NULL AND $direction === NULL)
		{
			// Act as getter.
			// Determine which column to sort by.
			if ($this->children_ordering_policy & Model_Page::CHILD_ORDER_ALPHABETIC)
			{
				$column = 'title';
			}
			elseif ($this->children_ordering_policy & Model_Page::CHILD_ORDER_DATE)
			{
				$column = 'visible_from';
			}
			else
			{
				$column = 'sequence';
			}

			// Determine the direction to sort in.
			$direction = ($this->children_ordering_policy & Model_Page::CHILD_ORDER_ASC)? 'asc' : 'desc';

			// Return the column and direction as an array.
			return array($column, $direction);
		}
		else
		{
			// Act as setter.

			// Convert the column into an integer.
			switch ($column)
			{
				case ($column == 'manual' OR $column == 'sequence'):
					$order = Model_Page::CHILD_ORDER_MANUAL;
					break;

				case ($column == 'date' OR $column == 'visible_from'):
					$order = Model_Page::CHILD_ORDER_DATE;
					break;

				default:
					$order = Model_Page::CHILD_ORDER_ALPHABETIC;
			}

			// Convert the direction to an integer and apply it to $order
			switch ($direction)
			{
				case 'asc':
					$order = $order | Model_Page::CHILD_ORDER_ASC;
					break;

				default:
					$order = $order | Model_Page::CHILD_ORDER_DESC;
			}

			// Set the value of the children_ordering_policy column.
			$this->children_ordering_policy = $order;

			// Rethrn the current object.
			return $this;
		}
	}

	/**
	 * Create a new version of a page.
	 *
	 * Accepts an optional array of values to apply to the new version.
	 *
	 * @param Model_Page_Version $current
	 * @param array $values
	 * @return Model_Page_Version
	 */
	public function create_version($current = NULL, array $values = NULL)
	{
		// Get the current version
		if ($current === NULL)
		{
			$current = $this->version();
		}

		// Create a new version with the same values as the current version.
		$new_version = ORM::factory('Page_Version')
			->values($current->object());

		// Update the new version with any update values.
		if ( ! empty($values))
		{
			$new_version
				->values($values, array_keys($values));
		}

		// Return the new version
		return $new_version;
	}

	/**
	 * **Delete a page.**
	 *
	 * Deleting a page involves a number of steps:
	 *
	 * *	Create a new version of the page.
	 * *	Sets the deleted flag of the new version to TRUE.
	 * *	Publish the new, deleted version.
	 * *	Delete the page from the MPTT tree.
	 * *	If $with_children is true calls itself recursively to delete child pages
	 *
	 * @param	boolean	$with_children	Whether to delete the child pages as well.
	 * @return	Model_Page
	 */
	public function delete($with_children = FALSE)
	{
		// Can't delete a page which doesn't exist.
		if ($this->_loaded)
		{
			// Delete the child pages as well?
			if ($with_children === TRUE)
			{
				// Get the mptt values of the child pages.
				foreach ($this->mptt->children() as $mptt)
				{
					// Delete the page.
					ORM::factory('Page', $mptt->id)
						->delete($with_children);
				}

				// Reload the MPTT values.
				// When the MPTT record is deleted the gap is closed in the tree
				// So if we don't reload the values after deleting children the gap will be closed incorrectly and the tree will get screwed up.
				$this->mptt->reload();
			}

			// There's a bug with deleting the root node via ORM_MPTT::delete(), so delete manually if this is the root node.
			if ($this->mptt->is_root())
			{
				DB::delete('page_mptt')
					->where('id', '=', $this->mptt->id)
					->execute($this->_db);
			}
			else
			{
				// Delete the page from the MPTT tree as normal.
				$this->mptt->delete();
			}

			// Flag the page as deleted.
			$this
				->create_version(NULL, array(
					'page_deleted'		=>	TRUE,	// Flag the new version as deleting the page
					'embargoed_until'	=>	$_SERVER['REQUEST_TIME'],	// Make the new version live
					'published'			=>	TRUE
				))
				->create();

			// Return a cleared page object.
			return $this->clear();
		}
	}

	/**
	 * Gets the page's description of the page.
	 *
	 * When a value is set for the description property this will be returned.
	 * When the description property is null then the standfirst for the current page version will be returned.
	 *
	 * @return string
	 */
	public function description()
	{
		// Return the page's description if one has been set.
		if ($this->description != NULL)
		{
			return $this->description;
		}

		// Return the standfirst for the current version.
		return Chunk::factory('text', 'standfirst', $this)->text();
	}

	/**
	 * Get the first version of the current page.
	 *
	 * @return Model_Page_Version
	 */
	public function first_version()
	{
		return $this
			->versions
			->order_by('id', 'asc')
			->find();
	}

	/**
	 * Determine whether a published version exists for the page.
	 *
	 * @return	bool
	 */
	public function has_published_version()
	{
		// Run a query to find the ID of a version which belongs to this page and has been published.
		// If a result is returned then a published version exists.
		$query = DB::select('id')
			->from('page_versions')
			->where('page_id', '=', $this->id)
			->where('published', '=', TRUE)
			->where('stashed', '=', FALSE)
			->where('embargoed_until', '<=', $_SERVER['REQUEST_TIME'])
			->limit(1)
			->execute();

		return ($query->count() == 1);
	}

	public function is_visible()
	{
		return ($this->visible AND $this->visible_from <= Editor::instance()->live_time() AND ($this->visible_to >= Editor::instance()->live_time() OR $this->visible_to == 0));
	}

	/**
	 * Get the parent page for the current page.
	 * If the current page is the root node then the current page is returned.
	 * Otherwise a page object for the parent page is returned.
	 *
	 * @return 	Model_Page
	 */
	public function parent()
	{
		return ($this->mptt->is_root())? $this : new Model_Page($this->mptt->parent_id);
	}

	/**
	 * Removes a tag with the given path from a page.
	 *
	 * @param string $path
	 * @return \Boom_Model_Page
	 * @throws Exception
	 */
	public function remove_tag_with_path($path)
	{
		// Page has to be loaded to remove a tag from it.
		if ( ! $this->_loaded)
		{
			throw new Exception("A page has to be loaded to remove a tag from it");
		}

		// Remove the tag.
		$this->remove('tag', new Model_Tag(array('path' => $path)));

		// Return the current page.
		return $this;
	}

	/**
	 * Generate a short URL for the page, similar to t.co etc.
	 * Returns the page ID encoded to base-36 prefixed with an underscore.
	 * We prefix the short URLs to avoid the possibility of conflicts with real URLs
	 *
	 * @return 	string
	 */
	public function short_url()
	{
		return "_" . base_convert($this->id, 10, 36);
	}

	/**
	 * Sort the page's children as specified by the child ordering policy.
	 * Called when changing a page's child ordering policy.
	 *
	 * @return	Model_Page
	 */
	public function sort_children()
	{
		// Get the column and direction to order by.
		list($column, $direction) = $this->children_ordering_policy();

		// Find the children, sorting the database results by the column we want the children ordered by.
		$children = ORM::factory('Page_MPTT')
			->join('pages', 'inner')
			->on('page_mptt.id', '=', 'pages.id')
			->join('page_versions', 'inner')
			->on('pages.id', '=', 'page_versions.page_id')
			->join(array(
				DB::select(array(DB::expr('max(id)'), 'id'), 'page_id')
					->from('page_versions')
					->group_by('page_id'),
				'current_version'
			))
			->on('current_version.id', '=', 'page_versions.id')
			->where('parent_id', '=', $this->id)
			->order_by($column, $direction)
			->find_all();

		// Flag to show that loop is on it's first iteration.
		$first = TRUE;

		// Loop through the children assigning new left and right values.
		foreach ($children as $child)
		{
			if ($first)
			{
				// First iteration of the loop so make the page the first child.
				$child->move_to_first_child($this->id);
				$first = FALSE;
			}
			else
			{
				// For all the other children move to the end.
				$child->move_to_last_child($this->id);
			}
		}

		return $this;
	}

	/**
	 * Restores a page to the last published version.
	 * Marks all versions which haven't been published since the last published versions as stashed.
	 *
	 * This is used for when there are edits to a page in progress which aren't ready to be published but a change needs to be made to the live (published) version (e.g. a typo fix).
	 *
	 * Yes, it's named after 'git stash'. The principal is the same.
	 *
	 * @return \Boom_Model_Page
	 */
	public function stash()
	{
		// Execute a DB query to stash unpublished versions.
		DB::update('page_versions')
			->set(array('stashed' => TRUE))
			->where('embargoed_until', '>=', $_SERVER['REQUEST_TIME'])
			->where('page_id', '=', $this->id)
			->execute($this->_db);

		// If the local cache for the current version is set then clear it.
		if (isset($this->_related['version']))
		{
			$this->_related['version'] = NULL;
		}

		// Return the current object.
		return $this;
	}

	public function status()
	{
		if ($this->is_visible())
		{
			// The page is visible so return the status of the current version.
			return $this->version()->status();
		}
		else
		{
			// The page is invisible - that's it's status.
			return 'invisible';
		}
	}

	/**
	 * Returns the page's absolute URL.
	 * The URL can be displayed by casting the returned object to a string:
	 *
	 *		(string) $page->url();
	 *
	 *
	 * @return Model_Page_URL
	 */
	public function url()
	{
		if ($this->_url === NULL)
		{
			// Get the primary URL for this page.
			$this->_url = $this->urls
				->where('is_primary', '=', TRUE)
				->find();
		}

		return $this->_url;
	}

	/**
	 * Returns the current version for the page.
	 *
	 * @return	Model_Version_Page
	 */
	public function version()
	{
		// Has $this->_version been set?
		if (isset($this->_related['version']))
		{
			// Yes it has, return it.
			return $this->_related['version'];
		}

		// No it hasn't, query the database for the right version to use.

		// Get the editor instance to determine which state the editor is in.
		$editor = Editor::instance();

		// Start the query.
		$query = ORM::factory('Page_Version')
			->where('page_id', '=', $this->id);

		if ($editor->state_is(Editor::DISABLED))
		{
			// For site users get the published version with the embargoed time that's most recent to the current time.
			// Order by ID as well incase there's multiple versions with the same embargoed time.
			$query
				->where('published', '=', TRUE)
				->where('embargoed_until', '<=', $editor->live_time())
				->order_by('embargoed_until', 'desc')
				->order_by('id', 'desc');
		}
		else
		{
			// For logged in users get the version with the highest ID.
			$query
				->order_by('id', 'desc');
		}

		// Run the query and return the result.
		return $this->_related['version'] = $query
			->find();
	}

	/**
	 * Determines whether the current page was created by a particular person.
	 *
	 * @param Model_Person $person
	 * @return boolean
	 */
	public function was_created_by(Model_Person $person)
	{
		return ($this->created_by AND $this->created_by == $person->id);
	}

	/**
	 *
	 *
	 * @param	Editor	$editor
	 * @return	Model_Page
	 */
	public function with_current_version(Editor $editor)
	{
		$current_version = DB::select(array(DB::expr('max(id)'), 'id'), 'page_id')
			->from('page_versions')
			->where('stashed', '=', 0)
			->group_by('page_id');

		if ($editor->state_is(Editor::DISABLED))
		{
			$current_version
				->where('embargoed_until', '<=', DB::expr($editor->live_time()))
				->where('published', '=', DB::expr(1));
		}

		$this
			->join(array($current_version, 'v2'), 'inner')
			->on('page.id', '=', 'v2.page_id')
			->join(array('page_versions', 'version'), 'inner')
			->on('page.id', '=', 'version.page_id')
			->on('v2.id', '=', 'version.id')
			->where('version.page_deleted', '=', FALSE);

		// Logged out view?
		if ($editor->state_is(Editor::DISABLED))
		{
			// Get the most recent published version for each page.
			$this
				->where('visible', '=', TRUE)
				->where('visible_from', '<=', $editor->live_time())
				->and_where_open()
					->where('visible_to', '>=', $editor->live_time())
					->or_where('visible_to', '=', 0)
				->and_where_close();
		}

		return $this;
	}
}