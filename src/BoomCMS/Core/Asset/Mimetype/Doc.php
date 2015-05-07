<?php

namespace BoomCMS\Core\Asset\Mimetype;

use BoomCMS\Core\Asset;

class Doc extends Asset\Mimetype
{
    protected $_extension = 'doc';
    protected $_type = \Boom\Asset\Type::MSWORD;
}
