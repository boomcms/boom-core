<?php

namespace Boom;

class Page extends \Boom\Model\Page
{
	/**
	 * Generate a short URL for the page, similar to t.co etc.
	 * Returns the page ID encoded to base-36 prefixed with an underscore.
	 * We prefix the short URLs to avoid the possibility of conflicts with real URLs
	 *
	 * @return 	string
	 */
	public function shortUrl()
	{
		return "_" . base_convert($this->id, 10, 36);
	}
}