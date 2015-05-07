<?php

namespace BoomCMS\Core\TextFilter\Filter;

/**
 * Turns links to assets such as <img src='/asset/view/324'> into munged hoopdb:// links to.
 *
 */
class MungeAssetEmbeds implements \Boom\TextFilter\Filter
{
    public function filterText($text)
    {
        $text = preg_replace('|<img(.*?)src=([\'"])/asset/view/(.*?)([\'"])(.*?)>|', '{image://$3}', $text);
        $text = preg_replace('|<a(.*?)class=[\'"]b-asset-embed[\'"] href=([\'"])/asset/view/(\d+)(.*?)</a>|', '{asset://$3}', $text);

        return $text;
    }
}
