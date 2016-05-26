<?php

namespace BoomCMS\Database\Models\Chunk;

class Slideshow extends BaseChunk
{
    protected $table = 'chunk_slideshows';

    public static function create(array $attributes = [])
    {
        if (isset($attributes['slides'])) {
            $slides = $attributes['slides'];
            unset($attributes['slides']);
        }

        $slideshow = parent::create($attributes);

        if (isset($slides)) {
            $slideshow->slides = $slides;
        }

        return $slideshow;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = strip_tags($value);
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
            $slide['url'] = (isset($slide['page']) && $slide['page'] > 0) ?
                $slide['page']
                : isset($slide['url']) ? $slide['url'] : null;

            unset($slide['page']);
            $slide = new Slideshow\Slide($slide);
        }

        $this->attributes['slides'] = $slides;
        $this->slides()->saveMany($slides);
    }

    public function scopeWithRelations($query)
    {
        return $query->with('slides.asset');
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
