<?php

namespace BoomCMS\Core\Asset\Mimetype;

use \Boom\Asset;

class Jpg extends Asset\Mimetype
{
    protected $_extension = 'jpg';
    protected $_type = \Boom\Asset\Type::IMAGE;
}
