<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Settings;

use BoomCMS\Core\Page\Finder as PageFinder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View as ViewFacade;

class View extends Settings
{
    public function admin()
    {
        parent::admin();

        return ViewFacade::make("$this->viewPrefix/admin", [
            'page' => $this->page,
        ]);
    }

    public function children()
    {
        parent::children();

        $childOrderingPolicy = $this->page->getChildOrderingPolicy();

        $manager = App::getFacadeApplication()['boomcms.template.manager'];
        $templates = $manager->getValidTemplates();

        // Create the main view with the basic settings
        $v = ViewFacade::make("$this->viewPrefix/children", [
            'default_child_template' => $this->page->getDefaultChildTemplateId(),
            'templates' => $templates,
            'child_order_column' => $childOrderingPolicy->getColumn(),
            'child_order_direction' => $childOrderingPolicy->getDirection(),
            'allowAdvanced' => $this->allowAdvanced,
        ]);

        // If we're showing the advanced settings then set the neccessary variables.
        if ($this->allowAdvanced) {
            // Add the view for the advanced settings to the main view.
            $v->default_grandchild_template = ($this->page->getGrandchildTemplateId() != 0) ? $this->page->getGrandchildTemplateId() : $this->page->getTemplateId();
            $v->page = $this->page;
        }

        return $v;
    }

    public function feature()
    {
        parent::feature();

        return ViewFacade::make("$this->viewPrefix/feature", [
            'featureImageId' => $this->page->getFeatureImageId(),
        ]);
    }

    public function navigation()
    {
        parent::navigation();

        return ViewFacade::make("$this->viewPrefix/navigation", [
            'page' => $this->page,
            'allowAdvanced' => $this->allowAdvanced,
        ]);
    }

    public function search()
    {
        parent::search($this->page);

        return ViewFacade::make("$this->viewPrefix/search", [
            'allowAdvanced' => $this->allowAdvanced,
            'page' => $this->page,
        ]);
    }

    public function sort_children()
    {
        parent::children();

        $finder = new PageFinder\Finder();

        $children = $finder
            ->addFilter(new PageFinder\ParentPage($this->page))
            ->setLimit(50)
            ->findAll();

        return ViewFacade::make("$this->viewPrefix/sort_children", [
            'children' => $children
        ]);
    }

    public function visibility()
    {
        parent::visibility();

        return ViewFacade::make("$this->viewPrefix/visibility", [
            'page' => $this->page,
        ]);
    }
}
