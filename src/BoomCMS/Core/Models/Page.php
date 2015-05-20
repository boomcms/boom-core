<?php

namespace BoomCMS\Core\Models;

use BoomCMS\Core\Editor\Editor as Editor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Page extends Model
{
    protected $table = 'pages';



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

    public function getCurrentVersionQuery()
    {
        $query = DB::select([\DB::expr('max(id)'), 'id'], 'page_id')
            ->from('page_versions')
            ->where('stashed', '=', 0)
            ->group_by('page_id');

        if ($this->_editor->isDisabled()) {
            $query
                ->where('embargoed_until', '<=', \DB::expr(time()))
                ->where('published', '=', \DB::expr(1));
        }

        return $query;
    }

    public function set_template_of_children($template_id)
    {
        $versions = DB::select([DB::expr('max(page_versions.id)'), 'id'])
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
                ->set(['template_id' => $template_id])
                ->where('id', 'IN', $versions)
                ->execute($this->_db);
        }
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
            ->set(['stashed' => true])
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

    public function scopeCurrentVersion($query)
    {
        return $query
            ->join([$this->getCurrentVersionSubquery(), 'v2'], 'pages.id', '=', 'v2.page_id')
            ->join(['page_versions', 'version'], function($join) {
                $join
                    ->on('pages.id', '=', 'version.page_id')
                    ->on('v2.id', '=', 'version.id');
            });
    }
    
    public function scopeIsVisible($query)
    {
        return $this->isVisibleAtTime($query, time());
    }

    public function scopeIsVisibleAtTime($query, $time)
    {
        return $query
            ->where('visible', '=', true)
            ->where('visible_from', '<=', $time)
            ->where(function($query) use ($time) {
                $query
                    ->where('visible_to', '>=', $time)
                    ->orWhere('visible_to', '=', 0);
            });
    }

    public function scropeWithUrl($query)
    {
        return $query->where('primary_uri', '!=', null);
    }
}
