<?php defined('SYSPATH') OR die('No direct script access.');

class Sledge_HTTP_Exception_403 extends Kohana_HTTP_Exception_403
{
	/**
	 * HTTP 403 handling via the CMS.
	 * If the user isn't logged in then redirect them to the login page.
	 * If they are logged in look for a page with '403' as the internal name.
	 * If that page doesn't exist then show the sledge/errors/403 view.
	 */
	public function get_response()
	{
		// Prepare a response object.
		$response = Response::factory()
			->status(403);

		if ( ! Auth::instance()->logged_in())
		{
			// Redirect to the CMS login page.
			$response->headers('Location', URL::site('cms/login'));
		}
		else
		{
			// Is there a page with 403 as the internal name?
			$page = ORM::factory('Page', array(
				'internal_name'	=>	'403',
			));

			if ($page->loaded())
			{
				// The response body will be the result of an internal request to the 403 page.
				$body = Request::factory($page->link())
					->execute()
					->body();
			}
			else
			{
				// Show the default 403 view
				$body = View::factory('sledge/error/403');
			}

			// Set the repsonse body.
			$response->body($body);
		}

		// Return the response object.
		return $response;
	}
}