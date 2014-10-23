<?php

namespace Boom\Asset\Mimetype;

use \Boom\Asset as Asset;

class Gif extends Asset\Mimetype
{
	protected $_extension = 'gif';
	protected $_type = \Boom\Asset\Type::IMAGE;
}