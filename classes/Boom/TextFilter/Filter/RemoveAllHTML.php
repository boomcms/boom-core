<?php

namespace Boom\TextFilter\Filter;

class RemoveAllHTML implements \Boom\TextFilter\Filter
{
	public function filterText($text)
	{
		return \strip_tags($text);
	}
}