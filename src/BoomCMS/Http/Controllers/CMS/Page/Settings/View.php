<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Settings;

use Illuminate\Support\Facades\App;

class View extends Settings
{
    public function admin()
    {
        parent::admin();

        return view("$this->viewPrefix/admin", [
            'page' => $this->page,
        ]);
    }

    public function children()
    {
        parent::children();

        list($orderCol, $orderDirection) = $this->page->getChildOrderingPolicy();

        $manager = App::getFacadeApplication()['boomcms.template.manager'];
        $templates = $manager->getValidTemplates();

        // Create the main view with the basic settings
        $v = ViewFacade::make("$this->viewPrefix/children", [
            'default_child_template' => $this->page->getDefaultChildTemplateId(),
            'templates'              => $templates,
            'child_order_column'     => $orderCol,
            'child_order_direction'  => $orderDirection,
            'allowAdvanced'          => $this->allowAdvanced,
            'page'                   => $this->page,
        ]);

        // If we're showing the advanced settings then set the neccessary variables.
        if ($this->allowAdvanced) {
            // Add the view for the advanced settings to the main view.
            $v->default_grandchild_template = ($this->page->getGrandchildTemplateId() != 0) ? $this->page->getGrandchildTemplateId() : $this->page->getTemplateId();
        }

        return $v;
    }

    public function delete()
    {
        parent::delete();

        return view($this->viewPrefix.'/delete', [
            'children' => $this->page->countChildren(),
            'page'     => $this->page,
        ]);
    }

    public function feature()
    {
        parent::feature();

        return view("$this->viewPrefix/feature", [
            'featureImageId' => $this->page->getFeatureImageId(),
        ]);
    }

    public function index()
    {
        return view("$this->viewPrefix/index", [
            'page' => $this->page,
        ]);
    }

    public function navigation()
    {
        parent::navigation();

        return view("$this->viewPrefix/navigation", [
            'page'          => $this->page,
            'allowAdvanced' => $this->allowAdvanced,
        ]);
    }

    public function search()
    {
        parent::search($this->page);

        return view("$this->viewPrefix/search", [
            'allowAdvanced' => $this->allowAdvanced,
            'page'          => $this->page,
        ]);
    }

    public function visibility()
    {
        parent::visibility();

        return view("$this->viewPrefix/visibility", [
            'page' => $this->page,
        ]);
    }
}
