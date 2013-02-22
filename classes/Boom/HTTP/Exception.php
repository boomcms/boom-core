<?php defined('SYSPATH') OR die('No direct script access.');

class Boom_HTTP_Exception extends Kohana_HTTP_Exception
{
	/**
	 * Boom HTTP exception handler
	 *
	 * Check for a CMS page with the internal name that matches the status code.
	 * If it exists then display that
	 * Otherwise show the boom/error/<status code> view.
	 */
	public function boom_response()
	{
		// Prepare a response object.
		$response = Response::factory()->status($this->_code);

		// If the initial request was an AJAX call then don't set a response body.
		if (Request::initial()->is_ajax())
		{
			// Return the response object.
			return $response;
		}

		// Look for a page an internal name which matches the status code.
		$page = new Model_Page(array('internal_name' => $this->_code));

		if ($page->loaded())
		{
			// The response body will be the result of an internal request to this page.
			$body = Request::factory($page->url()->location)
				->execute()
				->body();
		}
		else
		{
			$view_filename = "boom/errors/$this->_code";

			// Does a boom view for this status code exist?
			if (Kohana::find_file('views', $view_filename))
			{
				// Yes, use the contents of this view as the response body.
				$body = View::factory("boom/errors/$this->_code");
			}
			else
			{
				// No, use Kohana's error handler.
				return parent::get_response();
			}
		}

		// Set the response body.
		$response->body($body);

		// Return the response object.
		return $response;
	}
}