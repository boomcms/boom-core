<?php

namespace BoomCMS\Database\Models\Chunk;

class Linkset extends BaseChunk
{
    protected $table = 'chunk_linksets';

    public static function create(array $attributes = [])
    {
        if (isset($attributes['links'])) {
            $links = $attributes['links'];
            unset($attributes['links']);
        }

        $linkset = parent::create($attributes);

        if (isset($links)) {
            $linkset->links = $links;
        }

        return $linkset;
    }

    public function links()
    {
        return $this->hasMany('BoomCMS\Database\Models\Chunk\Linkset\Link', 'chunk_linkset_id');
    }

    public function scopeWithRelations($query)
    {
        return $query->with('links');
    }

    public function setLinksAttribute($links)
    {
        foreach ($links as &$link) {
            $link = new Linkset\Link($link);
        }

        $this->attributes['links'] = $links;
        $this->links()->saveMany($links);
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim(strip_tags($value));
    }
}
