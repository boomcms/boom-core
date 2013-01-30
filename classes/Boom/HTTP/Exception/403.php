<?php defined('SYSPATH') OR die('No direct script access.');

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
		// Prepare a response object.
		$response = Response::factory()
			->status(403);

		// Is there a page with 403 as the internal name?
		$page = new Model_Page(array(
			'internal_name'	=>	'403',
		));

		if ($page->loaded())
		{
			// The response body will be the result of an internal request to the 403 page.
			$body = Request::factory($page->url())
				->execute()
				->body();
		}
		else
		{
			// Show the default 403 view
			$body = View::factory('boom/errors/403');
		}

		// Set the repsonse body.
		$response->body($body);

		// Return the response object.
		return $response;
	}
}