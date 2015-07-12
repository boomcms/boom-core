<?php

namespace BoomCMS\Core\Models;

use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\URL\Helpers as URLHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Page extends Model
{
    use SoftDeletes;

    protected $table = 'pages';
    public $guarded = ['id'];
    public $timestamps = false;

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

    public function getCurrentVersionQuery(Editor $editor = null)
    {
        $query = DB::table('page_versions')
            ->select([DB::raw('max(id) as id'), 'page_id'])
            ->where('stashed', '=', 0)
            ->groupBy('page_id');

        if ($editor && $editor->isDisabled()) {
            $query
                ->where('embargoed_until', '<=', time())
                ->where('published', '=', 1);
        }

        return $query;
    }

    public function set_template_of_children($template_id)
    {
        $versions = DB::select([DB::raw('max(page_versions.id)'), 'id'])
            ->from('page_versions')
            ->join('page_mptt', 'inner')
            ->on('page_mptt.id', '=', 'page_versions.page_id')
            ->where('page_mptt.scope', '=', $this->mptt->scope)
            ->where('page_mptt.lft', '>', $this->mptt->lft)
            ->where('page_mptt.rgt', '<', $this->mptt->rgt)
            ->groupBy('page_versions.page_id')
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
            ->where('embargoed_until', '>=', time())
            ->where('page_id', '=', $this->id)
            ->execute($this->_db);

        // If the local cache for the current version is set then clear it.
        if (isset($this->_related['version'])) {
            $this->_related['version'] = null;
        }

        // Return the current object.
        return $this;
    }

    public function scopeAutocompleteTitle($query, $title, $limit)
    {
        return $query
            ->currentVersion()
            ->select('title', 'primary_uri')
            ->where('title', 'like', '%' . $title. '%')
            ->limit($limit)
            ->orderBy(DB::raw('length(title)'), 'asc');
    }

    public function scopeCurrentVersion($query, Editor $editor = null)
    {
        $subquery = $this->getCurrentVersionQuery($editor);

        return $query
            ->select('version.*')
            ->addSelect('version.id as version:id')
            ->addSelect('pages.*')
            ->join(DB::raw('(' . $subquery->toSql() . ') as v2'), 'pages.id', '=', 'v2.page_id')
            ->mergeBindings($subquery)
            ->join('page_versions as version', function ($join) {
                $join
                    ->on('pages.id', '=', 'version.page_id')
                    ->on('v2.id', '=', 'version.id');
            });
    }

    public function scopeIsVisible($query)
    {
        return $this->scopeIsVisibleAtTime($query, time());
    }

    public function scopeIsVisibleAtTime($query, $time)
    {
        return $query
            ->where('visible', '=', true)
            ->where('visible_from', '<=', $time)
            ->where(function ($query) use ($time) {
                $query
                    ->where('visible_to', '>=', $time)
                    ->orWhere('visible_to', '=', 0);
            });
    }

    public function scopeWithUrl($query)
    {
        return $query->whereNotNull('primary_uri');
    }

    public function setPrimaryUriAttribute($value)
    {
        $this->attributes['primary_uri'] = URLHelper::sanitise($value);
    }
}
