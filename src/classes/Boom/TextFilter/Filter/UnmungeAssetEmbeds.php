<?php

namespace Boom\TextFilter\Filter;

use \Boom\Editor as Editor;

class UnmungeAssetEmbeds implements \Boom\TextFilter\Filter
{
    public function filterText($text)
    {
        $text = $this->_unmungeImageLinksWithOnlyAssetId($text);
        $text = $this->_unmungeImageLinksWithMultipleParams($text);
        $text = $this->_unmungeNonImageAssetLinks($text);

        return $text;
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
        return preg_replace_callback('|<a.*href=[\'\"]hoopdb://asset/(\d+).*</a>|U', function ($matches) {
                $asset_id = $matches[1];
                $asset = new \Model_Asset($asset_id);

                if ($asset->loaded()) {
                    $text = "<p class='inline-asset'><a class='download ".strtolower(\Boom\Asset\Type::type($asset->type))."' href='/asset/view/{$asset->getId()}.{$asset->get_extension()}'>Download {$asset->getTitle()}</a>";

                    if (Editor::instance()->isDisabled()) {
                        $text .= " (".Text::bytes($asset->filesize)." ".ucfirst(\Boom\Asset\Type::type($asset->type)).")";
                    }

                    $text .= "</p>";

                    return $text;
                }
            }, $text);
    }
}
