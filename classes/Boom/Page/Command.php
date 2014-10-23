<?php

namespace Boom\Page;

abstract class Command
{
	abstract public function execute(Page $page);
}