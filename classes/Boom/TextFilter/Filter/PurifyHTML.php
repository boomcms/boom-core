<?php

namespace Boom\TextFilter\Filter;

class PurifyHTML implements \Boom\TextFilter\Filter
{
	public function filterText($text)
	{
		if ( ! class_exists('HTMLPurifier')) {
			$this->_includeDependencies();
		}

		$config = \HTMLPurifier_Config::createDefault();
		$config->loadArray(\Kohana::$config->load('htmlpurifier'));

		$purifier = new \HTMLPurifier($config);
		return $purifier->purify($text);
	}

	protected function _includeDependencies()
	{
		require \Kohana::find_file('vendor', 'htmlpurifier/library/HTMLPurifier.auto');
	}
}