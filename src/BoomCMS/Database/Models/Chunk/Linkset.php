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
        return $this->hasMany(Linkset\Link::class, 'chunk_linkset_id');
    }

    public function scopeWithRelations($query)
    {
        return $query->with('links');
    }

    public function setLinksAttribute($links)
    {
        foreach ($links as &$link) {
            if (isset($link['asset']) && is_array($link['asset'])) {
                $link['asset_id'] = $link['asset']['id'];
                unset($link['asset']);
            }

            $link = ($link instanceof Linkset\Link) ? $link : new Linkset\Link($link);
        }

        $this->links()->saveMany($links);
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim(strip_tags($value));
    }
}
