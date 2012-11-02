<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Sledge base controller.
* Contains components common to site and cms and controllers.
* @package Sledge
* @category Controllers
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Sledge_Controller extends Kohana_Controller
{
	/**
	* The current user.
	* @access protected
	* @var object
	*/
	protected $person;

	/**
	* The real current user.
	* Used for Hoop users to pose as a different user.
	* @access protected
	* @var object
	*/
	protected $actual_person;

	/**
	* Holds the auth instance.
	* @access protected
	* @var object
	*/
	protected $auth;

	/**
	* Holds the config variables.
	* @access protected
	* @var array
	*/
	protected $config;

	protected $template;

	public function before()
	{
		$this->auth = Auth::instance();
		$this->config = Kohana::$config->load('config');

		// Require the user to be logged in if the site isn't live.
		// skipenvcheck is sent for template previews to bypass the login requirement.

		if (Kohana::$environment != Kohana::PRODUCTION AND $this->request->is_initial() AND ! ($this->auth->logged_in() OR $this->request->query('skipenvcheck') == 1))
		{
			throw new HTTP_Exception_403;
		}

		// Force HTTPS when logged in.
		if ($this->auth->logged_in() AND $this->request->is_initial() AND ! $this->request->protocol() == 'HTTPS')
		{
			$this->redirect(URL::site($this->request->detect_uri(), 'https'));
		}

		/** CSRF security check.
		* If there is an input named 'csrf' in POST / GET then validate it.
		* @link http://thisishoop3/index.php/guide/api/Security#token
		*/
		if ($this->request->post('csrf') OR $this->request->query('csrf'))
		{
			$token = ($this->request->method() === Request::POST)? $this->request->post('csrf') : $this->request->query('csrf');

			if ( ! Security::check($token))
			{
				throw new HTTP_Exception_500;
			}
		}

		// Who are we?
		$this->person = $this->auth->get_user();
		$this->actual_person = $this->auth->get_real_user();
	}

	public function after()
	{
		if ( ! $this->response->body() AND $this->template instanceof View)
		{
			parent::after();

			// Set some variables.
			View::bind_global('person', $this->person);
			View::bind_global('actual_person', $this->actual_person);
			View::bind_global('request', $this->request);
			View::bind_global('auth', $this->auth);

			// Show the template.
			$this->response->body($this->template);
		}

		// Only cache by etag here if a cache-control header hasn't already been sent.
		// If the header has already been sent then it's probably because the controller has handled it's own caching (e.g. the asset controller).
		if ( ! $this->response->headers('Cache-Control') AND $this->response->body())
		{
			// Make the cache public for logged-out requests, private for CMS requests.
			$cache = ($this->auth->logged_in())? 'private' : 'public';
			$this->response->headers('Cache-Control', $cache);
			// $this->response->check_cache(NULL, $this->request);
		}
	}
}
