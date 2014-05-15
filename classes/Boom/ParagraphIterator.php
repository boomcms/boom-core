<?php

namespace Boom;

class ParagraphIterator extends \ArrayIterator
{
	public static function fromText($text)
	{
		preg_match_all('|<p>(.*?)</p>|', $this->_chunk->text, $matches, PREG_PATTERN_ORDER);
		$paragraphs = $matches[1];

		return new static($paragraphs);
	}
}