<?php

class Boom_Email_Newuser
{
	/**
	 *
	 * @var array
	 */
	protected $_config;

	/**
	 *
	 * @var string
	 */
	protected $_password;

	/**
	 *
	 * @var Model_Person
	 */
	protected $_person;

	/**
	 *
	 * @var Request
	 */
	protected $_request;

	/**
	 *
	 * @var string
	 */
	protected $_view_filename = 'boom/email/newuser';

	public function __construct(Model_Person $person, $password, Request $request)
	{
		$this->_person = $person;
		$this->_password = $password;
		$this->_request = $request;
		$this->_config = Kohana::$config->load('boom')->as_array();
	}

	protected function _get_content()
	{
		return View::factory($this->_view_filename, array(
			'password' => $this->_password,
			'person' => $this->_person,
			'request' => $this->_request,
			'site_name' =>Arr::get($this->_config, 'site_name'),
		));
	}

	public function send() {
		$content = $this->_get_content()->render();
		$this->_send($content);
	}

	protected function _send($content)
	{
		Email::factory('CMS Account Created')
			->to($this->_person->email)
			->from(Arr::get($this->_config, 'support_email'))
			->message(View::factory('boom/email', array(
				'request' => $this->_request,
				'content' => $content,
			)), 'text/html')
			->send();
	}
}