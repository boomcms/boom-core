<?php defined('SYSPATH') OR die('No direct script access.');

class Sledge_HTTP_Exception_500 extends Kohana_HTTP_Exception_500
{
	/**
	 * Check for a CMS page with the internal name of '500'
	 * If it exists then display that
	 * Otherwise show the sledge/error/500 view.
	 */
	public function get_response()
	{
		// Prepare a response object.
		$response = Response::factory()
			->status(500);

		// If the initial request was an AJAX call then only send a single line body
		if (Request::initial()->is_ajax())
		{
			// Get a single line representation of the exception.
			$body = $this->getMessage();
		}
		else
		{
			// Look for a page with '500' as the internal name.
			$page = new Model_Page(array(
				'internal_name'	=>	500,
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
				// Show the sledge/error/500 view.
				$body = View::factory('sledge/errors/500');
			}
		}

		// Set the response body.
		$response->body($body);

		// Return the response object.
		return $response;
	}
}