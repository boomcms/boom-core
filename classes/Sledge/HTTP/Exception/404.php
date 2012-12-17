<?php defined('SYSPATH') OR die('No direct script access.');

class Sledge_HTTP_Exception_404 extends Kohana_HTTP_Exception_404
{
	/**
	 * Check for a CMS page with the internal name of '404'
	 * If it exists then display that
	 * Otherwise show the sledge/error/404 view.
	 */
	public function get_response()
	{
		// Prepare a response object.
		$response = Response::factory()
			->status(404);

		// If the initial request was an AJAX call then don't set a response body.
		if (Request::initial()->is_ajax())
		{
			// Return the response object.
			return $response;
		}

		// Look for a page with '404' as the internal name.
		$page = new Model_Page(array(
			'internal_name'	=>	404,
		));

		if ($page->loaded())
		{
			// The response body will be the result of an internal request to this page.
			$body = Request::factory($page->link())
				->execute()
				->body();
		}
		else
		{
			// Show the sledge/error/404 view.
			$body = View::factory('sledge/errors/404');
		}

		// Set the response body.
		$response->body($body);

		// Return the response object.
		return $response;
	}
}