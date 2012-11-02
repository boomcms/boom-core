<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package Sledge
* @category Controllers
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*/
class Sledge_Controller_Plugin_Createsend extends Sledge_Controller
{
	/**
	* Build a validation object for the form data.
	* This is put into a separate method so that the validation rules can be customised on a site-by-site basis.
	*
	* @return Validation
	*/
	protected function _get_validation()
	{
		$validation = Validation::factory($this->request->post());
		$validation->rule('email', 'not_empty');
		$validation->rule('name', 'not_empty');
		$validation->rule('email', 'email');

		return $validation;
	}

	/**
	* Submit the form data to createsend.
	* Again, this is a separate function so it can be changed on a site-by-site basis.
	*
	* @param CS_REST_Subscribers $wrap
	*/
	protected function _submit_data( CS_REST_Subscribers $wrap)
	{
		$result = $wrap->add(array(
			'EmailAddress' => $this->request->post('email'),
			'Name' => $this->request->post('name'),
			'Resubscribe' => TRUE
		));

		return $result;
	}

	/**
	* Process a signup to an email list in createsend.
	*/
	public function action_signup()
	{
		// Was the form submitted or is it being displayed for the first time?
		if ($this->request->post('submit'))
		{
			// Make sure we've got an email address and password.
			$validation = $this->_get_validation();
		
			if ($validation->check())
			{
				// Include the Createsend API.
				require Kohana::find_file('vendor', 'createsend/csrest_subscribers');

				// Get a list ID and API key from the application config.
				$list_id = Kohana::$config->load('createsend')->get('list_id');
				$api_key = Kohana::$config->load('createsend')->get('api_key');
			
				if ( ! $list_id OR ! $api_key)
				{
					throw new Sledge_Exception("Site has not been configured for newsletter signups.");
				}

				// Submit the data to createsend.
				$wrap = new CS_REST_Subscribers($list_id, $api_key);
				$result = $this->_submit_data($wrap);

				if ($result->was_successful())
				{
					// Display a success message.
					// This template can be overloaded on a per site basis to change the success message.
					$this->template = View::factory('sledge/plugin/createsend/success');
				}
				else
				{
					// Createsend has returned an error message.
					// Display the signup form again with the response from createsend.
					$this->template = View::factory('sledge/plugin/createsend/form');

					$this->template->cs_error = $result->response->Message; 
				}
			}
			else
			{
				// Validation errors: show the form with validation messages.
				$this->template = View::factory('sledge/plugin/createsend/form');
				$this->template->errors = $validation->errors('createsend');
			}
		}
		else
		{
			// Show the signup form.
			// The signup form can be changed on a site-by-site basis.
			$this->template = View::factory('sledge/plugin/createsend/form');
		}
	}
} 