<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Settings;

use BoomCMS\Core\Page;

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

    /**
	 * ** View the child page settings.**
	 *
	 */
    public function children()
    {
        // Call the parent function to check permissions.
        parent::children();

        $childOrderingPolicy = $this->page->getChildOrderingPolicy();

        $manager = new \Boom\Template\Manager();
        $manager->createNew();
        $templates = $manager->getValidTemplates();

        // Create the main view with the basic settings
        return ViewFacade::make("$this->viewPrefix/children", [
            'default_child_template'    =>    $this->page->getDefaultChildTemplateId(),
            'templates' => $templates,
            'child_order_column'        =>    $childOrderingPolicy->getColumn(),
            'child_order_direction'    =>    $childOrderingPolicy->getDirection(),
            'allowAdvanced'        =>    $this->allowAdvanced,
        ]);

        // If we're showing the advanced settings then set the neccessary variables.
        if ($this->allowAdvanced) {
            // Add the view for the advanced settings to the main view.
            $this->template->set([
                'default_grandchild_template'    => ($this->page->getGrandchildTemplateId() != 0) ? $this->page->getGrandchildTemplateId() : $this->page->getTemplateId(),
                'page'                    => $this->page,
            ]);
        }
    }

    public function feature()
    {
        parent::feature();

        return ViewFacade::make("$this->viewPrefix/feature", [
            'featureImageId' => $this->page->getFeatureImageId(),
        ]);
    }

    /**
	 * ** View the page navigation settings.**
	 *
	 */
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
        parent::search();

        return ViewFacade::make("$this->viewPrefix/search", [
            'allowAdvanced' => $this->allowAdvanced,
            'page' => $this->page,
        ]);
    }

    public function sort_children()
    {
        parent::children();

        $finder = new PageFinder();

        $children = $finder
            ->addFilter(new PageFinder\Filter\ParentPage($this->page))
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
