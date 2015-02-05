<?php

class HTTP_Exception_404 extends Kohana_HTTP_Exception_404
{
    /**
	 * Check for a CMS page with the internal name of '404'
	 * If it exists then display that
	 * Otherwise show the boom/error/404 view.
	 */
    public function get_response()
    {
        return $this->boom_response();
    }
}
