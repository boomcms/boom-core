<?php

namespace BoomCMS\Database\Models\Chunk;

class Slideshow extends BaseChunk
{
    const ATTR_TITLE = 'title';

    protected $table = 'chunk_slideshows';

    public function setTitleAttribute($value)
    {
        $this->attributes[self::ATTR_TITLE] = strip_tags($value);
    }

    public function slides()
    {
        return $this->hasMany(Slideshow\Slide::class, 'chunk_id');
    }

    /**
     * Persists slide data to the database.
     */
    public function setSlidesAttribute($slides)
    {
        foreach ($slides as &$slide) {
            if (!$slide instanceof Slideshow\Slide) {
                $slide['url'] = (isset($slide['page']) && $slide['page'] > 0) ?
                    $slide['page']
                    : isset($slide['url']) ? $slide['url'] : null;

                unset($slide['page']);

                if (isset($slide['asset']) && is_array($slide['asset'])) {
                    $slide['asset_id'] = $slide['asset']['id'];
                    unset($slide['asset']);
                }

                $slide = new Slideshow\Slide($slide);
            }
        }

        $this->created(function () use ($slides) {
            $this->slides()->saveMany($slides);
        });
    }

    public function scopeWithRelations($query)
    {
        return $query->with('slides');
    }

    public function thumbnail()
    {
        if ($this->slides === null) {
            return $this->slides
                ->with('asset')
                ->find()
                ->asset;
        } else {
            return $this->slides[0]->asset;
        }
    }
}
