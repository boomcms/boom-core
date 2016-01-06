<?php

namespace BoomCMS\Http\Controllers\Page\Version;

use BoomCMS\Core\Template;
use Illuminate\Support\Facades\View as ViewFacade;

class View extends Version
{
    public function embargo()
    {
        parent::embargo();

        return ViewFacade::make("$this->viewPrefix.embargo", [
            'version' => $this->page->getCurrentVersion(),
        ]);
    }

    public function status()
    {
        parent::status();

        return ViewFacade::make("$this->viewPrefix.status", [
            'page'    => $this->page,
            'version' => $this->page->getCurrentVersion(),
            'auth'    => auth(),
        ]);
    }

    public function template(Template\Manager $manager)
    {
        parent::template($manager);

        $manager->findAndInstallNewTemplates();
        $templates = $manager->getValidTemplates();

        return ViewFacade::make("$this->viewPrefix.template", [
            'current'     => $this->page->getTemplate(),
            'templates'   => $templates,
        ]);
    }
}
