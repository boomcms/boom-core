<?php

namespace BoomCMS\Core\Asset\Mimetype;

use BoomCMS\Core\Asset;

class Pdf extends Asset\Mimetype
{
    protected $_extension = 'pdf';
    protected $_type = \Boom\Asset\Type::PDF;
}
