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

		return parent::__get($column);
	}

	/**
	 * Adds a URI to the page
	 *
	 * @param	string	$uri		URI to add
	 * @param	boolean	$primary	Whether the URI should be added as a primary URI
	 */
	public function add_link($link, $primary = FALSE)
	{
		if ($this->loaded())
		{
			$link = trim($link);

			// Check that this isn't an old link for this page.
			// This could happen when the page title is changed but the user goes back to a version with the old title and saves from there.
			$page_link = ORM::factory('Page_Link')
				->where('location', '=', $link)
				->where('page_id', '=', $this->id)
				->find();

			if ( ! $page_link->loaded())
			{
				//  It's not an old LINK, so create a new one.
				$page_link = ORM::factory('Page_Link')
					->values(array(
						'location'	=>	$link,
						'page_id'	=>	$this->id,
					));
			}

			$page_link->is_primary = $primary;
			$page_link->create();

			if ($primary == TRUE)
			{
				// Save the primary link in cache
				$this->_cache->set('primary_link_for_page:' . $this->id, $link);

				// Ensure that this is the only primary link for the page.
				// We do this through the ORM rather than a DB update query to catch cached links
				$page_links = ORM::factory('Page_Link')
					->where('page_id', '=', $this->id)
					->where('id', '!=', $page_link->id)
					->where('is_primary', '=', TRUE)
					->find_all();

				foreach ($page_links as $page_link)
				{
					$page_link->is_primary = FALSE;
					$page_link->save();
				}
			}
		}
	}

	/**
	* Adds a new child page to this page's MPTT tree.
	* Takes care of putting the child in the correct position according to this page's child ordering policy.
	*
	* @param Model_Page $page The new child page.
	* @return void
	*/
	public function add_child(Model_Page $page)
	{
		if ($this->child_ordering_policy & self::CHILD_ORDER_DATE)
		{
			if ($this->child_ordering_policy & self::CHILD_ORDER_ASC)
			{
				$page->mptt->insert_as_last_child($this->mptt);
			}
			else
			{
				$page->mptt->insert_as_first_child($this->mptt);
			}
		}
		elseif ($this->child_ordering_policy & self::CHILD_ORDER_ALPHABETIC)
		{
			// Ordering alphabetically?
			// Find the page_mptt record of the page which comes after this alphabetically.
			$mptt = ORM::factory('Page_MPTT')
				->join('pages', 'inner')
				->on('pages.id', '=', 'page_mptt.id')
				->join('page_versions', 'inner')
				->on('pages.active_vid', '=', 'page_versions.id')
				->where('page_mptt.parent_id', '=', $this->mptt->id);

			if ($this->child_ordering_policy & self::CHILD_ORDER_ASC)
			{
				$mptt->where('title', '>', $page->title)->order_by('title', 'asc');
			}
			else
			{
				$mptt->where('title', '<', $page->title)->order_by('title', 'desc');
			}

			$mptt
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
		}
		else
		{
			// For anything else (such as ordering children manually) just stick it at the end for now.
			$page->mptt->insert_as_last_child($this->mptt);
		}

		$this->mptt->reload();
	}

	/**
	* Creates table joins and where statements to limit query results to a sub-tree.
	*
	* @param Model_Page $parent The parent page.
	*/
	public function child_of(Model_Page $parent)
	{
		$this
			->join('page_mptt', 'inner')
			->on('page_mptt.id', '=', $this->_table_name . "." . $this->_primary_key)
			->where('page_mptt.scope', '=', $parent->mptt->scope)
			->where('page_mptt.lft', '>=', $parent->mptt->lft)
			->where('page_mptt.rgt', '<=', $parent->mptt->rgt);

		return $this;
	}

	/**
	* Returns how many versions a page has.
	*
	* @uses Model_Page::version_ids()
	*/
	public function count_versions()
	{
		return count($this->version_ids());
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

		return parent::delete();
	}

	/**
	* Return a human readable representation of the child ordering policy.
	*
	* @return string Child ordering policy
	*/
	public function get_child_ordering_policy()
	{
		switch ($this->child_ordering_policy)
		{
			case self::CHILD_ORDER_MANUAL:
				return 'Manual';
				break;
			case self::CHILD_ORDER_ALPHABETIC:
				return 'Alphabetic';
				break;
			case self::CHILD_ORDER_DATE:
				return 'Date';
				break;
			default:
				throw new Exception("Page version has unknown child ordering policy: " . $this->child_ordering_policy);
		}
	}

	/**
	* Builds a query to find the current page's children.
	*/
	public function get_children()
	{
		if ( ! $this->loaded())
		{
			return FALSE;
		}

		return ORM::factory('Page')
			->join('page_mptt', 'inner')
			->on('pages.id', '=', 'page_mptt.id')
			->where('lft', '>', $this->mptt->lft)
			->where('rgt', '<', $this->mptt->rgt)
			->where('scope', '=', $this->mptt->scope);
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
		$time = time();

		return ($this->visible AND $this->visible_from <= $time AND ($this->visible_to >= $time OR $this->visible_to == 0));
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
	* Used to re-order this page's children
	*
	* @param int $order The ordering policy ID.
	* @param string $direction The order direction, should be 'asc' or 'desc'
	* @todo lock mptt tables to avoid errors.
	*/
	public function order_children($order, $direction)
	{
		$direction = ($direction == 'asc')? 'asc' : 'desc';

		if (is_string($order))
		{
			switch ($order)
			{
				case 'manual':
					$order = self::CHILD_ORDER_MANUAL;
					break;
				case 'date':
					$order = self::CHILD_ORDER_DATE;
					break;
				default:
					$order = self::CHILD_ORDER_ALPHABETIC;
			}
		}

		if ($this->mptt->has_children())
		{
			// Find the children, sorting the database results by the column we want the children ordered by.
			$query = ORM::factory('Page_mptt')
				->join('page', 'inner')->on('page_mptt.id', '=', 'page.id')
				->join('page_v', 'inner')->on('page.active_vid', '=', 'page_v.id')
				->where('parent_id', '=', $this->mptt->id);

			if ($order == self::CHILD_ORDER_ALPHABETIC)
			{
				$query->order_by('title', $direction);
			}
			elseif ($order == self::CHILD_ORDER_DATE)
			{
				$query->order_by('audit_time', $direction);
			}
			else
			{
				$query->order_by('sequence', 'asc');
			}

			$children = $query->find_all();

			$previous = NULL;

			// Loop through the children assigning new left and right values.
			foreach ($children as $child)
			{
				if ($previous === NULL)
				{
					$child->move_to_first_child($this->mptt);
				}
				else
				{
					$child->move_to_next_sibling($previous);
				}

				$previous = $child;
			}
		}

		$direction = ($direction == 'asc')? (self::CHILD_ORDER_ASC) : (self::CHILD_ORDER_DESC);
		$this->child_ordering_policy = $direction | $order;
	}

	/**
	* Save the page.
	* Updates the cache with an array of page versions for use by Controller_Cms_Page::action_revisions()
	*/
	public function save( Validation $validation = NULL)
	{
		parent::save($validation);

		// Has the version been saved?
		if ($this->version->saved())
		{
			// If there's a revision list in the cache then update it.
			$cache_key = 'page_versions:' . $this->pk();

			if ($revisions = Cache::instance()->get($cache_key))
			{
				array_unshift($revisions, $this->active_vid);
				$this->_cache->set($cache_key, $revisions);
				$this->_cache->set("version_count:" . $this->pk(), count($revisions));
			}
		}
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

			$join_column = Page::join_column($this, Auth::instance()->get_user());
			$this->$join_column = $value->pk();
			$this->_changed[$column] = $value->pk();
		}
		else
		{
			return parent::set($column, $value);
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

	public function sort_children()
	{
		if ($this->mptt->has_children())
		{
			// Find the children, sorting the database results by the column we want the children ordered by.
			$query = ORM::factory('Page_mptt')
				->join('page', 'inner')->on('page_mptt.id', '=', 'page.id')
				->join('page_v', 'inner')->on('page.active_vid', '=', 'page_v.id')
				->where('parent_id', '=', $this->mptt->id);

			if ($this->child_ordering_policy & self::CHILD_ORDER_ALPHABETIC)
			{
				$query->order_by('title', 'asc');
			}
			elseif ($this->child_ordering_policy & self::CHILD_ORDER_DATE)
			{
				$query->order_by('audit_time', 'desc');
			}
			else
			{
				$query->order_by('sequence', 'asc');
			}

			$children = $query->find_all();

			$previous = NULL;

			// Loop through the children assigning new left and right values.
			foreach ($children as $child)
			{
				if ($previous === NULL)
				{
					$child->move_to_first_child($this->mptt);
				}
				else
				{
					$child->move_to_next_sibling($previous);
				}

				$previous = $child;
			}
		}
	}

	/**
	 * Returns an array of page version IDs.
	 * Tries to get a cached version ID list first before retrieving IDs from the database.
	 * Used to display a list of versions and to notify the user how many versions there are.
	 *
	 * @return integer
	 */
	public function version_ids()
	{
		$cache_key = 'page_versions:' . $this->id;

		// Cache the version IDs of the page versions
		// Only cache the IDs so that the version objects are only cached in one place.
		if ( ! $revisions = $this->_cache->get($cache_key))
		{
			$revisions = DB::select('page_versions.id')
							->from('page_versions')
							->where('rid', '=', $this->id)
							->order_by('id', 'desc')
							->execute();
			$revisions = Arr::pluck($revisions, 'id');

			$this->_cache->set($cache_key, $revisions);
		}

		// Just in case something's fouled up our cache.
		$revisions = array_unique($revisions);

		return $revisions;
	}
}
