<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Settings;

use BoomCMS\Core\Page;
use BoomCMS\Core\Template;

use Illuminate\Support\Facades\View as ViewFacade;

class View extends Settings
{
    public function admin(Page\Page $page)
    {
        parent::admin($page);

        return ViewFacade::make("$this->viewPrefix/admin", [
            'page' => $page,
        ]);
    }

    public function children(Page\Page $page)
    {
        parent::children($page);

        $childOrderingPolicy = $page->getChildOrderingPolicy();

        $manager = new Template\Manager();
        $templates = $manager->getValidTemplates();

        // Create the main view with the basic settings
        $v = ViewFacade::make("$this->viewPrefix/children", [
            'default_child_template' => $page->getDefaultChildTemplateId(),
            'templates' => $templates,
            'child_order_column' => $childOrderingPolicy->getColumn(),
            'child_order_direction' => $childOrderingPolicy->getDirection(),
            'allowAdvanced' => $this->allowAdvanced,
        ]);

        // If we're showing the advanced settings then set the neccessary variables.
        if ($this->allowAdvanced) {
            // Add the view for the advanced settings to the main view.
            $v->default_grandchild_template = ($page->getGrandchildTemplateId() != 0) ? $page->getGrandchildTemplateId() : $page->getTemplateId();
            $v->page = $page;
        }

        return $v;
    }

    public function feature(Page\Page $page)
    {
        parent::feature($page);

        return ViewFacade::make("$this->viewPrefix/feature", [
            'featureImageId' => $page->getFeatureImageId(),
        ]);
    }

    public function navigation(Page\Page $page)
    {
        parent::navigation($page);

        return ViewFacade::make("$this->viewPrefix/navigation", [
            'page' => $page,
            'allowAdvanced' => $this->allowAdvanced,
        ]);
    }

    public function search(Page\Page $page)
    {
        parent::search($page);

        return ViewFacade::make("$this->viewPrefix/search", [
            'allowAdvanced' => $this->allowAdvanced,
            'page' => $page,
        ]);
    }

    public function sort_children(Page\Page $page)
    {
        parent::children($page);

        $finder = new PageFinder();

        $children = $finder
            ->addFilter(new PageFinder\Filter\ParentPage($page))
            ->setLimit(50)
            ->findAll();

        return ViewFacade::make("$this->viewPrefix/sort_children", [
            'children' => $children
        ]);
    }

    public function visibility(Page\Page $page)
    {
        parent::visibility($page);

        return ViewFacade::make("$this->viewPrefix/visibility", [
            'page' => $page,
        ]);
    }
}
