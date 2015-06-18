<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Version;

use BoomCMS\Core\Controllers\CMS\Page\PageController;
use BoomCMS\Core\Template;

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

    public function template(Template\Manager $manager)
    {
        $this->authorization('edit_page_template', $this->page);
    }
}
