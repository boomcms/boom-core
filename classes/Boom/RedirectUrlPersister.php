<?php

/**
 * Class to store the last URL visited for redirection after login / logout.
 *
 *
 */
class Boom_RedirectUrlPersister
{
	/**
	 *
	 * @var Session
	 */
	protected $_session;
	protected $_session_key = 'last_url_log';
	protected $_url;

	public function __construct(Session $session)
	{
		$this->_session = $session;
		$this->_url = $this->_session->get($this->_session_key, NULL);
	}

	public function set_url($url)
	{
		$this->_url = $url;
		$this->_save_url();
	}

	public function get_url()
	{
		return $this->_url;
	}

	protected function _save_url()
	{
		$this->_session->set($this->_session_key, $this->_url);
	}
}