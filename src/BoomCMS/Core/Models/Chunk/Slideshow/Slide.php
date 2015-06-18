<?php

namespace BoomCMS\Core\Model\Chunk\Slideshow;

use Illuminate\Database\Eloquent\Model;
use BoomCMS\Core\URL\Helpers as URL;
use BoomCMS\Core\Link\Link;

class Slide extends Model
{
    protected $table = 'chunk_slideshow_slides';

    private $assetCache;

    public function getAsset()
    {
        if ($this->assetCache === null) {
            $this->assetCache = \Boom\Asset\Factory::fromModel($this->asset);
        }

        return $this->assetCache;
    }

    /**
	 * @return Link
	 */
    public function getLink()
    {
        return Link::factory($this->url);
    }

    /**
	 * Whether the current slide has a link associated with it.
	 *
	 * @return boolean
	 */
    public function hasLink()
    {
        return $this->url && $this->url != 'http://';
    }
	
	public function setCaptionAttribute($value)
	{
		$this->attributes['caption'] = strip_tags($value);
	}
	
	public function setLinkTextAttribute($value)
	{
		$this->attributes['link_text'] = strip_tags($value);
	}
	
	public function setUrlAttribute($value)
	{
		$this->attributes['url'] = URL::makeRelative($url);
	}
}
