<?php

namespace Boom\TextFilter\Filter;

use \Boom\Page\Factory as PageFactory;

class UnmungeInternalLinks implements \Boom\TextFilter\Filter
{
    protected $_internalLinkRegex = '(hoopdb://page/(\d+))';

    public function filterText($text)
    {
        $text = preg_replace_callback("|{$this->_internalLinkRegex}|", array($this, '_updatePageLink'), $text);
        $text = $this->_removeInvalidInternalLinks($text);

        return $text;
    }

    protected function _removeInvalidInternalLinks($text)
    {
        return preg_replace("|(<a.*href=['\"]{$this->_internalLinkRegex}['\"].*>)(.*)(</a>)|U", "$2", $text);
    }

    protected function _updatePageLink($match)
    {
        $page = PageFactory::byId($match[2]);

        return $page->loaded() ? $page->url() : $match[0];
    }
}
