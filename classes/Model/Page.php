<?php

use \Boom\Editor\Editor as Editor;

class Model_Page extends ORM
{
    /**
	 * Properties to create relationships with Kohana's ORM
	 */
    protected $_belongs_to = array(
        'mptt'        =>    array('model' => 'Page_MPTT', 'foreign_key' => 'id'),
        'feature_image' => array('model' => 'Asset', 'foreign_key' => 'feature_image_id')
    );

    protected $_created_column = array(
        'column'    =>    'created_time',
        'format'    =>    true,
    );

    protected $_has_one = array(
        'version'        =>    array('model' => 'Page_Version', 'foreign_key' => 'page_id'),
    );

    protected $_has_many = array(
        'versions'    => array('model' => 'Page_Version', 'foreign_key' => 'page_id'),
        'urls'        => array('model' => 'Page_URL', 'foreign_key' => 'page_id'),
        'tags'    => array('model' => 'Tag', 'through' => 'pages_tags'),
    );

    protected $_table_columns = array(
        'id'                        =>    '',
        'sequence'                    =>    '',
        'visible'                    =>    '',
        'visible_from'                =>    '',
        'visible_to'                =>    '',
        'internal_name'                =>    '',
        'external_indexing'            =>    '',
        'internal_indexing'            =>    '',
        'visible_in_nav'                =>    true,
        'visible_in_nav_cms'            =>    true,
        'children_visible_in_nav'        =>    true,
        'children_visible_in_nav_cms'    =>    true,
        'children_template_id'        =>    '',
        'children_url_prefix'            =>    '',
        'children_ordering_policy'        =>    '',
        'grandchild_template_id'        =>    '',
        'keywords'                =>    '',
        'description'                =>    '',
        'created_by'                =>    '',
        'created_time'                =>    '',
        'primary_uri'                =>    '',
        'deleted'                    =>    '',
        'feature_image_id'            =>    '',
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
        if (! $this->_loaded) {
            throw new Exception("Cannot call ".__CLASS__."::".__METHOD__." on an unloaded object.");
        }

        if ( ! empty($settings)) {
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

        if ( ! empty($versions)) {
            DB::update('page_versions')
                ->set(array('template_id' => $template_id))
                ->where('id', 'IN', $versions)
                ->execute($this->_db);
        }
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
        $target = new $model();

        foreach (array_keys($target->_object) as $column) {
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
        if (isset($this->_related['version'])) {
            $this->_related['version'] = null;
        }

        // Return the current object.
        return $this;
    }

    public function update_child_sequences(array $sequences)
    {
        foreach ($sequences as $sequence => $page_id) {
            $mptt = new Page_Mptt($page_id);

            // Only update the sequence of pages which are children of this page.
            if ($mptt->scope == $this->mptt->scope && $mptt->parent_id == $this->id) {
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
        if (isset($this->_related['version'])) {
            // Yes it has, return it.
            return $this->_related['version'];
        }

        $editor = Editor::instance();

        // Start the query.
        $query = ORM::factory('Page_Version')
            ->where('page_id', '=', $this->id);

        if ($editor->isDisabled()) {
            // For site users get the published version with the embargoed time that's most recent to the current time.
            // Order by ID as well incase there's multiple versions with the same embargoed time.
            $query
                ->where('published', '=', true)
                ->where('embargoed_until', '<=', $editor->getLiveTime())
                ->order_by('embargoed_until', 'desc')
                ->order_by('id', 'desc');
        } else {
            // For logged in users get the version with the highest ID.
            $query
                ->order_by('id', 'desc');
        }

        // Run the query and return the result.
        return $this->_related['version'] = $query->find();
    }

    /**
	 *
	 *
	 * @param	\Boom\Editor	$editor
	 * @param	boolean	$exclude_deleted
	 * @return	Model_Page
	 */
    public function with_current_version(\Boom\Editor\Editor $editor)
    {
        $page_query = new \Boom\Page\Query($this, $editor);
        $page_query->execute();

        // Add the version columns to the query.
        $this->_select_version();

        return $this;
    }
}
