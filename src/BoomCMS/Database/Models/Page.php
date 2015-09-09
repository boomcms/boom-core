<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Helpers\URL as URLHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Page extends Model
{
    use SoftDeletes;

    protected $table = 'pages';
    public $guarded = ['id'];
    public $timestamps = false;

    public function getCurrentVersionQuery()
    {
        $query = DB::table('page_versions')
            ->select([DB::raw('max(id) as id'), 'page_id'])
            ->groupBy('page_id');

        if (Editor::isDisabled()) {
            $query
                ->where('embargoed_until', '<=', time())
                ->where('published', '=', 1);
        }

        return $query;
    }

    public function scopeAutocompleteTitle($query, $title, $limit)
    {
        return $query
            ->currentVersion()
            ->select('title', 'primary_uri')
            ->where('title', 'like', '%'.$title.'%')
            ->limit($limit)
            ->orderBy(DB::raw('length(title)'), 'asc');
    }

    public function scopeCurrentVersion($query)
    {
        $subquery = $this->getCurrentVersionQuery();

        return $query
            ->select('version.*')
            ->addSelect('version.id as version:id')
            ->addSelect('pages.*')
            ->join(DB::raw('('.$subquery->toSql().') as v2'), 'pages.id', '=', 'v2.page_id')
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

    public function setInternalNameAttribute($value)
    {
        $value = strtolower(preg_replace('|[^-_0-9a-zA-Z]|', '', $value));

        $this->attributes['internal_name'] = $value ? $value : null;
    }

    public function setPrimaryUriAttribute($value)
    {
        $this->attributes['primary_uri'] = URLHelper::sanitise($value);
    }
}
