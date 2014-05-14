<?php

namespace Boom\Asset;

use \Boom\Asset as Asset;

abstract class Command
{
	abstract public function execute(Asset $asset);
}