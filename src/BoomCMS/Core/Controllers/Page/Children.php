<?php

namespace BoomCMS\Core\Controllers\Page;

class Children extends Boom\Controller
{
    public function action_json()
    {
        $parent = \Boom\Page\Factory::byId($this->request->post('parent'));

        $pages = $this->_get_child_pages($parent);
        $json = $this->_format_pages_as_json($pages);

        $this->response
            ->headers('Content-Type', static::JSON_RESPONSE_MIME)
            ->body(json_encode($json));
    }

    protected function _get_child_pages($parent)
    {
        $finder = new \Boom\Page\Finder();

        return $finder
            ->addFilter(new \Boom\Page\Finder\Filter\ParentPage($parent))
            ->findAll();
    }

    protected function _format_pages_as_json($pages)
    {
        $json_pages = [];

        foreach ($pages as $page) {
            $json_pages[] = [
                'id'            =>    $page->getId(),
                'title'            =>    $page->getTitle(),
                'url'            =>    (string) $page->url(),
                'visible'        =>    (int) $page->isVisible(),
                'has_children'    =>    (int) $page->hasChildren(),
            ];
        }

        return $json_pages;
    }
}
