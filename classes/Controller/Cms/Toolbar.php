<?php

class Controller_Cms_Toolbar extends Boom\Controller
{
    /**
	 *
	 * @var View
	 */
    protected $_toolbar;

    protected $viewDirectory = 'boom/toolbars';

    public function action_text()
    {
        $this->_toolbar = new \Boom\TextEditorToolbar($this->request->query('mode'));
        $this->_toolbar->render();
    }

    public function after()
    {
        $this->response->body($this->_toolbar);

        parent::after();
    }
}
