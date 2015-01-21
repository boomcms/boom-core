<?php

namespace Boom\Controller;

class Page extends \Boom\Controller
{
    /**
     *
     * @var Page\Page
     */
    public $page;

    /**
     *
     * @var string
     */
    protected $responseBody;

    /**
     *
     * @var Template
     */
    public $template;

    protected $_save_last_url = true;

    public function before()
    {
        parent::before();

        $this->page = $this->request->param('page');
        $this->template = $this->page->getTemplate();
        $this->editable = $this->_page_should_be_editable();

        if ( ! $this->_page_isnt_visible_to_current_user()) {
            throw new HTTP_Exception_404();
        }
    }

    public function action_show()
    {
        $method = 'as' . ucfirst(strtolower($this->request->param('format')));

        $this->responseBody = $this->template->$method($this->page, $this->request);
    }

    protected function _page_should_be_editable()
    {
        return ($this->editor->isEnabled() && ($this->page->wasCreatedBy($this->person) || $this->auth->loggedIn('edit_page', $this->page)));
    }

    protected function _page_isnt_visible_to_current_user()
    {
        // If the page shouldn't be editable then check that it's visible.
        if (! $this->editable) {
            if ($this->request->is_external() && ( ! $this->page->isVisible() && ! $this->editor->hasState(\Boom\Editor::PREVIEW))) {
                return false;
            }
        }

        return true;
    }
}
