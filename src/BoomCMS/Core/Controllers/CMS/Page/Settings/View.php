<?php

namespace BoomCMS\Core\Controllers\CMS\Page\Settings;

use BoomCMS\Core\Template as Template;
use BoomCMS\Core\Page\Finder as PageFinder;


class View extends Settings
{

    public function admin()
    {
        parent::action_admin();

        return View::make("$this->viewPrefix/admin", [
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
        parent::action_children();

        $childOrderingPolicy = $this->page->getChildOrderingPolicy();

        $manager = new \Boom\Template\Manager();
        $manager->createNew();
        $templates = $manager->getValidTemplates();

        // Create the main view with the basic settings
        return View::make("$this->viewPrefix/children", [
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
        parent::action_feature();

        return View::make("$this->viewPrefix/feature", [
            'feature_image_id' => $this->page->getFeatureImageId(),
        ]);
    }

    /**
	 * ** View the page navigation settings.**
	 *
	 */
    public function navigation()
    {
        parent::action_navigation();

        return View::make("$this->viewPrefix/navigation", [
            'page' => $this->page,
            'allowAdvanced' => $this->allowAdvanced,
        ]);
    }

    public function search()
    {
        parent::action_search();

        return View::make("$this->viewPrefix/search", [
            'allowAdvanced' => $this->allowAdvanced,
            'page' => $this->page,
        ]);
    }

    public function sort_children()
    {
        parent::action_children();

        $finder = new PageFinder();

        $children = $finder
            ->addFilter(new PageFinder\Filter\ParentPage($this->page))
            ->setLimit(50)
            ->findAll();

        return View::make("$this->viewPrefix/sort_children", [
            'children' => $children
        ]);
    }

    public function visibility()
    {
        parent::action_visibility();

        return View::make("$this->viewPrefix/visibility", [
            'page' => $this->page,
        ]);
    }
}
