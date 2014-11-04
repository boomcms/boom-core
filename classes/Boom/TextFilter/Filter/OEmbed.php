<?php

namespace Boom\TextFilter\Filter;

use \Embera\Embera as Embera;

class OEmbed implements \Boom\TextFilter\Filter
{
    public function filterText($text)
    {
        $embera = new Embera();

        return $embera->autoEmbed($text);
    }
}
