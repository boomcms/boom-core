<?php

class HTTP_Exception_500 extends Kohana_HTTP_Exception_500
{
    /**
	 * Check for a CMS page with the internal name of '500'
	 * If it exists then display that
	 * Otherwise show the boom/error/500 view.
	 */
    public function get_response()
    {
        return $this->boom_response();
    }
}
