<?php

namespace BoomCMS\Core\TextFilter;

class StorifyEmbed implements Filter
{
    protected $_filterMatchString = "/\<p\>(https?\:\/\/(?:www\.)?storify\.com\/(?:[^\/]+)\/(?:[^\/]+))\/?\<\/p\>/i";
    protected $_filterReplaceString = '<script type="text/javascript" src="${1}.js"></script>';

    public function filterText($text)
    {
        return \preg_replace($this->_filterMatchString, $this->_filterReplaceString, $text);
    }
}
