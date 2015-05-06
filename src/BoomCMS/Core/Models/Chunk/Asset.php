<?php

namespace Boom\Model\Chunk;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $_belongs_to = [
        'target' => ['model' => 'Asset', 'foreign_key' => 'asset_id'],
    ];

    protected $_table_columns = [
        'id'            =>    '',
        'asset_id'        =>    '',
        'title'            =>    '',
        'caption'        =>    '',
        'url'            =>    '',
        'slotname'        =>    '',
        'page_vid' => '',
    ];

    protected $table = 'chunk_assets';

    public function filters()
    {
        return [
            'title'    => [
                ['strip_tags'],
            ],
            'caption'    => [
                ['strip_tags'],
            ],
            'url' => [[
                function ($url) {
                    $link = Boom\Link\Link::factory($url);

                    return $link->isInternal() ? $link->getPage()->getId() : $link->url();
                },
            ]],
        ];
    }
}
