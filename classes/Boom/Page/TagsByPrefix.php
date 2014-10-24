<?php

namespace Boom\Page;

use \Boom\Page\Page as Page;
use \Boom\Tag\Finder as TagFinder;

class TagsByPrefix
{
	protected $prefix = '';

	/**
	 *
	 * @var array
	 */
	protected $tags;

	/**
	 *
	 * @var \Page
	 */
	protected $page;

	public function __construct(Page $page)
	{
		$this->page = $page;
		$this->tags = $this->getTags();
	}

	public function getNames()
	{
		$names = array();

		if ( ! empty($this->tags))
		{
			foreach ($this->tags as $tag)
			{
				$names[] = htmlentities(str_ireplace($this->prefix, '', $tag->getName()), ENT_QUOTES);
			}
		}

		return $names;
	}

	public function getTags()
	{
		$finder = new TagFinder;

		return $finder
			->addFilter(new TagFinder\Filter\Page($this->page))
			->addFilter(new TagFinder\Filter\NameBeginsWith($this->prefix))
			->findAll();
	}

	public function __toString()
	{
		return implode(', ', $this->getNames());
	}
}