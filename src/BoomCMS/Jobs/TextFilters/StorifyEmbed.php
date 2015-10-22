<?php

namespace BoomCMS\Jobs\TextFilters;

class StorifyEmbed extends BaseTextFilter
{
    protected $_filterMatchString = "/\<p\>(https?\:\/\/(?:www\.)?storify\.com\/(?:[^\/]+)\/(?:[^\/]+))\/?\<\/p\>/i";
    protected $_filterReplaceString = '<script type="text/javascript" src="${1}.js"></script>';

    public function handle()
    {
        return \preg_replace($this->_filterMatchString, $this->_filterReplaceString, $this->text);
    }
}
