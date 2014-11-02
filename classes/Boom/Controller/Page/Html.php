<?php

namespace Boom\Controller\Page;

use \Boom\Chunk as Chunk;
use \ORM as ORM;
use \View as View;

class Html extends \Boom\Controller\Page
{
    protected $_chunks = array();

    /**
	 *
	 * @var View
	 */
    public $template;

    public function before()
    {
        parent::before();

        $this->template = $this->page->getTemplate()->getView();
        $this->_chunks = $this->_loadChunks($this->_chunks);

        $this->_bindViewGlobals();
    }

    public function action_show() {}

    public function after()
    {
        if ($this->auth->isLoggedIn()) {
            $content = $this->editor->insert( (string) $this->template, $this->page->getId());
        } else {
            $content = (string) $this->template;
        }

        $this->response->body($content);
    }

    protected function _bindViewGlobals()
    {
        View::bind_global('auth', $this->auth);
        View::bind_global('chunks', $this->_chunks);
        View::bind_global('editor', $this->editor);
        View::bind_global('page', $this->page);
        View::bind_global('request', $this->request);
    }

    protected function _loadChunks(array $chunks)
    {
        foreach ($chunks as $type => $slotnames) {
            $model = ucfirst($type);
            $class = "\Boom\Chunk\\" . $model;

            $models = Chunk::find($type, $slotnames, $this->page->getCurrentVersion());

            $found = array();
            foreach ($models as $m) {
                $found[] = $m->slotname;
                $chunks[$type][$m->slotname] = new $class($this->page, $m, $m->slotname);
            }

            $not_found = array_diff($slotnames, $found);

            foreach ($not_found as $slotname) {
                $chunks[$type][$slotname] = new $class($this->page, ORM::factory("Chunk_$model"), $slotname);
            }
        }

        return $chunks;
    }
}
