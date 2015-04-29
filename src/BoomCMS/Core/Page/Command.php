<?php

namespace BoomCMS\Core\Page;

abstract class Command
{
    abstract public function execute(Page $page);
}
