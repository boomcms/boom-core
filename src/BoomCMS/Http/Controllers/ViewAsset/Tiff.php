<?php

namespace BoomCMS\Http\Controllers\ViewAsset;

/**
 * Browser support for Tiffs isn't great, so we convert them to png to viewing in browser.
 *
 * They can still be downloaded as normal.
 */
class Tiff extends Image
{
    protected $encoding = 'png';
}
