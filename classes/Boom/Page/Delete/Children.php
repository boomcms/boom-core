<?php

namespace Boom\Page\Delete;

use \Boom\Page as Page;

class Children extends \Boom\Page\Command
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
		$finder = new \Boom\Finder\Page;
		$finder->addFilter(new \Boom\Finder\Page\Filter\ParentId($page->getId()));

		return $finder->find();
	}

	protected function deletePage(Page $page) {
		$commander = new \Boom\Page\Commander($page);
		$commander
			->addCommand(new FromFeatureBoxes)
			->addCommand(new FromLinksets)
			->addCommand(new static)
			->addCommand(new FlagDeleted)
			->execute();
	}
}