<?php

namespace BoomCMS\Core\Controllers\CMS\Page;

use BoomCMS\Core\Page;
use BoomCMS\Core\Controllers\Controller;

class URLs extends Controller
{
    /**
	 *
	 * @var string
	 */
    protected $viewPrefix = "boom::editor.urls";


    public $page_url;

    /**
	 *
	 * @var Page\Page
	 */
    public $page;

    public function before()
    {
        parent::before();

        $this->page_url = new Model_Page_URL($this->request->param('id'));
        $this->page = \Boom\Page\Factory::byId($this->request->query('page_id'));

        if ($this->request->param('id') && ! $this->page_url->loaded()) {
            about(404);
        }

        if ($this->request->query('page_id') && ! $this->page->loaded()) {
            abourt(404);
        }

        if ($this->page->loaded()) {
            parent::authorization('edit_page_urls', $this->page);
        }
    }
}
