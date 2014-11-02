<?php

class Boom_HTTP_Exception_403 extends Kohana_HTTP_Exception_403
{
    /**
	 * HTTP 403 handling via the CMS.
	 *
	 * Look for a page with '403' as the internal name.
	 * If that page doesn't exist then show the boom/errors/403 view.
	 */
    public function get_response()
    {
        return $this->boom_response();
    }
}
