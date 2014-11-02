<?php

class Boom_HTTP_Exception_401 extends Kohana_HTTP_Exception_401
{
    /**
	 * HTTP 401 handling via the CMS.
	 *
	 * Redirect the user to the login page.
	 */
    public function get_response()
    {
        // Return a response object.
        return Response::factory()
            ->status(401)
            ->headers('Location', URL::site('cms/login'));
    }
}
