<?php

namespace Boom\TextFilter\Filter;

use \Embera\Embera as Embera;

class OEmbed implements \Boom\TextFilter\Filter
{
    protected function _includeDependencies()
    {
        require \Kohana::find_file('vendor', 'embera/Lib/Embera/Autoload');
    }

    public function filterText($text)
    {
        if ( ! class_exists('Embera')) {
            $this->_includeDependencies();
        }

        $embera = new Embera();

        return $embera->autoEmbed($text);
    }
}
