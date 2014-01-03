<?php

/**
 * Class to store recently visited URLs to redirect to after login / logout.
 * 
 */
class Boom_RecentUrlLogger
{
	/**
	 *
	 * @var Session
	 */
	protected $_session;

	protected $_session_key = 'recent_url_log';
	
	/**
	 *
	 * @var array
	 */
	protected $_urls;

	protected $_urls_to_save = 5;

	public function __construct(Session $session)
	{
		$this->_session = $session;
		$this->_urls = $this->_session->get($this->_session_key, array());
	}

	public function add_url($url)
	{
		array_unshift($this->_urls, $url);
		$this->_urls = array_unique($this->_urls);

		if (count($this->_urls) > $this->_urls_to_save)
		{
			$this->_urls = array_slice($this->_urls, 0, $this->_urls_to_save);
		}

		$this->_save_urls();
	}

	public function get_last_url()
	{
		return isset($this->_urls[0])? $this->_urls[0] : NULL;
	}

	public function get_urls()
	{
		return $this->_urls;
	}

	public function remove_last_url()
	{
		array_shift($this->_urls);
		$this->_save_urls();
	}

	protected function _save_urls()
	{
		$this->_session->set($this->_session_key, $this->_urls);
	}
}