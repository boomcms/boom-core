<?php

namespace Boom\Email;

use Arr;
use Email;
use Kohana;
use Request;
use View;
use Boom\Person;

class Newuser
{
	/**
	 *
	 * @var array
	 */
	protected $config;

	/**
	 *
	 * @var string
	 */
	protected $password;

	/**
	 *
	 * @var Model_Person
	 */
	protected $person;

	/**
	 *
	 * @var Request
	 */
	protected $request;

	/**
	 *
	 * @var string
	 */
	protected $viewFilename = 'boom/email/newuser';

	public function __construct(Person $person, $password, Request $request)
	{
		$this->person = $person;
		$this->password = $password;
		$this->request = $request;
		$this->config = Kohana::$config->load('boom')->as_array();
	}

	protected function _get_content()
	{
		return View::factory($this->viewFilename, array(
			'password' => $this->password,
			'person' => $this->person,
			'request' => $this->request,
			'site_name' =>Arr::get($this->config, 'site_name'),
		));
	}

	public function send() {
		$content = $this->_get_content()->render();
		$this->_send($content);
	}

	protected function _send($content)
	{
		Email::factory('CMS Account Created')
			->to($this->person->getEmail())
			->from(Arr::get($this->config, 'support_email'))
			->message(View::factory('boom/email', array(
				'request' => $this->request,
				'content' => $content,
			)), 'text/html')
			->send();
	}
}