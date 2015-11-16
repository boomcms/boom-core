<?php

namespace BoomCMS\Tests\Support;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Asset\Provider as Asset;
use BoomCMS\Core\Chunk\Provider as Chunk;
use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\Group\Provider as Group;
use BoomCMS\Core\Page\Provider as Page;
use BoomCMS\Core\Tag\Provider as Tag;
use BoomCMS\Core\URL\Provider as URL;
use BoomCMS\Support\Facades;
use BoomCMS\Tests\AbstractTestCase;

class FacadesTest extends AbstractTestCase
{
    protected $facades = [
        Facades\Auth::class     => Auth::class,
        Facades\Asset::class    => Asset::class,
        Facades\Chunk::class    => Chunk::class,
        Facades\Editor::class   => Editor::class,
        Facades\Group::class    => Group::class,
        Facades\Page::class     => Page::class,
        Facades\Tag::class      => Tag::class,
        Facades\URL::class      => URL::class,
    ];

    public function testFacadesProvideAccessToExpectedClasses()
    {
        foreach ($this->facades as $facade => $class) {
            $this->assertInstanceOf($class, $facade::getFacadeRoot());
        }
    }
}