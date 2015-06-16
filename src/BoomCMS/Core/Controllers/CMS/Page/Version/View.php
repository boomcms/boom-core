<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Version;

use BoomCMS\Core\Template;
use Illuminate\Support\Facades\View as ViewFacade;

class View extends Version
{
    public function embargo()
    {
        // Call the parent function to check permissions.
        parent::action_embargo();

        return ViewFacade::make("$this->viewPrefix.embargo", [
            'version' => $this->oldVersion,
        ]);
    }

    public function template(Template\Manager $manager)
    {
        parent::action_template($manager);

        $manager->createNew();
        $templates = $manager->getValidTemplates();

        return ViewFacade::make("$this->viewPrefix.template", [
            'template_id' => $this->oldVersion->getTemplateId(),
            'templates' => $templates
        ]);
    }
}
