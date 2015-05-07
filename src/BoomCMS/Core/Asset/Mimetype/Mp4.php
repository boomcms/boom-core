<?php

namespace BoomCMS\Core\Asset\Mimetype;

use \Boom\Asset;

class Mp4 extends Asset\Mimetype
{
    protected $_extension = 'mp4';
    protected $_type = \Boom\Asset\Type::VIDEO;
}
