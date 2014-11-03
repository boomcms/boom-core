<?php

use \Boom\Link\Link as Link;

class Model_Chunk_Slideshow_Slide extends \ORM
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
    ];

    protected $_table_name = 'chunk_slideshow_slides';

    public function filters()
    {
        return [
            'caption' => [
                ['strip_tags'],
            ],
            'url' => [
                [[$this, 'makeLinkLelative']],
            ],
        ];
    }

    public function getAsset()
    {
        return \Boom\Asset\Factory::fromModel($this->asset);
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
