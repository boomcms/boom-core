<?php

namespace Boom\Page;

use \Boom\Page\Page as Page;
use \Boom\Person as Person;

class Creator
{
    /**
	 *
	 * @var Person\Person
	 */
    protected $_creator;

    /**
	 *
	 * @var \Model_Page
	 */
    protected $_parent;

    protected $_templateId;
    protected $_title = 'Untitled';

    public function __construct(Page $parent, Person\Person $creator)
    {
        $this->_parent = $parent;
        $this->_creator = $creator;
    }

    protected function _createPage()
    {
        $model = new \Model_Page();

        return $model
            ->values([
                'visible_in_nav'                =>    $this->_parent->childrenAreVisibleInNav(),
                'visible_in_nav_cms'            =>    $this->_parent->childrenAreVisibleInCmsNav(),
                'children_visible_in_nav'        =>    $this->_parent->childrenAreVisibleInNav(),
                'children_visible_in_nav_cms'    =>    $this->_parent->childrenAreVisibleInCmsNav(),
                'visible_from'                =>    time(),
                'created_by'                =>    $this->_creator->getId(),
            ])
            ->create();
    }

    protected function _createVersion(\Model_Page $page)
    {
        return \ORM::factory('Page_Version')
            ->values([
                'edited_by'    =>    $this->_creator->getId(),
                'page_id'        =>    $page->id,
                'template_id'    =>    $this->_getTemplateId(),
                'title'            =>    $this->_title,
                'published' => true,
                'embargoed_until' => time(),
            ])
            ->create();
    }

    public function execute()
    {
        \Database::instance()->begin();

        $page = $this->_createPage();
        $this->_createVersion($page);
        $this->_insertIntoTree($page);

        \Database::instance()->commit();

        return new Page($page);
    }

    protected function _insertIntoTree(\Model_Page $page)
    {
        $page->mptt->id = $page->id;
        $page->mptt->insert_as_last_child($this->_parent->getMptt());
    }

    protected function _getTemplateId()
    {
        if ($this->_templateId) {
            return $this->_templateId;
        }

        return $this->_parent->getDefaultChildTemplateId();
    }

    public function setTemplateId($template_id)
    {
        $template_id && $this->_templateId = $template_id;

        return $this;
    }

    public function setTitle($title)
    {
        $title && $this->_title = $title;

        return $this;
    }
}
