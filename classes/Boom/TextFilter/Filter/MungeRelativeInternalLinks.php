<?php

namespace Boom\TextFilter\Filter;

use \Boom\Page\Factory as PageFactory;

/**
 * Munges internal links to hoopdb://page/<pageId>
 *
 * Assumes that the MakeInternalLinksRelative filter has been applied to the block of text first
 * As this filter only checks for internal links.
 *
 * This filter also needs to run before any other filters which work with internal links (e.g. RemoveLinksToInvisiblePages)
 * As those filters look for hoopdb://page/<pageId>
 *
 */
class MungeRelativeInternalLinks implements \Boom\TextFilter\Filter
{
    public function filterText($text)
    {
        return preg_replace_callback('|(<.*href=[\'"])/([-\w/]+\K(?!/asset/.*))([\'"].*>)|U', [$this, '_mungeLink'], $text);
    }

    protected function _mungeLink($matches)
    {
        return $matches[1] . 'hoopdb://page/' . $this->_getPageIdForUri($matches[2]) . $matches[3];
    }

    protected function _getPageIdForUri($uri)
    {
        $page = PageFactory::byPrimaryUri($uri);

        return $page->getId();
    }
}
