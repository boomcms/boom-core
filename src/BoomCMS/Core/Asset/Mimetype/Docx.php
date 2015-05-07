<?php

namespace BoomCMS\Core\Asset\Mimetype;

use BoomCMS\Core\Asset;

class Docx extends Asset\Mimetype
{
    protected $_extension = 'docx';
    protected $_type = \Boom\Asset\Type::MSWORD;
}
