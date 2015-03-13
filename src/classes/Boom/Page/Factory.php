<?php

namespace Boom\Page;

class Provider
{
    public function findById($id)
    {
        return new Page(new \Model_Page(['id' => $id, 'deleted' => false]));
    }

    public function findByInternalName($name)
    {
        return new Page(new \Model_Page(['internal_name' => $name, 'deleted' => false]));
    }

    public function findByPrimaryUri($uri)
    {
        return new Page(new \Model_Page(['primary_uri' => $uri, 'deleted' => false]));
    }

    public function findByUri($uri)
    {
        $finder = new Finder();

        return $finder
            ->addFilter(new Finder\Filter\Uri($uri))
            ->find();
    }
}
