<?php

namespace BoomCMS\Core\Models;

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

    public function scopeCurrentVersion($query)
    {
        $query
            ->select('version.*')
            ->addSelect('version.id as version:id')
            ->addSelect('pages.*')
			->join('page_versions as version', 'pages.id', '=', 'version.page_id')
			->where('version.stashed', '=', 0)
			->leftJoin('page_versions as v2', function($join){
				$join
					->on('version.page_id', '=', 'v2.page_id')
					->on('version.id', '<', 'v2.id');
			})
			->whereNull('v2.id');
			
		// if ($this->_editor->isDisabled()) {
            $query
				->where(function($query) {
					$query
						->where('version.embargoed_until', '<=', time())
						->orWhereNull('version.embargoed_until');
				})
                ->where('version.published', '=', 1);
		//  }	
			
		return $query;
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
            ->where(function($query) use ($time) {
                $query
                    ->where('visible_to', '>=', $time)
                    ->orWhere('visible_to', '=', 0);
            });
    }

    public function scopeWithUrl($query)
    {
        return $query->whereNotNull('primary_uri');
    }
}
