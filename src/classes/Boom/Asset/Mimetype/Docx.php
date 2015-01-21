<?php

namespace Boom\Asset\Mimetype;

use \Boom\Asset;

class Docx extends Asset\Mimetype
{
    protected $_extension = 'docx';
    protected $_type = \Boom\Asset\Type::MSWORD;
}
