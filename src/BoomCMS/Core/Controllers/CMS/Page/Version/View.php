<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Version;

use BoomCMS\Core\Template;
use Illuminate\Support\Facades\View as ViewFacade;

class View extends Version
{
    public function embargo()
    {
        parent::embargo();

        return ViewFacade::make("$this->viewPrefix.embargo", [
            'version' => $this->oldVersion,
        ]);
    }

    public function template(Template\Manager $manager)
    {
        parent::template($manager);

        $manager->findAndInstallNewTemplates();
        $templates = $manager->getValidTemplates();

        return ViewFacade::make("$this->viewPrefix.template", [
            'template_id' => $this->oldVersion->getTemplateId(),
            'templates' => $templates
        ]);
    }
}
