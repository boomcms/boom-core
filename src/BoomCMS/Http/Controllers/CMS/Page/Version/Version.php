<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Version;

use BoomCMS\Core\Template;
use BoomCMS\Http\Controllers\CMS\Page\PageController;

abstract class Version extends PageController
{
    protected $viewPrefix = 'boomcms::editor.page.version';

    public function embargo()
    {
        $this->authorize('editContent', $this->page);
    }

    public function request_approval()
    {
        $this->authorize('editContent', $this->page);
    }

    public function status()
    {
        $this->authorize('editContent', $this->page);
    }

    public function template(Template\Manager $manager)
    {
        $this->authorize('editTemplate', $this->page);
    }
}
