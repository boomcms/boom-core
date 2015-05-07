<?php

namespace BoomCMS\Core\Controllers\Page;

use BoomCMS\Core\Chunk;
use \ORM;
use \View;

class Html extends \Boom\Controller\Page
{
    /**
     *
     * @var array
     */
    private $chunks;

    public function before()
    {
        parent::before();

        $this->chunks = $this->loadChunks($this->template->getChunks());

        $this->_bindViewGlobals();
    }

    public function after()
    {
        if ($this->auth->isLoggedIn()) {
            $content = $this->editor->insert( (string) $this->response->body(), $this->page->getId());
            $this->response->body($content);
        }
    }

    protected function _bindViewGlobals()
    {
        View::bind_global('auth', $this->auth);
        View::bind_global('chunks', $this->chunks);
        View::bind_global('editor', $this->editor);
        View::bind_global('page', $this->page);
        View::bind_global('request', $this->request);
    }

    protected function loadChunks(array $chunks)
    {
        foreach ($chunks as $type => $slotnames) {
            $model = ucfirst($type);
            $class = "\Boom\Chunk\\" . $model;

            $models = Chunk::find($type, $slotnames, $this->page->getCurrentVersion());

            $found = [];
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
