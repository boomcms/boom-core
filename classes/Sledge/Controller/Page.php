<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays a page for the site.
 * Pages can be displayed in different formats by adding a file extension to the page's URL:
 * 		http://test.com/page.<extension>
 * The actions in this class relate to the supported output formats e.g. action_jpeg outputs the page as a jpeg
 * The default format when none is given is html. When displaying a page as HTML the editor will also be loaded for editing the page if the user is logged in.
 *
 * @package 	Sledge
 * @category 	Controllers
 * @author 	Rob Taylor
 */
class Sledge_Controller_Page extends Sledge_Controller
{
	/**
	 * Whether the editor should be enabled
	 * This is mainly used for rendering the page in HTML format where the editor toolbar will be inserted into the site HTML.
	 * However it's also used for other formats to allow viewing a previous version of the page.
	 *
	 * This property is FALSE by default but will be set to TRUE in Sledge_Controller_Page::before() if the current user is allowed to edit this page and the editor is enabled (i.e. not in preview mode).
	 *
	 * @var	boolean
	 */
	public $editable = FALSE;

	/**
	 * @var	Model_Page	Which page is being displayed. Set in Controller_Site::before() from $this->request->param('page')
	 *
	 */
	public $page;

	/**
	 * Set the page and options properties.
	 */
	public function before()
	{
		// Inherit from parent.
		parent::before();

		// Assign the page we're viewing to Sledge_Controller_Page::$_page;
		$this->page = $this->request->param('page');

		// Should the editor be enabled?
		if ($this->editor->state() == Editor::EDIT AND $this->auth->logged_in('edit_page', $this->_page))
		{
			$this->editable = TRUE;
		}

		// If the page shouldn't be editable then check that it's visible.
		if ( ! $this->editable)
		{
			if ( ! $this->page->is_visible() OR ($this->editor->state() === Editor::DISABLED AND ! $this->page->is_published()))
			{
				throw new HTTP_Exception_404;
			}
		}

		// Check that the page hasn't been deleted at this version.
		if ($this->page->version()->page_deleted)
		{
			throw new HTTP_Exception_404;
		}
	}

	/**
	 * Show a site page
	 *
	 */
	public function action_html()
	{
		$template = ($this->request->query('template'))? ORM::factory('Template', $this->request->query('template')) : $this->page->version()->template;

		// Set some variables which need to be used globally in the views.
		View::bind_global('auth', $this->auth);
		View::bind_global('editor', $this->editor);
		View::bind_global('page', $this->page);

		$html = View::factory(Model_Template::DIRECTORY . $template->filename)
			->render();

		// If we're in the CMS then add the sledge editor the the page.
		if ($this->auth->logged_in())
		{
			$html = $this->editor->insert($html, $this->page->id);
		}

		$this->response->body($html);
	}

	/**
	 * Return details of the page as a json_encoded array
	 */
	public function action_json()
	{
		$this->response
			->headers('Content-Type', 'application/json')
			->body(json_encode(array(
				'id'			=>	$this->page->id,
				'title'			=>	$this->page->version()->title,
				'visible'		=>	$this->page->visible,
				'visible_to'	=>	$this->page->visible_to,
				'visible_from'	=>	$this->page->visible_from,
				'parent'		=>	$this->page->mptt->parent_id,
				'bodycopy'	=>	Chunk::factory('text', 'bodycopy', $this->page)->text(),
				'standfirst'		=>	Chunk::factory('text', 'standfirst', $this->page)->text(),
			)));
	}

	/**
	 * Show an RSS feed of the page.
	 * If the page doesn't have RSS enabled than a 404 is returned.
	 * RSS feeds contain the child pages of the current page.
	 */
	public function action_rss()
	{
		// RSS feeds for a page display a list of the child pages so get the children of the current page.
		// Use the child page plugin to avoid code duplication.
		$pages = Request::factory('plugin/page/children.json')
				->post(array(
					'parent'	=>	$this->page,
					'order'		=>	'visible_from',
				))
				->execute()
				->body();

		$pages = json_decode($pages);

		foreach ($pages as & $page)
		{
			$p = ORM::factory('Page', $page->id);

			$page = array(
				'title'			=>	html_entity_decode($p->version()->title),
				'link'			=>	$page->uri,
				'guid'		=>	$page->uri,
				'description'	=>	strip_tags(Chunk::factory('text', 'standfirst', $p)->text()),
				'pubDate'		=>	$p->visible_from,
			);
		}

		$feed = Feed::create(array(
				'title'	=>	$this->page->title,
				'link'	=>	$this->page->link() . ".rss",
			),
			$pages
		);

		// Send RSS headers.
		$this->response
			->headers('Content-Type', 'application/rss+xml')
			->body($feed);
	}
}