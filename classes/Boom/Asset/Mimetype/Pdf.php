<?php

namespace Boom\Asset\Mimetype;

use \Boom\Asset as Asset;

class Pdf extends Asset\Mimetype
{
    protected $_extension = 'pdf';
    protected $_type = \Boom\Asset\Type::PDF;
}
