<?php

namespace Boom\TextFilter\Filter;

/**
 * Turns links to assets such as <img src='/asset/view/324'> into munged hoopdb:// links to.
 * 
 */
class MungeAssetLinks implements \Boom\TextFilter\Filter
{
	public function filterText($text)
	{
		$text = preg_replace('|<(.*?)src=([\'"])/asset/view/(.*?)([\'"])(.*?)>|', '<$1src=$2hoopdb://image/$3$4$5>', $text);
		$text = preg_replace('|<(.*?)href=([\'"])/asset/view/(\d+)/?.*?([\'"])(.*?)>|', '<$1href=$2hoopdb://asset/$3$4$5>', $text);

		return $text;
	}
}