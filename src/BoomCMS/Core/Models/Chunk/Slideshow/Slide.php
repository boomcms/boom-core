<?php

namespace BoomCMS\Core\Model\Chunk\Slideshow;

use Illuminate\Database\Eloquent\Model;
use \Boom\Link\Link as Link;

class Slide extends Model
{
    protected $_belongs_to = [
        'asset'    =>    ['model' => 'Asset', 'foreign_key' => 'asset_id']
    ];

    protected $_table_columns = [
        'id'        =>    '',
        'asset_id'    =>    '',
        'url'        =>    '',
        'chunk_id'    =>    '',
        'caption'    =>    '',
        'title'        =>    '',
        'linktext' => '',
    ];

    protected $table = 'chunk_slideshow_slides';

    private $assetCache;

    public function filters()
    {
        return [
            'caption' => [
                ['strip_tags'],
            ],
            'url' => [
                [[$this, 'makeLinkLelative']],
            ],
            'link_text' => [
                ['strip_tags'],
            ],
        ];
    }

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

    public function makeLinkLelative($url)
    {
        return ($base = \URL::base(\Request::current())) ? str_replace($base, '/', $url) : $url;
    }
}
