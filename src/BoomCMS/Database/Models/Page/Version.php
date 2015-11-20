<?php

namespace BoomCMS\Database\Models\Page;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Facades\Editor;
use Illuminate\Database\Eloquent\Model;

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
        if (Editor::isDisabled()) {
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
        $title = trim(html_entity_decode(strip_tags($title)));

        if (strlen($title) <= 70) {
            $this->attributes['title'] = $title;
        }
    }
}
