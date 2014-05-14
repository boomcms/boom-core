<?php

namespace Boom\Asset\Mimetype;

use \Boom\Asset as Asset;

class Jpg extends Asset\Mimetype
{
	protected $_extension = 'jpg';
	protected $_type = \Boom\Asset\Type::IMAGE;
}