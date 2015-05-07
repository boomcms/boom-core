<?php

class Controller_Cms_Page_Version_View extends Controller_Cms_Page_Version
{
    public function embargo()
    {
        // Call the parent function to check permissions.
        parent::action_embargo();

        return View::make("$this->viewPrefix/embargo", [
            'version'    =>    $this->old_version,
        ]);
    }

    public function template()
    {
        parent::action_template();

        $manager = new \Boom\Template\Manager();
        $manager->createNew();
        $templates = $manager->getValidTemplates();

        return View::make("$this->viewPrefix/template", [
            'template_id'    =>    $this->old_version->template_id,
            'templates'    =>     $templates
        ]);
    }
}
