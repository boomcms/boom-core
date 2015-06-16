<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Version;

use BoomCMS\Core\Controllers\CMS\Page\PageController;
use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Template;
use BoomCMS\Core\Page;

use Illuminate\Http\Request;

abstract class Version extends PageController
{
    /**
	 *
	 * @var	Page\Version
	 */
    public $oldVersion;

    protected $viewPrefix = 'boom::editor.page.version';

    public function __construct(Page\Provider $provider, Auth $auth, Request $request)
    {
		parent::__construct($provider, $auth, $request);

		$this->oldVersion = $this->page->getCurrentVersion();
    }

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
