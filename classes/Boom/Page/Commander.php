<?php

namespace Boom\Page;

class Commander
{
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function addCommand(\Boom\Page\Command $command)
    {
        $this->commands[] = $command;

        return $this;
    }

    public function execute()
    {
        if ($this->page->loaded()) {
            foreach ($this->commands as $command) {
                $command->execute($this->page);
            }
        }
    }
}
