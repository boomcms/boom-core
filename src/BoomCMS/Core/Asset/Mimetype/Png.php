<?php

namespace BoomCMS\Core\Asset\Mimetype;

use BoomCMS\Core\Asset;

class Png extends Asset\Mimetype
{
    protected $_extension = 'png';
    protected $_type = \Boom\Asset\Type::IMAGE;
}
