<?php

namespace Boom\Asset\Mimetype;

use \Boom\Asset;

class Xlsx extends Asset\Mimetype
{
    protected $_extension = 'xlsx';
    protected $_type = \Boom\Asset\Type::MSEXCEL;
}
