<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Lang;
use Mockery as m;

abstract class AbstractChangeTestCase extends AbstractTestCase
{
    protected $class;
    protected $className;

    public function setUp()
    {
        parent::setUp();

        $this->class = new $this->className(m::mock(PageVersion::class), m::mock(PageVersion::class));
    }

    public function testLangKeysExist()
    {
        $this->assertTrue(Lang::has($this->class->getSummaryKey()));
    }
}
