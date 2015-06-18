<?php

namespace BoomCMS\Core\Model\Chunk;

use Illuminate\Database\Eloquent\Model;

/**
 * Records which assets are being referenced from within text chunks
 * When a text chunk is saved regular expressions are used to find links to CMS assets.
 * Recording these allows us to show in the asset manager where an asset is used.
 */
class Asset extends Model
{
    protected $table = 'chunk_text_assets';
}
