<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Models
 * 
 */
class Boom_Model_Page extends Model_Taggable
{
	/**
	 * Properties to create relationships with Kohana's ORM
	 */
	protected $_belongs_to = array(
		'mptt'		=>	array('model' => 'Page_MPTT', 'foreign_key' => 'id'),
	);

	protected $_created_column = array(
		'column'	=>	'created_time',
		'format'	=>	true,
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
		'visible_in_nav'				=>	true,
		'visible_in_nav_cms'			=>	true,
		'children_visible_in_nav'		=>	true,
		'children_visible_in_nav_cms'	=>	true,
		'children_template_id'		=>	'',
		'children_url_prefix'			=>	'',
		'children_ordering_policy'		=>	'',
		'children_prompt_for_template'	=>	'',
		'grandchild_template_id'		=>	'',
		'keywords'				=>	'',
		'description'				=>	'',
		'created_by'				=>	'',
		'created_time'				=>	'',
		'primary_uri'				=>	'',
		'deleted'					=>	'',
	);

	protected $_table_name = 'pages';

	/**
	 * Cached result for self::url()
	 *
	 * @access	private
	 * @var		string
	 */
	private $_url;

	/**
	 *
	 * @return \Boom_Model_Page
	 * @throws Exception
	 */
	public function cascade_to_children(array $settings)
	{
		// Page must be loaded.
		if ( ! $this->_loaded)
		{
			throw new Exception("Cannot call ".__CLASS__."::".__METHOD__." on an unloaded object.");
		}

		if ( ! empty($settings))
		{
			DB::update('pages')
				->where('id', 'IN', DB::select('id')
					->from('page_mptt')
					->where('parent_id', '=', $this->id)
				)
				->set($settings)
				->execute($this->_db);
		}

		return $this;
	}

	/**
	 * Delete any feature boxes which have this page as the target.
	 *
	 */
	public function delete_from_feature_boxes()
	{
		DB::delete('chunk_features')
			->where('target_page_id', '=', $this->id)
			->execute($this->_db);
	}

	public function delete_from_linksets()
	{
		DB::delete('chunk_linkset_links')
			->where('target_page_id', '=', $this->id)
			->execute($this->_db);
	}

	public function set_template_of_children($template_id)
	{
		$versions = DB::select(array(DB::expr('max(page_versions.id)'), 'id'))
			->from('page_versions')
			->join('page_mptt', 'inner')
			->on('page_mptt.id', '=', 'page_versions.page_id')
			->where('page_mptt.scope', '=', $this->mptt->scope)
			->where('page_mptt.lft', '>', $this->mptt->lft)
			->where('page_mptt.rgt', '<', $this->mptt->rgt)
			->group_by('page_versions.page_id')
			->execute($this->_db)
			->as_array();

		$versions = Arr::pluck($versions, 'id');

		if ( ! empty($versions))
		{
			DB::update('page_versions')
				->set(array('template_id' => $template_id))
				->where('id', 'IN', $versions)
				->execute($this->_db);
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
	public function create_version($current = null, array $values = null)
	{
		// Get the current version
		if ($current === null)
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
	 * *	Sets the deleted flag of the new version to true.
	 * *	Publish the new, deleted version.
	 * *	Delete the page from the MPTT tree.
	 * *	If $with_children is true calls itself recursively to delete child pages
	 *
	 * @param	boolean	$with_children	Whether to delete the child pages as well.
	 * @return	Model_Page
	 */
	public function delete($with_children = false)
	{
		if ( ! $this->_loaded)
		{
			return $this;
		}

		$this->delete_from_feature_boxes();
		$this->delete_from_linksets();

		$with_children && $this->delete_children(true);

		$this->mptt->delete();

		// Flag the page as deleted.
		$this
			->set('deleted', true)
			->update();

		// Return a cleared page object.
		return $this->clear();
	}

	public function delete_children($cascade = false)
	{
		foreach ($this->mptt->children() as $mptt)
		{
			$page = \Boom\Page\Finder::byId($mptt->id);
			$page->delete($cascade);
		}

		$this->mptt->reload();
	}

	public function get_tags_applied_down_tree_query()
	{
		return ORM::factory('Tag')
			->join('pages_tags', 'inner')
			->on('tag.id', '=', 'pages_tags.tag_id')
			->join('pages', 'inner')
			->on('pages_tags.page_id', '=', 'pages.id')
			->join('page_mptt', 'inner')
			->on('pages.id', '=', 'page_mptt.id')
			->where('page_mptt.lft', '>=', $this->mptt->lft)
			->where('page_mptt.rgt', '<=', $this->mptt->rgt)
			->where('page_mptt.scope', '=', $this->mptt->scope)
			->distinct(true)
			->order_by('tag.name', 'asc');
	}

	public function get_tags_applied_down_tree($prefix = null)
	{
		$query = $this->get_tags_applied_down_tree_query();

		if ($prefix)
		{
			$query->where('tag.name', 'like', $prefix);
		}

		return $query->find_all()->as_array();
	}

	public function has_feature_image()
	{
		return $this->version()->feature_image_id != 0;
	}

	public static function id_by_internal_name($name)
	{
		$results = DB::select('id')
			->from('pages')
			->where('internal_name', '=', $name)
			->execute()
			->as_array();

		if (isset($results[0]))
		{
			return $results[0]['id'];
		}
	}

	public function is_visible()
	{
		return ($this->visible && $this->visible_from <= \Boom\Editor::instance()->getLiveTime() && ($this->visible_to >= \Boom\Editor::instance()->getLiveTime() || $this->visible_to == 0));
	}

	/**
	 *
	 * @return 	Model_Page
	 */
	public function parent()
	{
		return ($this->mptt->is_root())? $this : \Boom\Finder\Page::byId($this->mptt->parent_id);
	}

	public function remove_drafts()
	{
		DB::delete('page_versions')
			->where('page_id', '=', $this->id)
			->and_where_open()
					->where('embargoed_until', '=', null)
					->or_where('embargoed_until', '>', $_SERVER['REQUEST_TIME'])
			->and_where_close()
			->where('stashed', '=', false)
			->execute($this->_db);
	}

	/**
	 * Add the page version columns to a select query.
	 *
	 * @return \Boom_Model_Page
	 */
	protected function _select_version()
	{
		// Add the version columns to the select.

		$model = "Model_".$this->_has_one['version']['model'];
		$target = new $model;

		foreach (array_keys($target->_object) as $column)
		{
			// Add the prefix so that load_result can determine the relationship
			$this->select(array("version.$column", "version:$column"));
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
			->set(array('stashed' => true))
			->where('embargoed_until', '>=', $_SERVER['REQUEST_TIME'])
			->where('page_id', '=', $this->id)
			->execute($this->_db);

		// If the local cache for the current version is set then clear it.
		if (isset($this->_related['version']))
		{
			$this->_related['version'] = null;
		}

		// Return the current object.
		return $this;
	}

	public function update_child_sequences(array $sequences)
	{
		foreach ($sequences as $sequence => $page_id)
		{
			$mptt = new Model_Page_Mptt($page_id);

			// Only update the sequence of pages which are children of this page.
			if ($mptt->scope == $this->mptt->scope && $mptt->parent_id == $this->id)
			{
				DB::update($this->_table_name)
					->set(array('sequence' => $sequence))
					->where('id', '=', $page_id)
					->execute($this->_db);
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
		$editor = \Boom\Editor::instance();

		// Start the query.
		$query = ORM::factory('Page_Version')
			->where('page_id', '=', $this->id);

		if ($editor->isDisabled())
		{
			// For site users get the published version with the embargoed time that's most recent to the current time.
			// Order by ID as well incase there's multiple versions with the same embargoed time.
			$query
				->where('published', '=', true)
				->where('embargoed_until', '<=', $editor->getLiveTime())
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
		return $this->_related['version'] = $query->find();
	}

	/**
	 * Determines whether the current page was created by a particular person.
	 *
	 * @param Model_Person $person
	 * @return boolean
	 */
	public function was_created_by(Model_Person $person)
	{
		return ($this->created_by && $this->created_by == $person->id);
	}

	/**
	 *
	 *
	 * @param	\Boom\Editor	$editor
	 * @param	boolean	$exclude_deleted
	 * @return	Model_Page
	 */
	public function with_current_version(\Boom\Editor $editor)
	{
		$page_query = new \Boom\Page\Query($this, $editor);
		$page_query->execute();

		// Add the version columns to the query.
		$this->_select_version();

		return $this;
	}
}
