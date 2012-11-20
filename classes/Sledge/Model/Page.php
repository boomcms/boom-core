<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @see		Model_Page_Version
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Page extends ORM_Taggable
{
	/**
	 * The first version of the object.
	 * Useful for getting the created time / person.
	 * @var	object
	 */
	private $_first_version;

	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_many = array(
		'links'	=> array('model' => 'Page_Link'),
		'revisions' => array('model' => 'Page_Version', 'foreign_key' => 'rid'),
	);
	protected $_belongs_to = array(
		'version'	=> array('model' => 'Page_Version', 'foreign_key' => 'published_vid'),
		'mptt'	=> array('model' => 'Page_MPTT', 'foreign_key' => 'id'),
	);
	protected $_load_with = array('version');

	protected $_table_columns = array(
		'id'			=>	'',
		'active_vid'	=>	'',
		'published_vid'	=>	'',
		'sequence'		=>	'',
		'visible'		=>	'',
	);

	protected $_cache_columns = array('internal_name');

	/**
	* Child ordering policy value for manual
	* @var integer
	*/
	const CHILD_ORDER_MANUAL = 1;

	/**
	* Child ordering policy value for alphabetic
	* @var integer
	*/
	const CHILD_ORDER_ALPHABETIC = 2;

	/**
	* Child ordering policy value for date
	* @var integer
	*/
	const CHILD_ORDER_DATE = 4;

	/**
	* Child ordering policy for ascending.
	* @var integer
	*/
	const CHILD_ORDER_ASC = 8;

	/**
	* Child ordering policy for descending.
	* @var integer
	*/
	const CHILD_ORDER_DESC = 16;

	/**
	* Holds the calculated primary URI
	*
	* @access private
	* @var string
	*/
	private $_primary_link;

	/**
	* Cached result for self::link()
	*
	* @access private
	* @var string
	*/
	private $_link;

	/**
	 * Pass any method calls not handled by this class to the version object.
	 *
	 * @param	string	$method	Method name
	 * @param	array 	$args	array of arguments
	 */
	public function __call($method, $args)
	{
		$method = new ReflectionMethod(get_class($this->version), $method);
		return $method->invokeArgs($this->version, $args);
	}

	/**
	* Load database values into the object.
	*
	* This is customised to ensure that a user who cannot edit the current page sees the current, published version.
	* While someone who can edit the page sees the current version, whatever it's status.
	* Essentially this function replaces $this->version if the user can edit the page.
	* This isn't a very efficient way of doing it since it means that the published version is loaded and then the published version
	* Resulting in two database queries when we only need the data from one.
	* However, I can't see any other way of doing this at the moment, within the constraints of Kohana.
	*
	* This logic is here, rather than __construct or _initialize as putting this code in those methods wouldn't work for loading pages through related objects, e.g. $page_uri->page.
	*/
	protected function _load_values(array $values)
	{
		parent::_load_values($values);

		if ($this->loaded() AND (bool) $this->version->deleted == TRUE)
		{
			$this->version->clear();
			$this->clear();
		}

		if ($this->loaded())
		{
			$person = Auth::instance()->get_user();

			if ($this->active_vid != $this->published_vid AND Auth::instance()->logged_in( 'edit', $this ))
			{
				$this->_related['version'] = ORM::factory('page_version', $this->active_vid);
			}
		}
	}

	/**
	* This is customised to ensure that a user who cannot edit the current page sees the current, published version.
	* While someone who can edit the page sees the current version, whatever it's status.
	* Essentially this function replaces $this->version if the user can edit the page.
	* This isn't a very efficient way of doing it since it means that the published version is loaded and then the published version
	* Resulting in two database queries when we only need the data from one.
	* However, I can't see any other way of doing this at the moment, within the constraints of Kohana.
	*
	*/
	public function __get($column)
	{
		if ($column == 'version' AND ! isset($this->_related['version']))
		{
			if ($this->loaded() AND Auth::instance()->logged_in())
			{
				if ($this->active_vid != $this->published_vid AND Editor::state() !== Editor::PREVIEW_PUBLISHED AND Auth::instance()->logged_in('edit_page', $this))
				{
					return $this->_related['version'] = ORM::factory('page_version', $this->active_vid);
				}
			}
		}

		if (isset($this->$column) OR array_key_exists($column, $this->_object))
		{
			return parent::__get($column);
		}
		else
		{
			return $this->version->$column;
		}
	}

	/**
	* Adds a new child page to this page's MPTT tree.
	* Ensures that the child is added in the correct position according to this page's child ordering policy.
	*
	* @param Model_Page $page The new child page.
	* @return Model_Page
	*/
	public function add_child(Model_Page $page)
	{
		// Get the child ordering policy column and direction.
		list($column, $direction) = $this->child_ordering_policy();

		// Find the page_mptt record of the page which comes after this one.
		$mptt = ORM::factory('page_mptt')
			->join('page', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->join('page_v', 'inner')
			->on('page.active_vid', '=', 'page_v.id')
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
	 * Getter / setter for the child_ordering_policy column.
	 * When used as a getter converts the integer stored in the child_ordering_policy column to an array of column and direction which can be used when querying the database.
	 * When used as a setter converts the column and direction to an integer which can be stored in the child_ordering_policy column.
	 *
	 * @param	string	$column
	 * @param	string	$direction
	 */
	public function child_ordering_policy($column = NULL, $direction = NULL)
	{
		if ($column === NULL AND $direction === NULL)
		{
			// Act as getter.
			// Determine which column to sort by.
			if ($this->child_ordering_policy & Model_Page::CHILD_ORDER_ALPHABETIC)
			{
				$column = 'title';
			}
			elseif ($this->child_ordering_policy & Model_Page::CHILD_ORDER_DATE)
			{
				$column = 'visible_from';
			}
			else
			{
				$column = 'sequence';
			}

			// Determine the direction to sort in.
			$direction = ($this->child_ordering_policy & Model_Page::CHILD_ORDER_ASC)? 'asc' : 'desc';

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

			// Set the value of the child_ordering_policy column.
			$this->child_ordering_policy = $order;

			// Rethrn the current object.
			return $this;
		}
	}

	/**
	 * Check whether the model data has been changed.
	 * Extends the default behaviour to check the versioned data.
	 *
	 * @param	string	$field	Field to check for changes
	 * @return 	boolean
	 */
	public function changed($field = NULL)
	{
		$return = parent::changed($field);

		if ( ! $return)
		{
			$return = $this->version->changed($field);
		}

		return $return;
	}

	/**
	 * Copy an object into a new object.
	 * Resets the primary key so that the new object can be saved to the database as a new record.
	 *
	 * @return 	ORM
	 */
	public function copy()
	{
		$new = parent::copy();
		$new->active_vid = NULL;

		$new->version = $this->version->copy();
		return $new;
	}

	public function create(Validation $validation = NULL)
	{
		parent::create($validation);

		$this->version->rid = $this->id;
		return $this->update($validation);
	}

	/**
	* Delete a page.
	* Ensures child pages are deleted and that the pages are deleted from the MPTT tree.
	*
	* @return ORM
	*/
	public function delete($children = FALSE)
	{
		if ($children === TRUE)
		{
			foreach ($this->mptt->children() as $p)
			{
				$p->page->delete();
			}
		}

		$this->mptt->reload();
		$this->mptt->delete();

		if ($this->loaded())
		{
			$this->version->deleted = TRUE;
			$this->save();

			$this->version->clear();
			return $this->clear();
		}
	}

	/**
	 * Find the first version of the current object.
	 *
	 * @return 	Model_Version
	 */
	public function first_version()
	{
		if ($this->_first_version === NULL)
		{
			$this->_first_version = ORM::factory($this->version->_object_name)
						->order_by('id', 'asc')
						->where('rid', '=', $this->pk())
						->limit(1)
						->find();
		}

		return $this->_first_version;
	}

	/**
	 * Reload the current object and version
	 */
	public function reload()
	{
		parent::reload();

		$this->version->reload();
	}

	/**
	* Get the page's primary URI
	* From the page's available URIs finds the one which is marked as the primary URI.
	* @return string The RELATIVE primary URI of the page.
	*
	*/
	public function primary_link()
	{
		if ($this->_primary_link == NULL)
		{
			$this->_primary_link = $this->_cache->get('primary_link_for_page:' . $this->id);

			if ($this->_primary_link === NULL)
			{
				$this->_primary_link = DB::select('location')
					->from('page_links')
					->where('page_id', '=', $this->id)
					->and_where('is_primary', '=', TRUE)
					->limit(1)
					->execute()
					->get('location');

				$this->_cache->set('primary_link_for_page:' . $this->id, $this->_primary_link);
			}
		}

		return $this->_primary_link;
	}

	/**
	* Determine whether a published version exists for the page.
	*
	* @return bool
	*/
	public function has_published_version()
	{
		return ! ($this->published_vid == 0);
	}

	/**
	* Checks that a page is published.
	* @return boolean TRUE if it's published, FALSE if it isn't.
	*/
	public function is_published()
	{
		return $this->published_vid == $this->version->id;
	}

	public function is_visible()
	{
		return ($this->visible AND $this->visible_from <= $_SERVER['REQUEST_TIME'] AND ($this->visible_to >= $_SERVER['REQUEST_TIME'] OR $this->visible_to == 0));
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
	 * @return 	Model_Page
	 */
	public function parent()
	{
		return ($this->mptt->is_root())? $this : $this->mptt->parent()->page;
	}

	/**
	 * This ensures that when assigning a new version to a page the active_vid or published_vid is updated depending on whether the person can edit the page.
	 * This fixes a bug whereby when Sledge_Controller_Cms_Page::action_page() assigns a new version to the page the published_vid is updated, even though the logged in user is accessing the page version based on the active_vid column.
	 */
	public function set($column, $value)
	{
		if ($column == 'version')
		{
			$this->_related[$column] = $value;

			$join_column = Page::join_column($this, Auth::instance());
			$this->$join_column = $value->pk();
			$this->_changed[$column] = $value->pk();
		}
		else
		{
			try
			{
				parent::set($column, $value);
			}
			catch (Kohana_Exception $e)
			{
				$this->version->set($column, $value);
			}

			return $this;
		}
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
		list($column, $direction) = $this->child_ordering_policy();

		// Find the children, sorting the database results by the column we want the children ordered by.
		$children = ORM::factory('Page_MPTT')
			->join('page', 'inner')
			->on('page_mptt.id', '=', 'page.id')
			->join('page_v', 'inner')
			->on('page.active_vid', '=', 'page_v.id')
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

	public function unserialize($data)
	{
		parent::unserialize($data);

		if ($this->_loaded AND $this->deleted)
		{
			$this->clear();
			$this->version->clear();
		}
	}

	public function update(Validation $validation = NULL)
	{
		// Has the version been changed?
		$changed = $this->version->changed();

		if ( ! empty($changed))
		{
			$version = $this->version->copy();
			$version->rid = $this->id;

			$auth = Auth::instance();

			if ($auth->logged_in())
			{
				$version->audit_person = $auth->get_user()->pk();
			}

			$version->audit_time = time();
			$version->save();

			$this->active_vid = $version->id;
			$this->_related['version'] = $version;

			unset($this->_changed['version']);
		}

		return parent::update($validation);
	}
}