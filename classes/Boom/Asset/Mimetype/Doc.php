<?php

namespace Boom\Asset\Mimetype;

use \Boom\Asset as Asset;

class Doc extends Asset\Mimetype
{
	protected $_extension = 'doc';
	protected $_type = \Boom\Asset\Type::MSWORD;
}