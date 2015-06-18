<?php

namespace BoomCMS\Core\Models\Page;

use BoomCMS\Core\Page\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Version extends Model
{
    protected $table = 'page_versions';
    public $guarded = ['id'];
    public $timestamps = false;
	
	public function scopeLastPublished($query)
	{
		// Get the published version with the most recent embargoed time.
		// Order by ID as well incase there's multiple versions with the same embargoed time.
		return $query
			->where('published', '=', true)
			->where('embargoed_until', '<=', time())
			->orderBy('embargoed_until', 'desc')
			->orderBy('id', 'desc');
	}

    public function scopeLatestAvailable($query)
    {
        $editor = App::make('BoomCMS\Core\Editor\Editor');

        if ($editor->isDisabled()) {
            return $this->scopeLastPublished($query);
        } else {
            // For logged in users get the version with the highest ID.
            return $query->orderBy('id', 'desc');
        }
    }
	
	public function scopeForPage($query, Page $page)
	{
		return $query->where('page_id', '=', $page->getId());
	}
	
	public function setTitleAttribute($title)
	{
		$this->attributes['title'] = trim(html_entity_decode(strip_tags($title)));
	}
}
