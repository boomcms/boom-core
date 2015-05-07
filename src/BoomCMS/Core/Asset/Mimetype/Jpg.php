<?php

namespace BoomCMS\Core\Asset\Mimetype;

use BoomCMS\Core\Asset;

class Jpg extends Asset\Mimetype
{
    protected $_extension = 'jpg';
    protected $_type = \Boom\Asset\Type::IMAGE;
}
