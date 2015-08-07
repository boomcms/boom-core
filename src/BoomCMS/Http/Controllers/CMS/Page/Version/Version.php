<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Version;

use BoomCMS\Core\Template;
use BoomCMS\Http\Controllers\CMS\Page\PageController;

abstract class Version extends PageController
{
    protected $viewPrefix = 'boom::editor.page.version';

    public function embargo()
    {
        $this->authorization('edit_page_content', $this->page);
    }

    public function request_approval()
    {
        $this->authorization('edit_page_content', $this->page);
    }

    public function status()
    {
        $this->authorization('edit_page_content', $this->page);
    }

    public function template(Template\Manager $manager)
    {
        $this->authorization('edit_page_template', $this->page);
    }
}
