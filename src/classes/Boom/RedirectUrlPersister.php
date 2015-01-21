<?php

namespace Boom;

/**
 * Class to store the last URL visited for redirection after login / logout.
 *
 *
 */
class RedirectUrlPersister
{
    /**
	 *
	 * @var Session
	 */
    protected $_session;
    protected $_session_key = 'last_url_log';
    protected $_url;

    public function __construct(\Session $session)
    {
        $this->_session = $session;
        $this->_url = $this->_session->get($this->_session_key, null);
    }

    public function setUrl($url)
    {
        $this->_url = $url;
        $this->_saveUrl();
    }

    public function getUrl()
    {
        return $this->_url;
    }

    protected function _saveUrl()
    {
        $this->_session->set($this->_session_key, $this->_url);
    }
}
