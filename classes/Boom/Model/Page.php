<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Boom_Model_Page extends ORM_Taggable
{
	/**
	 * Properties to create relationships with Kohana's ORM
	 */
	protected $_belongs_to = array(
		'mptt'		=>	array('model' => 'Page_MPTT', 'foreign_key' => 'id'),
	);

	protected $_has_one = array(
		'version'		=>	array('model' => 'Page_Version', 'foreign_key' => 'page_id'),
	);

	protected $_has_many = array(
		'versions'	=> array('model' => 'Page_Version', 'foreign_key' => 'page_id'),
		'links'	=> array('model' => 'Page_Link', 'foreign_key' => 'page_id'),
	);

	protected $_table_columns = array(
		'id'						=>	'',
		'sequence'					=>	'',
		'visible'					=>	'',
		'visible_from'				=>	'',
		'visible_to'				=>	0,
		'internal_name'				=>	'',
		'external_indexing'			=>	'',
		'internal_indexing'			=>	'',
		'visible_in_nav'				=>	'',
		'visible_in_nav_cms'			=>	'',
		'children_visible_in_nav'		=>	'',
		'children_visible_in_nav_cms'	=>	'',
		'children_template_id'		=>	'',
		'children_link_prefix'			=>	'',
		'children_ordering_policy'		=>	'',
		'children_prompt_for_template'	=>	'',
		'grandchild_template_id'		=>	'',
		'keywords'				=>	'',
		'description'				=>	'',
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
	 * Holds the calculated primary URI
	 *
	 * @access	private
	 * @var		string
	 */
	private $_primary_link;

	/**
	 * Cached result for self::link()
	 *
	 * @access	private
	 * @var		string
	 */
	private $_link;

	/**
	 * Adds a new child page to this page's MPTT tree.
	 * Ensures that the child is added in the correct position according to this page's child ordering policy.
	 *
	 * @param	Model_Page	$page	The new child page.
	 * @return	Model_Page
	 */
	public function add_child(Model_Page $page)
	{
		// Get the child ordering policy column and direction.
		list($column, $direction) = $this->children_ordering_policy();

		// Find the page_mptt record of the page which comes after this one.
		$mptt = ORM::factory('page_mptt')
			->join('pages', 'inner')
			->on('pages.id', '=', 'page_mptt.id')
			->where('page_mptt.parent_id', '=', $this->mptt->id)
			->where($column, '>', $page->$column)
			->order_by($column, $direction)
			->limit(1)
			->find();

		if ( ! $mptt->loaded())
		{
			// If a record wasn't loaded then there's no page after this one.
			// Insert as the last child of the parent.
			$page->mptt->insert_as_last_child($this->mptt);
		}
		else
		{
			$page->mptt->insert_as_next_sibling($mptt);
		}

		// Reload the MPTT values for this page.
		$this->mptt->reload();

		// Return the current object.
		return $this;
	}

	/**
	 * Getter / setter for the children_ordering_policy column.
	 * When used as a getter converts the integer stored in the children_ordering_policy column to an array of column and direction which can be used when querying the database.
	 * When used as a setter converts the column and direction to an integer which can be stored in the children_ordering_policy column.
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
				case 'manual':
					$order = Model_Page::CHILD_ORDER_MANUAL;
					break;

				case 'date':
					$order = Model_Page::CHILD_ORDER_DATE;
					break;

				default:
					$order = Model_Page::CHILD_ORDER_ALPHABETIC;
			}

			// Convert the direction to an integer and apply it to $order
			switch ($direction)
			{
				case 'asc':
					$order | Model_Page::CHILD_ORDER_ASC;
					break;

				default:
					$order | Model_Page::CHILD_ORDER_DESC;
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
				->values($values);
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

			// Delete the page from the MPTT tree.
			$this->mptt->delete();

			// Flag the page as deleted.
			$this
				->create_version(array(
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
		return TRUE;
		return ! ($this->published_vid == 0);
	}

	/**
	 * Checks that a page is published.
	 * @return	boolean
	 */
	public function is_published()
	{
		return TRUE;
		return ($this->published_from <= editor::instance()->live_time() AND ($this->published_to >= editor::instance()->live_time() OR $this->published_to == 0));
	}

	public function is_visible()
	{
		return ($this->visible AND $this->visible_from <= editor::instance()->live_time() AND ($this->visible_to >= editor::instance()->live_time() OR $this->visible_to == 0));
	}

	/**
	 * Returns the page's absolute link.
	 * This method uses Kohana's URL::base() method to generate the base URL from the current request (protocol, hostnane etc.) {@link http://kohanaframework.org/3.2/guide/api/URL#base}
	 *
	 * @uses	URL::base()
	 * @uses	Request::Instance()
	 * @return string The absolute URI.
	 */
	public function link()
	{
		if ($this->_link === NULL)
		{
			// Get the base link of the current request.
			$this->_link = URL::site($this->primary_link());
		}

		return $this->_link;
	}

	/**
	 * Get the parent page for the current page.
	 * If the current page is the root node then the current page is returned.
	 * Otherwise a page object for the parent page is returned.
	 *
	 * @return 	Model_Version_Page
	 */
	public function parent()
	{
		return ($this->mptt->is_root())? $this : new Model_Page($this->mptt->parent_id);
	}

	/**
	 * Get the page's primary URI
	 * From the page's available URIs finds the one which is marked as the primary URI.
	 *
	 * @return	string	The RELATIVE primary URI of the page.
	 *
	 * @todo	Could use ORM relationship for this.
	 */
	public function primary_link()
	{
		if ($this->_primary_link == NULL)
		{
			$this->_primary_link = DB::select('location')
				->from('page_links')
				->where('page_id', '=', $this->id)
				->where('is_primary', '=', TRUE)
				->limit(1)
				->execute()
				->get('location');
		}

		return $this->_primary_link;
	}

	/**
	 * Generate a short URI for the page, similar to t.co etc.
	 * Returns the page ID encoded to base-36 prefixed with an underscore.
	 * We prefix the short URIs to avoid the possibility of conflicts with real URIs
	 *
	 * @return 	string
	 */
	public function short_link()
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
				DB::select(array('max(id)', 'id'), 'page_id')
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

		if ($editor->state() === Editor::DISABLED)
		{
			// For site users get the published version with the embargoed time that's most recent to the current time.
			// Order by ID as well incase there's multiple versions with the same embargoed time.
			$query
				->where('published', '=', TRUE)
				->where('embargoed_until', '<=', Editor::instance()->live_time())
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
	 *
	 *
	 * @param	Editor	$editor
	 * @return	Model_Page
	 */
	public function with_current_version(Editor $editor)
	{
		$this
			->join(array('page_versions', 'version'), 'inner')
			->on('page.id', '=', 'version.page_id');

		// Logged in view?
		if ($editor->state() === Editor::EDIT)
		{
			$this
				->join(array(
					DB::select(array(DB::expr('max(id)'), 'id'))
						->from('page_versions')
						->where('stashed', '=', FALSE)
						->group_by('page_id'),
					'current_version'
				))
				->on('version.id', '=', 'current_version.id');
		}
		else
		{
			// Get the most recent published version for each page.
			$this
				->join(array(
					DB::select(array(DB::expr('max(id)'), 'id'))
						->from('page_versions')
						->where('embargoed_until', '<=', $editor->live_time())
						->where('stashed', '=', FALSE)
						->where('published', '=', TRUE)
						->group_by('page_id'),
					'current_version'
				))
				->on('version.id', '=', 'current_version.id')
				->where('version.page_deleted', '=', FALSE)
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