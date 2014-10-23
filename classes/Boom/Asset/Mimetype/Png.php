<?php

namespace Boom\Asset\Mimetype;

use \Boom\Asset as Asset;

class Png extends Asset\Mimetype
{
	protected $_extension = 'png';
	protected $_type = \Boom\Asset\Type::IMAGE;
}