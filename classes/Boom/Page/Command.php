<?php

namespace Boom\Page;

use \Boom\Page as Page;

abstract class Command
{
	abstract public function execute(Page $page);
}