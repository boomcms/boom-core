<?php

namespace Boom\TextFilter\Filter;

use \Boom\Asset;

class UnmungeAssetEmbeds implements \Boom\TextFilter\Filter
{
    public function filterText($text)
    {
        $text = $this->_unmunge_new_style_image_embeds($text);
        $text = $this->_unmungeImageLinksWithOnlyAssetId($text);
        $text = $this->_unmungeImageLinksWithMultipleParams($text);
        $text = $this->_unmungeNonImageAssetLinks($text);

        return $text;
    }

    protected function _unmunge_new_style_image_embeds($text)
    {
        return preg_replace('|{image://(.*?)}|', '<img src="/asset/view/$1" />', $text);
    }

    protected function _unmungeImageLinksWithOnlyAssetId($text)
    {
        return preg_replace('|hoopdb://image/(\d+)([\'"])|', '/asset/view/$1$2', $text);
    }

    protected function _unmungeImageLinksWithMultipleParams($text)
    {
        return preg_replace('|hoopdb://image/(\d+)/|', '/asset/view/$1/', $text);
    }

    protected function _unmungeNonImageAssetLinks($text)
    {
        return preg_replace_callback('|{asset://(\d+?)}|', function ($matches) {
                $assetId = $matches[1];
                $asset = Asset\Factory::byId($assetId);

                if ($asset->loaded()) {
                    return "<a class='b-asset-embed' href='/asset/view/{$asset->getId()}.{$asset->getExtension()}'>{$asset->getTitle()}</a>";
                }
            }, $text);
    }
}
