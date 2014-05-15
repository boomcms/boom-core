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
	protected $viewDirectory = "boom/editor/urls";

	/**
	 *
	 * @var Model_Page_URL
	 */
	public $page_url;

	/**
	 *
	 * @var Model_Page
	 */
	public $page;

	public function before()
	{
		parent::before();

		$this->page_url = new Model_Page_URL($this->request->param('id'));
		$this->page = \Boom\Finder\Page::byId($this->request->query('page_id'));

		if ($this->request->param('id') && ! $this->page_url->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if ($this->request->query('page_id') && ! $this->page->loaded())
		{
			throw new HTTP_Exception_404;
		}

		if ($this->page->loaded())
		{
			parent::authorization('edit_page_urls', $this->page);
		}
	}
}