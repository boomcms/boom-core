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
	protected $_editable = FALSE;

	/**
	 * @var	Model_Page	Which page is being displayed. Set in Controller_Site::before() from $this->request->param('page')
	 *
	 */
	protected $_page;

	/**
	 * Set the page and options properties.
	 */
	public function before()
	{
		// Inherit from parent.
		parent::before();

		// Assign the page we're viewing to Sledge_Controller_Page::$_page;
		$this->_page = $this->request->param('page');

		// Should the editor be enabled?
		if ($this->editor->state() == Editor::EDIT AND $this->auth->logged_in('edit_page', $this->_page))
		{
			$this->_editable = TRUE;
		}

		// If the page shouldn't be editable then check that it's visible.
		if ( ! $this->_editable)
		{
			if ( ! $this->_page->is_visible() OR ($this->editor->state() === Editor::DISABLED AND ! $this->_page->is_published()))
			{
				throw new HTTP_Exception_404;
			}
		}

		// If the editor is enabled and a version ID has been given in the query string display the specified version.
		// Otherwise show the default version (most recent for editors, most recent published for site).
		if ($this->_editable AND $this->request->query('version'))
		{
			$version = ORM::factory('Page_Version', $this->request->query('version'));

			// Check that this version belongs to the current page.
			if ($version->page_id != $this->_page->id)
			{
				// Page IDs don't match, throw a 500 error.
				throw new HTTP_Exception_500;
			}

			// Set the version with the page.
			$this->_page->version($version);
		}

		// Check that the page hasn't been deleted at this version.
		if ($this->_page->version()->page_deleted)
		{
			throw new HTTP_Exception_404;
		}
	}

	/**
	 * Show a site page
	 *
	 *	@todo	This should be in two functions really - one for read-only and one for editable. But how best to do it?
	 */
	public function action_html()
	{
		$template = ($this->request->query('template'))? ORM::factory('Template', $this->request->query('template')) : $this->_page->version()->template;

		// Set some variables which need to be used globally in the views.
		View::bind_global('page', $this->_page);
		View::bind_global('auth', $this->auth);
		View::bind_global('request', $this->request);
		View::bind_global('editor', $this->editor);

		$html = View::factory(Model_Template::DIRECTORY . $template->filename)
			->render();

		// If we're in the CMS then add the sledge editor the the page.
		if ($this->auth->logged_in())
		{
			// Find the body tag in the HTML. We need to take into account that the body may have attributes assigned to it in the HTML.
			preg_match("|(</head>.*<body[^>]*>)|imsU", $html, $matches);

			if (isset($matches[0]))
			{
				$body_tag = $matches[0];

				// Add the editor iframe to just after the <body> tag.
				$head = View::factory('sledge/editor/head');
				$head->set('body_tag', $body_tag);
				$html = str_replace($body_tag, $head->render(), $html);
			}
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
				'id'			=>	$this->_page->id,
				'title'			=>	$this->_page->version()->title,
				'visible'		=>	$this->_page->visible,
				'visible_to'	=>	$this->_page->visible_to,
				'visible_from'	=>	$this->_page->visible_from,
				'parent'		=>	$this->_page->mptt->parent_id,
				'bodycopy'	=>	Chunk::factory('text', 'bodycopy', $this->_page)->text(),
				'standfirst'		=>	Chunk::factory('text', 'standfirst', $this->_page)->text(),
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
					'parent'	=>	$this->_page,
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
				'title'	=>	$this->_page->title,
				'link'	=>	$this->_page->link() . ".rss",
			),
			$pages
		);

		// Send RSS headers.
		$this->response
			->headers('Content-Type', 'application/rss+xml')
			->body($feed);
	}
}