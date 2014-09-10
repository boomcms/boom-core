<?php

namespace Boom;

/**
 * Class for the CMS editor
 *
 */
class Editor
{
	const EDIT = 1;
	const DISABLED = 2;
	const PREVIEW = 3;

	public static $default = Editor::PREVIEW;
	public static $instance;

	/**
	 *
	 * @var	\Auth
	 */
	protected $_auth;

	protected $_liveTime;
	protected $_liveTimePersistenceKey = 'editor_liveTime';
	protected $_persistentStorage;
	protected $_state;
	protected $_statePersistenceKey = 'editor_state';

	public function __construct(\Auth $auth, \Session $session)
	{
		$this->_auth = $auth;
		$this->_persistentStorage = $session;

		// Determine the default value to pass to Session::get()
		// If the user is logged in then the default is preview, if they're not logged in then it should be disabled.
		$default = ($this->_auth->logged_in())? Editor::$default : Editor::DISABLED;
		$this->_state = $this->_persistentStorage->get($this->_statePersistenceKey, $default);
	}

	/**
	 * Insert the editor iframe into a block of HTML
	 *
	 * @param	string	$html
	 * @param	string	$page_id
	 * @return	string
	 */
	public function insert($html, $page_id)
	{
		// Find the body tag in the HTML. We need to take into account that the body may have attributes assigned to it in the HTML.
		preg_match("|(.*)(</head>)(.*<body[^>]*>)|imsU", $html, $matches);

		if ( ! empty($matches))
		{
			$head = new \View('boom/editor/iframe', array(
				'before_closing_head' => $matches[1],
				'body_tag'	=>	$matches[3],
				'page_id'	=>	$page_id,
			));

			$html = str_replace($matches[0], $head->render(), $html);
		}

		return $html;
	}

	/**
	 *
	 * @return Editor
	 */
	public static function instance()
	{
		if (static::$instance === null)
		{
			static::$instance = new static(\Auth::instance(), \Session::instance());
		}

		return static::$instance;
	}

	public function isDisabled()
	{
		return $this->hasState(static::DISABLED);
	}

	public function isEnabled()
	{
		return $this->hasState(static::EDIT);
	}

	public function hasState($state)
	{
		return ($this->_state == $state);
	}

	public function getState()
	{
		return $this->_state;
	}

	public function getLiveTime()
	{
		if ($this->_liveTime === null)
		{
			$this->_liveTime = $this->_persistentStorage->get($this->_liveTimePersistenceKey, $_SERVER['REQUEST_TIME']);
		}

		return $this->_liveTime;
	}

	public function setState($state)
	{
		$this->_state = $state;
		return $this->_persistentStorage->set($this->_statePersistenceKey, $state);
	}

	/**
	 * The time to use for viewing live pages.
	 * This allows for viewing pages as they were at a certain time in the page.
	 * If no time has been set then the value of $_SERVER['REQUEST_TIME'] is used.
	 *
	 */
	public function setLiveTime($time = null)
	{
		return $this->_persistentStorage>set($this->_liveTimePersistenceKey, $time);
	}
}