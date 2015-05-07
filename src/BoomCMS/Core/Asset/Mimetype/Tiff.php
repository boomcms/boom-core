<?php

namespace BoomCMS\Core\Asset\Mimetype;

use BoomCMS\Core\Asset;

class Tiff extends Asset\Mimetype
{
    protected $_extension = 'tiff';
    protected $_type = \Boom\Asset\Type::IMAGE;
}
