<?php

namespace Boom\Model\Chunk;

use Illuminate\Database\Eloquent\Model;

/**
 * Records which assets are being referenced from within text chunks
 * When a text chunk is saved regular expressions are used to find links to CMS assets.
 * Recording these allows us to show in the asset manager where an asset is used.
 */
class Text_Asset extends Model
{
    protected $_primary_key = null;
    protected $_belongs_to = ['asset' => []];
    protected $_table_columns = [
        'chunk_id'    =>    '',
        'asset_id'    =>    '',
        'position'    =>    '',
    ];

    protected $table = 'chunk_text_assets';
}
