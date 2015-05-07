<?php

namespace BoomCMS\Core\Page\Command\Delete;

use BoomCMS\Core\Page\Page as Page;

class Children extends Page\Command
{
    public function execute(Page $page)
    {
        $children = $this->getChildren($page);

        foreach ($children as $child) {
            $this->deletePage($child);
        }
    }

    protected function getChildren(Page $page)
    {
        $finder = new \Boom\Page\Finder();
        $finder->addFilter(new \Boom\Page\Finder\Filter\ParentId($page->getId()));

        return $finder->find();
    }

    protected function deletePage(Page $page)
    {
        $commander = new \Boom\Page\Commander($page);
        $commander
            ->addCommand(new FromFeatureBoxes())
            ->addCommand(new FromLinksets())
            ->addCommand(new static())
            ->addCommand(new FlagDeleted())
            ->execute();
    }
}
