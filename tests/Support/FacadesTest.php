<?php

namespace BoomCMS\Tests\Support;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Chunk\Provider as Chunk;
use BoomCMS\Core\Editor\Editor;
use BoomCMS\Repositories;
use BoomCMS\Support\Facades;
use BoomCMS\Tests\AbstractTestCase;

class FacadesTest extends AbstractTestCase
{
    protected $facades = [
        Facades\Auth::class     => Auth::class,
        Facades\Asset::class    => Repositories\Asset::class,
        Facades\Chunk::class    => Chunk::class,
        Facades\Editor::class   => Editor::class,
        Facades\Group::class    => Repositories\Group::class,
        Facades\Page::class     => Repositories\Page::class,
        Facades\Tag::class      => Repositories\Tag::class,
        Facades\URL::class      => Repositories\URL::class,
    ];

    public function testFacadesProvideAccessToExpectedClasses()
    {
        foreach ($this->facades as $facade => $class) {
            $this->assertInstanceOf($class, $facade::getFacadeRoot());
        }
    }
}