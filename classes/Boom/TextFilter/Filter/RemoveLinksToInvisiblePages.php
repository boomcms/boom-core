<?php

namespace Boom\TextFilter\Filter;

use \Boom\Finder\Page as PageFinder;

class RemoveLinksToInvisiblePages implements \Boom\TextFilter\Filter
{
	protected $_regex = "|(<a.*href=['\"]hoopdb://page/(\d+)['\"].*>)(.*)(</a>)|U";

	public function filterText($text)
	{
		preg_match_all($this->_regex, $text, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			$page = PageFinder::byId($match[2]);

			if ( ! $page->isVisibleAtTime(time())) {
				preg_replace($this->_regex, $match[3], $text);
			}
		}

		return $text;
	}
}