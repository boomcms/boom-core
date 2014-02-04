<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_Page_Urls extends Boom_Controller
{
	/**
	 *
	 * @var string
	 */
	protected $_view_directory = "boom/editor/urls";

	/**
	 *
	 * @var Model_Page_URL
	 */
	public $page_url;

	public function before()
	{
		parent::before();

		$this->page_url = new Model_Page_URL($this->request->param('id'));
	}

	public function action_add()
	{
		$page = new Model_Page($this->request->query('page_id'));

		if ($location = $this->request->post('location'))
		{
			$location = URL::title(trim($location));

			$this->page_url
				->where('location', '=', $location)
				->find();

			if ($this->page_url->loaded() AND $this->page_url->page_id !== $page->id)
			{
				// Url is being used for a different page.
				// Notify that the url is already in use so that the JS can load a prompt to move the url.
				$this->response->body(json_encode(array('existing_url_id' => $this->page_url->id)));
			}
			elseif ( ! $this->page_url->loaded())
			{
				//  It's not an old URL, so create a new one.
				$this->page_url
					->values(array(
						'location'		=>	$location,
						'page_id'		=>	$page->id,
						'is_primary'	=>	FALSE,
					));

				$this->_security_checks();

				$this->page_url->create();
				$this->log("Added secondary url $location to page " . $page->version()->title . "(ID: " . $page->id . ")");
			}
		}
		else
		{
			$this->template = View::factory("$this->_view_directory/add", array(
				'page'	=> $page,
			));
		}
	}

	public function action_delete()
	{
		$this->_url_must_exist();
		$this->_security_checks();

		if ( ! $this->page_url->is_primary)
		{
			$this->page_url->delete();
		}
	}

	public function action_make_primary()
	{
		$this->_url_must_exist();
		$this->_security_checks();

		$this->page_url->make_primary();
	}

	/**
	 * Move a url from one page to another.
	 */
	public function action_move()
	{
		$this->_url_must_exist();

		if ($this->request->method() == Request::POST)
		{
			$this->_security_checks();

			$this->page_url->values(array(
				'page_id'		=>	$this->request->query('page_id'),
				'is_primary'	=>	FALSE, // Make sure that it's only a secondary url for the this page.
			))
			->update();
		}
		else
		{
			$this->template = View::factory("$this->_view_directory/move", array(
				'url'		=>	$this->page_url,
				'current'	=>	$this->page_url->page,
				'page'	=>	new Model_Page($this->request->query('page_id')),
			));
		}
	}

	protected function _url_must_exist()
	{
		if ( ! $this->page_url->loaded())
		{
			throw new HTTP_Exception_404;
		}
	}

	protected function _security_checks()
	{
		parent::authorization('edit_page_urls', $this->page_url->page);

		$this->_csrf_check();
	}
}