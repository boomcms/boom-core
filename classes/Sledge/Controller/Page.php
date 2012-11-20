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
	 * @var	Model_Page	Which page is being displayed. Set in Controller_Site::before() from $this->request->param('page')
	 *
	 */
	public $page;

	/**
	 * Set the default template.
	 * Used by Controller_Template to know which template to use.
	 * @link http://kohanaframework.org/3.0/guide/kohana/tutorials/hello-world#that-was-good-but-we-can-do-better
	 * @access public
	 * @var string
	 */
	public $template;

	/**
	 * Set the page and options properties.
	 */
	public function before()
	{
		parent::before();

		$this->page = $this->request->param('page');

		// Make the page available to views.
		View::bind_global('page', $this->page);
		View::bind_global('config', $this->config);
		View::bind_global('person', $this->person);
		View::bind_global('actual_person', $this->actual_person);
		View::bind_global('auth', $this->auth);
		View::bind_global('request', $this->request);
	}

	/**
	 * Show a site page
	 *
	 *	@todo	This should be in two functions really - one for read-only and one for editable. But how best to do it?
	 */
	public function action_html()
	{
		$mode = (Editor::state() == Editor::EDIT AND $this->auth->logged_in('edit_page', $this->page))? 'cms' : 'site';

		// Enables viewing a previous version of a page.
		if ($this->request->query('version') !== NULL AND $mode == 'cms')
		{
//			$this->page = ORM::factory('page_version', $this->request->query('version'));
		}


		// If they can't edit the page check that it's visible.
		if ($mode == 'site' OR Editor::state() != Editor::EDIT)
		{
			if ( ! $this->page->is_visible() OR (Editor::state() == Editor::PREVIEW_PUBLISHED AND ! $this->page->is_published()))
			{
				throw new HTTP_Exception_404;
			}
		}

		$template = ($this->request->query('template'))? ORM::factory('Template', $this->request->query('template')) : $this->page->template;

		// If no template has been set or the template file doesn't exist then use the orange template.
		$filename = ($template->loaded() AND Kohana::find_file('views', Sledge::TEMPLATE_DIR . $template->filename))? $template->filename : 'orange';

		$html = View::factory(Sledge::TEMPLATE_DIR . $filename)->render();

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
	 * Display a page as a jpeg
	 * Uses wkhtmltoimage and snappy
	 * @link https://github.com/KnpLabs/snappy
	 */
	public function action_jpeg()
	{
		// Name of the cache file.
		$file = APPPATH . 'cache/' . md5($this->page->link() . serialize($this->request->query()));

		// When the cache file was last modified. Used to determine whether the file should be regenerated and HTTP last-modified header.
		$filemtime = file_exists($file)? filemtime($file) : 0;

		// Only create the cache file if it doesn't already exist or it's over an hour old.
		if ($filemtime <= $_SERVER['REQUEST_TIME'] - 3600)
		{
			// Send the current query params for reviewing a page with a template.
			// Send skipenvcheck as this will prevent us being forwarded to the login page.
			$query = http_build_query(array_merge($this->request->query(), array('skipenvcheck' => 1)));

			// Turn the HTML into a jpeg.
			require_once(Kohana::find_file('vendor', 'snappy/src/autoload'));

			try
			{
				$snappy = new Knp\Snappy\Image('wkhtmltoimage');
				$snappy->generate($this->page->link() . "?$query", $file, array(), TRUE);
			}
			catch (Exception $e)
			{
				@unlink($file);
				throw $e;
			}

			// Set the file modified time.
			$filemtime = $_SERVER['REQUEST_TIME'];
		}

		if ($this->request->headers('If-Modified-Since') AND strtotime($this->request->headers('If-Modified-Since')) >= $filemtime)
		{
			$this->response->status(304);
		}
		else
		{
			try
			{
				$image = Image::factory($file);
				$image->resize(NULL, 768);

				$this->response->headers('content-type', 'image/jpeg');
				$this->response->headers('Cache-Control', 'must-revalidate, public');
				$this->response->headers('Expires', gmdate(DATE_RFC1123, $_SERVER['REQUEST_TIME'] + 3600));
				$this->response->headers('Last-Modified', gmdate(DATE_RFC1123, $filemtime));
				$this->response->body($image->render());
			}
			catch (Exception $e)
			{
				@unlink($file);

				$this->response->status(500);
				$this->response->body();
			}
		}
	}

	/**
	 * Return details of the page as a json_encoded array
	 */
	public function action_json()
	{
		$this->response->headers('Content-Type', 'application/json');

		$this->response->body(json_encode(array(
			'id'			=>	$this->page->id,
			'title'			=>	$this->page->title,
			'visible'		=>	$this->page->visible,
			'visible_to'	=>	$this->page->visible_to,
			'visible_from'	=>	$this->page->visible_from,
			'parent'		=>	$this->page->mptt->parent_id,
			'bodycopy'		=>	Chunk::factory('text', 'bodycopy', $this->page)->text(),
			'standfirst'	=>	Chunk::factory('text', 'standfirst', $this->page)->text(),
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
				'title'			=>	html_entity_decode($p->title),
				'link'			=>	$page->uri,
				'guid'			=>	$page->uri,
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