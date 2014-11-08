<?php

namespace Boom\Page;

abstract class Factory
{
    public static function byId($id)
    {
        return new Page(new \Model_Page(['id' => $id, 'deleted' => false]));
    }

    public static function byInternalName($name)
    {
        return new Page(new \Model_Page(['internal_name' => $name, 'deleted' => false]));
    }

    public static function byPrimaryUri($uri)
    {
        return new Page(new \Model_Page(['primary_uri' => $uri, 'deleted' => false]));
    }

    public static function byUri($uri)
    {
        $finder = new Finder();

        return $finder
            ->addFilter(new Finder\Filter\Uri($uri))
            ->find();
    }
}
