<?php

namespace Boom\Asset\Mimetype;

use \Boom\Asset as Asset;

class Mp4 extends Asset\Mimetype
{
	protected $_extension = 'mp4';
	protected $_type = \Boom\Asset\Type::VIDEO;
}